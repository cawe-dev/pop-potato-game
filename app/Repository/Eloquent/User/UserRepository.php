<?php

namespace App\Repository\Eloquent\User;

use App\Models\User;
use App\Repository\Eloquent\BaseRepository;
use App\Repository\Eloquent\User\IUserRepository;

class UserRepository extends BaseRepository implements IUserRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }
}
