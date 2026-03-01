<?php

namespace App\Services\User;

use Illuminate\Database\Eloquent\Model;

interface IUserService
{
    public function createGuest(string $nickname): Model;
}
