<?php

use App\Models\User;

test('user reported screenshot routes are 100% accessible with 200 OK', function () {
    $admin = User::where('role', 'admin')->first() ?? User::where('role', 'super_admin')->first();
    expect($admin)->not->toBeNull();

    // 1. /library (Screenshot 2: Column not found book_type)
    $response = $this->actingAs($admin)->get('/library');
    $response->assertStatus(200);

    // 2. /tu-students/demographics (Screenshot 3: Column not found entry_path)
    $response = $this->actingAs($admin)->get('/tu-students/demographics');
    $response->assertStatus(200);

    // 3. /tu-students (Screenshot 4: Column not found current_rombel)
    $response = $this->actingAs($admin)->get('/tu-students');
    $response->assertStatus(200);

    // 4. /alumni (Screenshot 5: Column not found status)
    $response = $this->actingAs($admin)->get('/alumni');
    $response->assertStatus(200);

    // 5. /students/learning-agendas (Screenshot 6: Column not found current_rombel)
    $response = $this->actingAs($admin)->get('/students/learning-agendas');
    $response->assertStatus(200);

    // 6. / (Screenshot 1: Website home)
    $response = $this->get('/');
    $response->assertStatus(200);
});
