<?php

it('should be able to authenticate as a guest with a valid nickname', function () {
    $response = $this->post('/guest-login', [
        'nickname' => 'ValidGuestNickname',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard'));
});

it('should be able to save the guest user in the database with the is_guest flag', function () {
    $this->post('/guest-login', [
        'nickname' => 'ValidGuestNickname',
    ]);

    $this->assertDatabaseHas('users', [
        'nickname' => 'ValidGuestNickname',
        'email' => null,
        'is_guest' => true,
    ]);
});

it('should not be able to authenticate as a guest without a nickname', function () {
    $response = $this->post('/guest-login', []);

    $this->assertGuest();
    $response->assertSessionHasErrors('nickname');
});

it('should not allow a nickname that is already taken', function () {
    \App\Models\User::factory()->create(['nickname' => 'GuestTakenName']);

    $response = $this->post('/guest-login', [
        'nickname' => 'GuestTakenName',
    ]);

    $response->assertSessionHasErrors('nickname');
});