<?php

declare(strict_types=1);

namespace App\Repository\Eloquent\User;

use App\Models\User;
use App\Repository\Eloquent\BaseRepository;

final class UserRepository extends BaseRepository implements IUserRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }
}
