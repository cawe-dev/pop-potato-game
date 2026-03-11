<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\UserLeftRoomEvent;
use App\Services\Room\IRoomService;

final class DeleteRoomIfEmpty
{
    /**
     * Create the event listener.
     */
    public function __construct(private IRoomService $service)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserLeftRoomEvent $event): void
    {
        if ($event->room->users()->count() === 0) {
            $this->service->destroy($event->room->id);
        }
    }
}
