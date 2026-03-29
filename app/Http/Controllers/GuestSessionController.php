<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Auth\GuestLoginRequest;
use App\Services\User\IUserService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

final class GuestSessionController extends Controller
{
    public function __construct(protected IUserService $userService) {}

    public function index()
    {
        return Inertia::render('auth/GuestLogin');
    }

    public function store(GuestLoginRequest $request)
    {
        $credentials = $request->safe()->only('nickname');
        $user = $this->userService->createGuest($credentials['nickname']);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }
}
