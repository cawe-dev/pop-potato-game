<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Filters\RoomFilter;
use App\Http\Requests\JoinRoomRequest;
use App\Http\Requests\Room\IndexRoomRequest;
use App\Http\Requests\Room\KickRoomRequest;
use App\Http\Requests\Room\StoreRoomRequest;
use App\Http\Requests\Room\UpdateRoomRequest;
use App\Services\Room\IRoomService;

final class RoomController extends Controller
{
    public function __construct(protected IRoomService $service) {}

    public function index(IndexRoomRequest $request, RoomFilter $filter)
    {
        return $this->service->indexPaginated($filter);
    }

    public function create()
    {
        //
    }

    public function store(StoreRoomRequest $request)
    {
        $validated = $request->validated();
        $data = array_merge($validated, [
            'user_id' => auth()->id(),
        ]);

        $this->service->store($data);

        return redirect()->intended(route('rooms.index'));
    }

    public function show(int $id)
    {
        return $this->service->show($id);
    }

    public function join(JoinRoomRequest $request, string $code)
    {
        $validated = $request->validated();
        $password = $validated->password ?? null;

        $room = $this->service->join($code, $password, auth()->id());

        return redirect()->intended(route('rooms.show', $room->id));
    }

    public function leave(string $code)
    {
        $this->service->leave($code, auth()->id());
    }

    public function kick(KickRoomRequest $request, string $code, int $userId)
    {
        $this->service->leave($code, $userId);
    }

    public function edit(int $id)
    {
        //
    }

    public function update(UpdateRoomRequest $request, int $id)
    {
        $this->service->update($id, $request->validated());

        return redirect()->intended(route('rooms.index'));
    }

    public function destroy(int $id)
    {
        //
    }
}
