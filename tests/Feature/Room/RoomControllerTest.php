<?php

declare(strict_types=1);

use App\Models\Room;
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

        $this->assertDatabaseHas('rooms', array_merge($payload, ['user_id' => auth()->id()]));
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

        $this->assertDatabaseHas('rooms', array_merge($payload, ['user_id' => auth()->id()]));
    });

    describe('Validations', function () {
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
            $payload = [
                'code'      => 'TESTE',
                'icon'      => 'lucide:fish',
                'password'  => null,
                'theme'     => 'underwater',
                'type'      => 'public',
                'max_users' => '2',
                'game_mode' => 'default',
                'status'    => 'playing',
            ];

            $response = $this->post(route('room.store'), $payload);

            $response->assertRedirect(route('room.index'));

            $this->assertDatabaseHas('rooms', [
                'status' => 'waiting',
            ]);
        });

        it('should not be able to create a room with only one max_users', function () {
            $payload = [
                'code'      => 'TESTE',
                'icon'      => 'lucide:fish',
                'password'  => null,
                'theme'     => 'underwater',
                'type'      => 'public',
                'game_mode' => 'default',
                'max_users' => '1',
            ];

            $response = $this->post(route('room.store'), $payload);

            $response->assertStatus(302)
                ->assertInvalid('max_users');
        });

        it('should not be able to create a room without password', function () {
            $payload = [
                'code'      => 'TESTE',
                'icon'      => 'lucide:fish',
                'password'  => null,
                'theme'     => 'underwater',
                'type'      => 'private',
                'game_mode' => 'default',
                'max_users' => '2',
            ];

            $response = $this->post(route('room.store'), $payload);

            $response->assertStatus(302)
                ->assertInvalid('password');
        });

        it('ensure that the create status room to private require password', function () {
            $payload = [
                'icon'      => 'lucide:fish',
                'password'  => null,
                'theme'     => 'underwater',
                'type'      => 'private',
                'max_users' => '2',
                'game_mode' => 'default',
            ];

            $response = $this->post(route('room.store'), $payload);

            $response->assertStatus(302)
                ->assertInvalid('password');
        });

        it('ensure that the create status room to public require clean password', function () {
            $payload = [
                'icon'      => 'lucide:fish',
                'password'  => '1234',
                'theme'     => 'underwater',
                'type'      => 'public',
                'max_users' => '2',
                'game_mode' => 'default',
            ];

            $response = $this->post(route('room.store'), $payload);

            $response->assertStatus(302)
                ->assertInvalid('password');
        });
    });
});

describe('Update Room', function () {
    $publicRoom = null;
    $privateRoom = null;

    beforeEach(function () use (&$publicRoom, &$privateRoom) {
        $publicRoom = Room::factory()->create(['user_id' => auth()->id(), 'type' => 'public']);
        $privateRoom = Room::factory()->create(['user_id' => auth()->id(), 'type' => 'private']);
    });

    it('should be able to update a room if owner', function () use (&$publicRoom) {
        $payload = [
            'icon'      => 'lucide:fish',
            'password'  => null,
            'theme'     => 'underwater',
            'type'      => 'public',
            'max_users' => '2',
            'game_mode' => 'default',
        ];

        $response = $this->put(route('room.update', $publicRoom->id), $payload);

        $response->assertRedirect(route('room.index'));

        $this->assertDatabaseHas('rooms', $payload);
    });

    describe('Validations', function () use (&$publicRoom, &$privateRoom) {
        it('should not be able to update a room if not owner', function () use (&$publicRoom) {
            $userNotOwner = User::factory()->create();
            $this->actingAs($userNotOwner);

            $payload = [
                'icon'      => 'lucide:fish',
                'password'  => null,
                'theme'     => 'underwater',
                'type'      => 'public',
                'max_users' => '2',
                'game_mode' => 'default',
            ];

            $response = $this->put(route('room.update', $publicRoom->id), $payload);

            $response->assertStatus(403);
        });

        it('should not be able to minor update a room to max_users minor of 2', function () use (&$publicRoom) {
            $payload = [
                'icon'      => 'lucide:fish',
                'password'  => null,
                'theme'     => 'underwater',
                'type'      => 'public',
                'max_users' => '1',
                'game_mode' => 'default',
            ];

            $response = $this->put(route('room.update', $publicRoom->id), $payload);

            $response->assertStatus(302)
                ->assertInvalid('max_users');
        });

        it('should not be able to update a room code', function () use (&$publicRoom) {
            $payload = [
                'code'      => 'NEWCD',
                'icon'      => 'lucide:fish',
                'password'  => null,
                'theme'     => 'underwater',
                'type'      => 'public',
                'max_users' => '1',
                'game_mode' => 'default',
            ];

            $response = $this->put(route('room.update', $publicRoom->id), $payload);

            $response->assertStatus(302)
                ->assertInvalid('code');
        });

        it('ensure that the update status room to private require password', function () use (&$publicRoom) {
            $payload = [
                'icon'      => 'lucide:fish',
                'password'  => null,
                'theme'     => 'underwater',
                'type'      => 'private',
                'max_users' => '2',
                'game_mode' => 'default',
            ];

            $response = $this->put(route('room.update', $publicRoom->id), $payload);

            $response->assertStatus(302)
                ->assertInvalid('password');
        });

        it('ensure that the update status room to public require clean password', function () use (&$privateRoom) {
            $payload = [
                'icon'      => 'lucide:fish',
                'password'  => '1234',
                'theme'     => 'underwater',
                'type'      => 'public',
                'max_users' => '2',
                'game_mode' => 'default',
            ];

            $response = $this->put(route('room.update', $privateRoom->id), $payload);

            $response->assertStatus(302)
                ->assertInvalid('password');
        });

        it('ensure that the update room does not update the code', function () use (&$publicRoom) {
            $roomCode = $publicRoom->code;

            $payload = [
                'icon'      => 'lucide:fish',
                'password'  => null,
                'theme'     => 'underwater',
                'type'      => 'public',
                'max_users' => '2',
                'game_mode' => 'default',
            ];

            $this->put(route('room.update', $publicRoom->id), $payload);

            $this->assertDatabaseHas('rooms', array_merge($payload, [
                'user_id' => auth()->id(),
                'id'      => $publicRoom->id,
                'code'    => $roomCode,
            ]));
        });
    });
});
