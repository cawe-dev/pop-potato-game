<?php

declare(strict_types=1);

namespace App\Repository\Eloquent;

use App\Filters\QueryFilter;
use App\Repository\IBaseRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository implements IBaseRepository
{
    public function __construct(private Model $model) {}

    final public function find(int $id)
    {
        return $this->model->findOrFail($id);
    }

    final public function findAll()
    {
        return $this->model->all();
    }

    final public function create(array $data)
    {
        return $this->model->create($data);
    }

    final public function update(int $id, array $data)
    {
        $record = $this->find($id);
        $record->update($data);

        return $record;
    }

    final public function delete(int $id)
    {
        $record = $this->find($id);

        return $record->delete();
    }

    final public function findAllPaginated(int $perPage = 15, ?QueryFilter $filters = null): LengthAwarePaginator
    {
        return $this->model->query()
            ->latest()
            ->when($filters, fn ($query) => $query->filter($filters))
            ->paginate($perPage)
            ->withQueryString();
    }
}
