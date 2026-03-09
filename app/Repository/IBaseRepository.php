<?php

declare(strict_types=1);

namespace App\Repository;

use App\Filters\QueryFilter;

interface IBaseRepository
{
    public function find(int $id);

    public function findAll();

    public function findAllPaginated(int $perPage = 15, ?QueryFilter $filters = null);

    public function create(array $data);

    public function update(int $id, array $data);

    public function delete(int $id);
}
