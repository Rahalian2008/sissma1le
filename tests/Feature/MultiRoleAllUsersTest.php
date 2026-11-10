<?php

use App\Models\User;

test('all multi-role users can access dashboard and key modules without any 500 error', function () {
    $multiRoleUsers = User::all()->filter(fn ($u) => count($u->all_roles) > 1);

    expect($multiRoleUsers->count())->toBeGreaterThan(0);

    $routes = [
        route('dashboard'),
        route('profile'),
        route('attendance.index'),
        route('attendance.history'),
        route('achievements.index'),
        route('violations.index'),
        route('habits.journal'),
    ];

    foreach ($multiRoleUsers as $user) {
        // Test default state
        $response = $this->actingAs($user)->get(route('dashboard'));
        expect($response->status())->toBeIn([200, 302], "User {$user->name} (ID: {$user->id}, Roles: ".implode(',', $user->all_roles).') failed on dashboard');

        // Test with each role set as active_role
        foreach ($user->all_roles as $role) {
            session(['active_role' => $role]);

            $dashboardRes = $this->actingAs($user)->get(route('dashboard'));
            expect($dashboardRes->status())->toBeIn([200, 302], "User {$user->name} failed on dashboard with active_role {$role}");

            foreach ($routes as $url) {
                $res = $this->actingAs($user)->get($url);
                expect($res->status())->not->toBe(500, "User {$user->name} got 500 on {$url} with active_role {$role}");
            }
        }
    }
});
