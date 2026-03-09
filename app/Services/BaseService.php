<?php

declare(strict_types=1);

namespace App\Services;

use App\Filters\QueryFilter;
use App\Repository\IBaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseService
{
    public function __construct(protected IBaseRepository $repository) {}

    final public function index(): Collection
    {
        return $this->repository->findAll();
    }

    final public function indexPaginated(?QueryFilter $filters = null): LengthAwarePaginator
    {
        return $this->repository->findAllPaginated(filters: $filters);
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
