<?php

namespace App\Services;

use App\Repository\IBaseRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

abstract class BaseService
{
    public function __construct(protected IBaseRepository $repository) {}

    public function index(): Collection
    {
        return $this->repository->findAll();
    }

    public function show(int $id): Model
    {
        return $this->repository->find($id);
    }

    public function store(array $data): Model
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): Model
    {
        return $this->repository->update($id, $data);
    }

    public function destroy(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
