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
        $data = $this->beforeStore($data);

        $model = $this->repository->create($data);

        $this->afterStore($model, $data);

        return $model;
    }

    final public function update(int $id, array $data): Model
    {
        $data = $this->beforeUpdate($data);

        $model = $this->repository->update($id, $data);

        $this->afterUpdate($model, $data);

        return $model;
    }

    final public function destroy(int $id): bool
    {
        return $this->repository->delete($id);
    }

    protected function beforeStore(array $data): array
    {
        return $data;
    }

    protected function afterStore(Model $model, array $data): void
    {
        //
    }

    protected function beforeUpdate(array $data): array
    {
        return $data;
    }

    protected function afterUpdate(Model $model, array $data): void
    {
        //
    }
}
