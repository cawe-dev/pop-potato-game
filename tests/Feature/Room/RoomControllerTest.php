<?php

declare(strict_types=1);

use App\Actions\room\LeaveRoom;
use App\Enums\Room\RoomGameMode;
use App\Enums\Room\RoomStatus;
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
            'password'  => null,
            'theme'     => 'underwater',
            'type'      => 'public',
            'max_users' => '2',
            'game_mode' => 'default',
        ];

        $response = $this->post(route('rooms.store'), $payload);

        $response->assertRedirect(route('rooms.index'));

        $this->assertDatabaseHas('rooms', array_merge($payload, ['user_id' => auth()->id()]));
    });

    it('should be able to create a private room', function () {
        $payload = [
            'password'  => '1234',
            'theme'     => 'underwater',
            'type'      => 'private',
            'max_users' => '2',
            'game_mode' => 'default',
        ];

        $response = $this->post(route('rooms.store'), $payload);

        $response->assertRedirect(route('rooms.index'));

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
            'password'  => '1234',
            'theme'     => 'underwater',
            'type'      => 'private',
            'max_users' => '2',
            'game_mode' => 'default',
        ];

        $response = $this->post(route('rooms.store'), $payload);

        $response->assertRedirect(route('rooms.index'));

        $this->assertDatabaseHas('rooms', array_merge($payload, ['user_id' => $guestUser->id]));
    });

    describe('Validations', function () {
        it('should not be able to create a room without required fields', function (string $field) {
            $payload = [
                'password'  => null,
                'theme'     => 'underwater',
                'type'      => 'public',
                'max_users' => '2',
                'game_mode' => 'default',
            ];
            unset($payload[$field]);

            $response = $this->post(route('rooms.store'), $payload);

            $response->assertStatus(302)
                ->assertInvalid($field);
        })->with([
            'theme',
            'game_mode',
            'type',
        ]);

        it('should not be able to create a room with more fields than expected', function (string $field, string $value) {
            $payload = [
                'password'  => null,
                'theme'     => 'underwater',
                'type'      => 'public',
                'max_users' => '2',
                'game_mode' => 'default',
                $field      => $value,
            ];

            $expectPayload = [
                'password'  => null,
                'theme'     => 'underwater',
                'type'      => 'public',
                'max_users' => '2',
                'game_mode' => 'default',
            ];

            $response = $this->post(route('rooms.store'), $payload);

            $response->assertRedirect(route('rooms.index'));

            $this->assertDatabaseHas('rooms', array_merge($expectPayload, ['user_id' => auth()->id()]));
            $this->assertDatabaseMissing('rooms', [$field => $value]);
        })->with([
            ['other_field', 'ABC2'],
            ['user_id', '4'],
            ['malicious_field', '8'],
        ]);

        it('should not be able to create a room with user define status', function () {
            $payload = [
                'password'  => null,
                'theme'     => 'underwater',
                'type'      => 'public',
                'max_users' => '2',
                'game_mode' => 'default',
                'status'    => 'playing',
            ];

            $response = $this->post(route('rooms.store'), $payload);

            $response->assertRedirect(route('rooms.index'));

            $this->assertDatabaseHas('rooms', [
                'status' => 'waiting',
            ]);
        });

        it('should not be able to create a room with only one max_users', function () {
            $payload = [
                'password'  => null,
                'theme'     => 'underwater',
                'type'      => 'public',
                'game_mode' => 'default',
                'max_users' => '1',
            ];

            $response = $this->post(route('rooms.store'), $payload);

            $response->assertStatus(302)
                ->assertInvalid('max_users');
        });

        it('should not be able to create a room without password', function () {
            $payload = [
                'password'  => null,
                'theme'     => 'underwater',
                'type'      => 'private',
                'game_mode' => 'default',
                'max_users' => '2',
            ];

            $response = $this->post(route('rooms.store'), $payload);

            $response->assertStatus(302)
                ->assertInvalid('password');
        });

        it('ensure that the create status room to private require password', function () {
            $payload = [
                'password'  => null,
                'theme'     => 'underwater',
                'type'      => 'private',
                'max_users' => '2',
                'game_mode' => 'default',
            ];

            $response = $this->post(route('rooms.store'), $payload);

            $response->assertStatus(302)
                ->assertInvalid('password');
        });

        it('ensure that the create status room to public require clean password', function () {
            $payload = [
                'password'  => '1234',
                'theme'     => 'underwater',
                'type'      => 'public',
                'max_users' => '2',
                'game_mode' => 'default',
            ];

            $response = $this->post(route('rooms.store'), $payload);

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
        $response = $this->get(route('rooms.index'));

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->has('data', 15)
                ->has('links')
                ->where('current_page', 1)
                ->where('per_page', 15)
                ->has(
                    'data.0',
                    fn ($json) => $json
                        ->hasAll(collect($publicRoom[0]->getAttributes())->forget('password')->keys()->toArray())
                )
                ->has(
                    'data',
                    fn ($json) => $json
                        ->whereContains('id', $publicRoom[0]->id)
                        ->whereContains('code', $publicRoom[0]->code)
                        ->whereContains('type', 'public')
                        ->etc()
                )->etc()
        );
    });

    it('should not be possible to list non-exist specific rooms by id', function () use (&$publicRoom) {
        $response = $this->get(route('rooms.show', 9999999999));
        $response->assertNotFound();
    });

    it('should be possible to list specific  rooms by id', function () use (&$publicRoom) {
        $response = $this->get(route('rooms.show', $publicRoom[0]->id));
        $response->assertOk()
            ->assertJsonFragment([
                'id' => $publicRoom[0]->id,
            ]);
    });

    it('ensure that the password not return with room', function () use (&$publicRoom) {
        $response = $this->get(route('rooms.show', $publicRoom[0]->id));
        $response->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json->missing('password')->etc());
    });

    describe('Filter Validation', function () {
        it('filters rooms by simple fields', function ($field, $value) {
            Room::factory()->create([$field => $value]);

            $this->get(route('rooms.index', [$field => $value]))
                ->assertOk()
                ->assertJsonPath("data.0.$field", $value);
        })->with([
            ['code', 'ABC2'],
            ['type', 'public'],
        ]);

        it('should filter rooms by theme', function () {
            Room::factory()->create(['theme' => RoomTheme::UNDERWATER, 'type' => 'public']);
            Room::factory()->create(['theme' => RoomTheme::FILMS, 'type' => 'public']);

            $response = $this->get(route('rooms.index', ['theme' => [RoomTheme::UNDERWATER->value]]));

            $response->assertOk()
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.theme', RoomTheme::UNDERWATER->value);
        });

        it('should filter rooms by game mode', function () {
            Room::factory()->create(['game_mode' => RoomGameMode::HARD_POTATO]);

            $response = $this->get(route('rooms.index', ['game_mode' => [RoomGameMode::HARD_POTATO->value]]));

            $response->assertOk()
                ->assertJsonPath('data.0.game_mode', RoomGameMode::HARD_POTATO->value);
        });

        it('should not be able to list rooms if the filters are invalid', function (string $field, $value) {
            $response = $this->get(route('rooms.index', [$field => $value]));

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
        $publicRoom = Room::factory()->create(['user_id' => auth()->id(), 'type' => 'public', 'status' => 'waiting']);
        $privateRoom = Room::factory()->create(['user_id' => auth()->id(), 'type' => 'private', 'status' => 'waiting']);
    });

    it('should be able to update a room if owner', function () use (&$publicRoom) {
        $payload = [
            'password'  => null,
            'theme'     => 'underwater',
            'type'      => 'public',
            'max_users' => '2',
            'game_mode' => 'default',
        ];

        $response = $this->put(route('rooms.update', $publicRoom->id), $payload);

        $response->assertRedirect(route('rooms.index'));

        $this->assertDatabaseHas('rooms', $payload);
    });

    it('should be able to update a specific field room if owner', function (string $field, string $value) use (&$publicRoom) {
        $payload = [
            $field => $value,
        ];

        $response = $this->put(route('rooms.update', $publicRoom->id), $payload);

        $response->assertRedirect(route('rooms.index'));

        $this->assertDatabaseHas('rooms', $payload);
    })->with([
        ['theme', 'space'],
        ['max_users', '5'],
        ['game_mode', 'hard_potato'],
    ]);

    describe('Validations', function () use (&$publicRoom, &$privateRoom) {
        it('should not be able to update a room if not owner', function () use (&$publicRoom) {
            $userNotOwner = User::factory()->create();
            $this->actingAs($userNotOwner);

            $payload = [
                'password'  => null,
                'theme'     => 'underwater',
                'type'      => 'public',
                'max_users' => '2',
                'game_mode' => 'default',
            ];

            $response = $this->put(route('rooms.update', $publicRoom->id), $payload);

            $response->assertStatus(403);
        });

        it('should not be able to minor update a room to max_users minor of 2', function () use (&$publicRoom) {
            $payload = [
                'password'  => null,
                'theme'     => 'underwater',
                'type'      => 'public',
                'max_users' => '1',
                'game_mode' => 'default',
            ];

            $response = $this->put(route('rooms.update', $publicRoom->id), $payload);

            $response->assertStatus(302)
                ->assertInvalid('max_users');
        });

        it('should not be able to update a room code', function () use (&$publicRoom) {
            $payload = [
                'code'      => 'NEWCD',
                'password'  => null,
                'theme'     => 'underwater',
                'type'      => 'public',
                'max_users' => '1',
                'game_mode' => 'default',
            ];

            $response = $this->put(route('rooms.update', $publicRoom->id), $payload);

            $response->assertStatus(302)
                ->assertInvalid('code');
        });

        it('should not be able to update a room if status not is waiting', function (string $field, string $value) use (&$publicRoom) {
            $invalidUpdateStatus = [RoomStatus::PLAYING, RoomStatus::FINISHED];
            $publicRoom->update(['status' => $invalidUpdateStatus[array_rand($invalidUpdateStatus)]]);

            $payload = [
                $field => $value,
            ];

            $response = $this->put(route('rooms.update', $publicRoom->id), $payload);

            $response->assertForbidden();
        })->with([
            ['max_users', '5'],
            ['type', 'private'],
            ['game_mode', 'default'],
            ['theme', 'space'],
        ]);

        it('ensure that the update type room to private require password', function () use (&$publicRoom) {
            $payload = [
                'password'  => null,
                'theme'     => 'underwater',
                'type'      => 'private',
                'max_users' => '2',
                'game_mode' => 'default',
            ];

            $response = $this->put(route('rooms.update', $publicRoom->id), $payload);

            $response->assertStatus(302)
                ->assertInvalid('password');
        });

        it('ensure that the update type room to public require clean password', function () use (&$privateRoom) {
            $payload = [
                'password'  => '1234',
                'theme'     => 'underwater',
                'type'      => 'public',
                'max_users' => '2',
                'game_mode' => 'default',
            ];

            $response = $this->put(route('rooms.update', $privateRoom->id), $payload);

            $response->assertStatus(302)
                ->assertInvalid('password');
        });

        it('ensure that the update type room to public clear the password', function () use (&$privateRoom) {
            $payload = [
                'type' => 'public',
            ];

            $response = $this->put(route('rooms.update', $privateRoom->id), $payload);

            $response->assertRedirect(route('rooms.index'));

            $this->assertDatabaseHas('rooms', array_merge($payload, ['id' => $privateRoom->id, 'password' => null]));
        });

        it('ensure that the update room does not update the code', function () use (&$publicRoom) {
            $roomCode = $publicRoom->code;

            $payload = [
                'password'  => null,
                'theme'     => 'underwater',
                'type'      => 'public',
                'max_users' => '2',
                'game_mode' => 'default',
            ];

            $this->put(route('rooms.update', $publicRoom->id), $payload);

            $this->assertDatabaseHas('rooms', array_merge($payload, [
                'user_id' => auth()->id(),
                'id'      => $publicRoom->id,
                'code'    => $roomCode,
            ]));
        });

        it('ensure that the update private room without password in payload', function () use (&$privateRoom) {
            $roomPassword = $privateRoom->password;
            $payload = [
                'max_users' => '20',
            ];

            $this->put(route('rooms.update', $privateRoom->id), $payload);

            $this->assertDatabaseHas('rooms', array_merge($payload, [
                'user_id'  => auth()->id(),
                'id'       => $privateRoom->id,
                'password' => $roomPassword,
            ]));
        });
    });
});

