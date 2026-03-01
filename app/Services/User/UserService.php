<?php

namespace App\Services\User;

use App\Repository\Eloquent\User\IUserRepository;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Model;

class UserService extends BaseService implements IUserService
{
    public function __construct(IUserRepository $repository)
    {
        parent::__construct($repository);
    }

    public function createGuest(string $nickname): Model
    {
        return $this->store([
            'name' => $nickname,
            'nickname' => $nickname,
            'is_guest' => true,
        ]);
    }
}
