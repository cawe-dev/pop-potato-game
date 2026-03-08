<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Room\StoreRoomRequest;
use App\Http\Requests\Room\UpdateRoomRequest;
use App\Models\Room;
use App\Services\Room\IRoomService;

final class RoomController extends Controller
{
    public function __construct(protected IRoomService $roomService) {}

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(StoreRoomRequest $request)
    {
        $validated = $request->validated();
        $data = array_merge($validated, ['user_id' => auth()->id()]);

        $this->roomService->store($data);

        return redirect()->intended(route('room.index'));
    }

    public function show(Room $room)
    {
        //
    }

    public function edit(Room $room)
    {
        //
    }

    public function update(UpdateRoomRequest $request, Room $room)
    {
        $this->roomService->update($room->id, $request->all());

        return redirect()->intended(route('room.index'));
    }

    public function destroy(Room $room)
    {
        //
    }
}
