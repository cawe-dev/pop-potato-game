<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Room;
use App\Services\Room\IRoomService;
use Illuminate\Http\Request;

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

    public function store(Request $request)
    {
        //
    }

    public function show(Room $room)
    {
        //
    }

    public function edit(Room $room)
    {
        //
    }

    public function update(Request $request, Room $room)
    {
        //
    }

    public function destroy(Room $room)
    {
        //
    }
}