describe('Room Members', function () {

    it('should be able enter in public room without password', function () {
        $room = Room::factory()->create(['user_id' => auth()->id(), 'status' => 'waiting', 'type' => 'public']);
        $payload = ['type' => $room->type->value];

        $response = $this->post(route('rooms.join', $room->code), $payload);

        $response->assertRedirect(route('rooms.show', $room->id));

        $this->assertDatabaseHas('room_user', [
            'room_id' => $room->id,
            'user_id' => auth()->id(),
        ]);
    });

    it('ensure that room owner join in room when created', function () {
        $payload = [
            'password'  => null,
            'theme'     => 'underwater',
            'type'      => 'public',
            'max_users' => '2',
            'game_mode' => 'default',
        ];

        $response = $this->post(route('rooms.store'), $payload);

        $response->assertRedirect(route('rooms.index'));

        $this->assertDatabaseHas('rooms', array_merge($payload, ['user_id' => auth()->id()]));
        $this->assertDatabaseHas('room_user', ['room_id' => 1, 'user_id' => auth()->id()]);
    });

    describe('Validations', function () {
        $room = null;

        beforeEach(function () use (&$room) {
            $room = Room::factory()->create([
                'user_id' => auth()->id(),
                'type'    => 'public',
                'status'  => 'waiting',
            ]);
        });

        it('ensure that delete room if empty', function () use (&$room) {
            $room->users()->attach(auth()->id());

            $this->assertDatabaseHas('rooms', ['id' => $room->id]);
            $this->assertDatabaseHas('room_user', [
                'room_id' => $room->id,
                'user_id' => auth()->id(),
            ]);

            $leaveRoomAction = app(LeaveRoom::class);
            $leaveRoomAction($room, auth()->id());

            $this->assertDatabaseMissing('rooms', ['id' => $room->id]);

            $this->assertDatabaseMissing('room_user', [
                'room_id' => $room->id,
                'user_id' => auth()->id(),
            ]);
        });

        it('should not be able enter a private room without password', function () use (&$room) {
            $room->update(['type' => 'private']);
            $payload = ['type' => $room->type->value];

            $response = $this->post(route('rooms.join', $room->code), $payload);

            $response->assertFound()
                ->assertInvalid('password');

            $this->assertDatabaseMissing('room_user', [
                'room_id' => $room->id,
                'user_id' => auth()->id(),
            ]);
        });

        it('should be able enter a private room with password', function () use (&$room) {
            $room->update(['type' => 'private', 'password' => '1234']);
            $payload = ['type' => $room->type->value, 'password' => $room->password];

            $response = $this->post(route('rooms.join', $room->code), $payload);

            $response->assertRedirect(route('rooms.show', $room->id));

            $this->assertDatabaseHas('room_user', [
                'room_id' => $room->id,
                'user_id' => auth()->id(),
            ]);
        });

        it('should not be able enter a full room', function () use (&$room) {
            $room->update(['max_users' => 2]);

            $users = User::factory()->count(2)->create();
            $room->users()->sync($users->pluck('id'));

            $payload = ['type' => $room->type->value];

            $response = $this->post(route('rooms.join', $room->code), $payload);

            $response->assertForbidden();

            $this->assertDatabaseMissing('room_user', [
                'room_id' => $room->id,
                'user_id' => auth()->id(),
            ]);
        });

        it('ensure that have a new owner when user owner left room', function () use (&$room) {
            $newUser = User::factory()->create();
            $users = collect($newUser->pluck('id'))->add(auth()->id());

            $room->users()->sync($users);

            $response = $this->delete(route('rooms.leave', $room->code));

            $response->assertOk();

            $this->assertDatabaseHas('rooms', [
                'id'      => $room->id,
                'user_id' => $newUser->id,
            ]);

            $this->assertDatabaseHas('room_user', [
                'room_id' => $room->id,
                'user_id' => $newUser->id,
            ]);
        });

        it('should be able kick user if owner', function () use (&$room) {
            $newUser = User::factory()->create();
            $users = collect($newUser->pluck('id'))->add(auth()->id());

            $room->users()->sync($users);

            $response = $this->delete(route('rooms.kick', [$room->code, $newUser->id]));

            $response->assertOk();

            $this->assertDatabaseHas('rooms', [
                'id'      => $room->id,
                'user_id' => auth()->id(),
            ]);

            $this->assertDatabaseHas('room_user', [
                'room_id' => $room->id,
                'user_id' => auth()->id(),
            ]);

            $this->assertDatabaseMissing('room_user', [
                'room_id' => $room->id,
                'user_id' => $newUser->id,
            ]);
        });
    });
});
