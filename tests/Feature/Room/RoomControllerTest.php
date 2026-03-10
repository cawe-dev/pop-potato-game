<?php

declare(strict_types=1);

use App\Enums\Room\RoomGameMode;
use App\Enums\Room\RoomTheme;
use App\Models\Room;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;

beforeEach(function () {
    $user = User::factory()->create();
    $this->actingAs($user);
});

describe('Create Room', function () {

    it('should be able to create a public room', function () {
        $payload = [
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

    it('should be able to create a room as a guest user', function () {
        $guestUser = User::factory()->create([
            'nickname' => 'GuestUser',
            'email'    => null,
            'is_guest' => true,
        ]);
        $this->actingAs($guestUser);

        $payload = [
            'icon'      => 'lucide:fish',
            'password'  => '1234',
            'theme'     => 'underwater',
            'type'      => 'private',
            'max_users' => '2',
            'game_mode' => 'default',
        ];

        $response = $this->post(route('room.store'), $payload);

        $response->assertRedirect(route('room.index'));

        $this->assertDatabaseHas('rooms', array_merge($payload, ['user_id' => $guestUser->id]));
    });

    describe('Validations', function () {
        it('should not be able to create a room without required fields', function (string $field) {
            $payload = [
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
            'theme',
            'game_mode',
            'type',
        ]);

        it('should not be able to create a room with more fields than expected', function (string $field, string $value) {
            $payload = [
                'icon'      => 'lucide:fish',
                'password'  => null,
                'theme'     => 'underwater',
                'type'      => 'public',
                'max_users' => '2',
                'game_mode' => 'default',
                $field      => $value,
            ];

            $expectPayload = [
                'icon'      => 'lucide:fish',
                'password'  => null,
                'theme'     => 'underwater',
                'type'      => 'public',
                'max_users' => '2',
                'game_mode' => 'default',
            ];

            $response = $this->post(route('room.store'), $payload);

            $response->assertRedirect(route('room.index'));

            $this->assertDatabaseHas('rooms', array_merge($expectPayload, ['user_id' => auth()->id()]));
            $this->assertDatabaseMissing('rooms', [$field => $value]);
        })->with([
            ['other_field', 'ABC2'],
            ['user_id', '4'],
            ['malicious_field', '8'],
        ]);

        it('should not be able to create a room with user define status', function () {
            $payload = [
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

describe('Read Room', function () {
    $publicRoom = null;
    $privateRoom = null;

    beforeEach(function () use (&$publicRoom, &$privateRoom) {
        $publicRoom = Room::factory(10)->create(['user_id' => auth()->id(), 'type' => 'public']);
        $privateRoom = Room::factory(10)->create(['user_id' => auth()->id(), 'type' => 'private']);
    });

    it('should be able to list all rooms with pagination', function () use (&$publicRoom) {
        $response = $this->get(route('room.index'));

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->has('data', 15)
                ->has('links')
                ->where('current_page', 1)
                ->where('per_page', 15)
                ->has(
                    'data.0',
                    fn ($json) => $json->hasAll(collect($publicRoom[0]->getAttributes())->forget('password')->keys()->toArray())
                        ->where('id', $publicRoom[0]->id)
                        ->where('code', $publicRoom[0]->code)
                        ->where('type', 'public')
                        ->etc()
                )->etc()
        );
    });

    it('should not be possible to list non-exist specific rooms by id', function () use (&$publicRoom) {
        $response = $this->get(route('room.show', 9999999999));
        $response->assertNotFound();
    });

    it('should be possible to list specific  rooms by id', function () use (&$publicRoom) {
        $response = $this->get(route('room.show', $publicRoom[0]->id));
        $response->assertOk()
            ->assertJsonFragment([
                'id' => $publicRoom[0]->id,
            ]);
    });

    it('ensure that the password not return with room', function () use (&$publicRoom) {
        $response = $this->get(route('room.show', $publicRoom[0]->id));
        $response->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json->missing('password')->etc());
    });

    describe('Filter Validation', function () {
        it('filters rooms by simple fields', function ($field, $value) {
            Room::factory()->create([$field => $value]);

            $this->get(route('room.index', [$field => $value]))
                ->assertOk()
                ->assertJsonPath("data.0.$field", $value);
        })->with([
            ['code', 'ABC2'],
            ['type', 'public'],
        ]);

        it('should filter rooms by theme', function () {
            Room::factory()->create(['theme' => RoomTheme::UNDERWATER, 'type' => 'public']);
            Room::factory()->create(['theme' => RoomTheme::FILMS, 'type' => 'public']);

            $response = $this->get(route('room.index', ['theme' => [RoomTheme::UNDERWATER->value]]));

            $response->assertOk()
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.theme', RoomTheme::UNDERWATER->value);
        });

        it('should filter rooms by game mode', function () {
            Room::factory()->create(['game_mode' => RoomGameMode::HARD_POTATO]);

            $response = $this->get(route('room.index', ['game_mode' => [RoomGameMode::HARD_POTATO->value]]));

            $response->assertOk()
                ->assertJsonPath('data.0.game_mode', RoomGameMode::HARD_POTATO->value);
        });

        it('should not be able to list rooms if the filters are invalid', function (string $field, $value) {
            $response = $this->get(route('room.index', [$field => $value]));

            $response->assertStatus(302);
            $response->isInvalid($field);
        })->with([
            'invalid code (too short)' => ['code', 'abc'],
            'invalid code (too long)'  => ['code', 'abcde'],
            'invalid type'             => ['type', 'not-a-valid-type'],
            'invalid game_mode'        => ['game_mode', 'game-mode-non-existent'],
            'invalid theme'            => ['theme', 'theme-non-existent'],
        ]);
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
