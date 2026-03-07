<?php

declare(strict_types=1);

use App\Models\User;

beforeEach(function () {
    $user = User::factory()->create();
    $this->actingAs($user);
});

it('should be able to create a room', function () {
    $response = $this->post(route('room.store'), [
        'code'      => 'TESTE',
        'icon'      => 'lucide:fish',
        'password'  => null,
        'theme'     => 'underwater',
        'type'      => 'public',
        'max_users' => '2',
        'game_mode' => 'default',
    ]);

    $response->assertRedirect(route('room.index'));

    $this->assertDatabaseHas('rooms', [
        'code'      => 'TESTE',
        'icon'      => 'lucide:fish',
        'password'  => null,
        'theme'     => 'underwater',
        'type'      => 'public',
        'max_users' => '2',
        'game_mode' => 'default',
        'status'    => 'waiting',
        'user_id'   => auth()->id(),
    ]);
});

describe('Room Validations', function () {
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
});
