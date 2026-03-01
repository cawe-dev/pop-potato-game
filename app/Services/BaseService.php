<?php

declare(strict_types=1);

namespace App\Services;

use App\Repository\IBaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseService
{
    public function __construct(protected IBaseRepository $repository) {}

    final public function index(): Collection
    {
        return $this->repository->findAll();
    }

    final public function show(int $id): Model
    {
        return $this->repository->find($id);
    }

    final public function store(array $data): Model
    {
        return $this->repository->create($data);
    }

    final public function update(int $id, array $data): Model
    {
        return $this->repository->update($id, $data);
    }

    final public function destroy(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
