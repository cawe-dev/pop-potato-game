<?php

declare(strict_types=1);

use App\Models\User;

beforeEach(function () {
    $user = User::factory()->create();
    $this->actingAs($user);
});

describe('Create Room', function () {

    it('should be able to create a public room', function () {
        $payload = [
            'code'      => 'TESTE',
            'icon'      => 'lucide:fish',
            'password'  => null,
            'theme'     => 'underwater',
            'type'      => 'public',
            'max_users' => '2',
            'game_mode' => 'default',
        ];

        $response = $this->post(route('room.store'), $payload);

        $response->assertRedirect(route('room.index'));

        $this->assertDatabaseHas('rooms', $payload);
    });

    it('should be able to create a private room', function () {
        $payload = [
            'code'      => 'TESTE',
            'icon'      => 'lucide:fish',
            'password'  => '1234',
            'theme'     => 'underwater',
            'type'      => 'private',
            'max_users' => '2',
            'game_mode' => 'default',
        ];

        $response = $this->post(route('room.store'), $payload);

        $response->assertRedirect(route('room.index'));

        $this->assertDatabaseHas('rooms', $payload);
    });
});

describe('Create Room Validations', function () {
    it('should not be able to create a room without required fields', function (string $field) {
        $payload = [
            'code'      => 'TESTE',
            'icon'      => 'lucide:fish',
            'password'  => null,
            'theme'     => 'underwater',
            'type'      => 'public',
            'max_users' => '2',
            'game_mode' => 'default',
        ];
        unset($payload[$field]);

        $response = $this->post(route('room.store'), $payload);

        $response->assertStatus(302)
            ->assertInvalid($field);
    })->with([
        'code',
        'theme',
        'game_mode',
        'type',
    ]);

    it('should not be able to create a room with user define status', function () {
        $response = $this->post(route('room.store'), [
            'code'      => 'TESTE',
            'icon'      => 'lucide:fish',
            'password'  => null,
            'theme'     => 'underwater',
            'type'      => 'public',
            'max_users' => '2',
            'game_mode' => 'default',
            'status'    => 'playing',
        ]);

        $response->assertRedirect(route('room.index'));

        $this->assertDatabaseHas('rooms', [
            'status' => 'waiting',
        ]);
    });

    it('should not be able to create a room with only one max_users', function () {
        $response = $this->post(route('room.store'), [
            'code'      => 'TESTE',
            'icon'      => 'lucide:fish',
            'password'  => null,
            'theme'     => 'underwater',
            'type'      => 'public',
            'game_mode' => 'default',
            'max_users' => '1',
        ]);

        $response->assertStatus(302)
            ->assertInvalid('max_users');
    });

    it('should not be able to create a room without password', function () {
        $response = $this->post(route('room.store'), [
            'code'      => 'TESTE',
            'icon'      => 'lucide:fish',
            'password'  => null,
            'theme'     => 'underwater',
            'type'      => 'private',
            'game_mode' => 'default',
            'max_users' => '2',
        ]);

        $response->assertStatus(302)
            ->assertInvalid('password');
    });
});
