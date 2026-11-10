<?php

use App\Models\Role;
use App\Models\User;

test('role adm_web exists in database and grants student access only to news content', function () {
    $role = Role::where('name', 'adm_web')->first();
    expect($role)->not->toBeNull();
    expect($role->display_name)->toContain('Admin Web');

    // Create a student user with adm_web role
    $student = User::factory()->create([
        'role' => 'adm_web',
        'roles' => ['siswa', 'adm_web'],
    ]);

    expect($student->canManageWebsiteContent())->toBeTrue();
    expect($student->allowedWebsiteContentTabs())->toBe(['news']);
    expect($student->canAccessWebsiteContentTab('news'))->toBeTrue();
    expect($student->canAccessWebsiteContentTab('hero'))->toBeFalse();
    expect($student->canAccessWebsiteContentTab('headmaster'))->toBeFalse();
    expect($student->canAccessWebsiteContentTab('staff'))->toBeFalse();

    // Accessing website content page should succeed
    $response = $this->actingAs($student)->get(route('master.website_content'));
    $response->assertStatus(200);

    // Attempting to post to hero settings should be forbidden (403)
    $responseHero = $this->actingAs($student)->post(route('master.website_content.hero'), [
        'website_hero_headline' => 'Test Headline',
        'website_hero_subheadline' => 'Test Subheadline',
        'website_humas_phone' => '081234567890',
        'website_humas_message' => 'Test Message',
    ]);
    $responseHero->assertStatus(403);
});
