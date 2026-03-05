<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repository\Eloquent\Room\IRoomRepository;
use App\Repository\Eloquent\Room\RoomRepository;
use App\Repository\Eloquent\User\IUserRepository;
use App\Repository\Eloquent\User\UserRepository;
use App\Services\Room\IRoomService;
use App\Services\Room\RoomService;
use App\Services\User\IUserService;
use App\Services\User\UserService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(IUserService::class, UserService::class);
        $this->app->bind(IUserRepository::class, UserRepository::class);

        $this->app->bind(IRoomService::class, RoomService::class);
        $this->app->bind(IRoomRepository::class, RoomRepository::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
