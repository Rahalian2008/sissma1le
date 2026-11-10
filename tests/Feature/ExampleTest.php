<?php

test('unauthenticated users are redirected to login when accessing dashboard', function () {
    $response = $this->get('/dashboard');

    $response->assertRedirect('/login');
});

test('public school website returns a successful response', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('SMAN 1 Lengkong');
});

test('login page returns a successful response', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});
