<?php

namespace App\Repository\Eloquent;

use App\Repository\IBaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class BaseRepository implements IBaseRepository
{
    public function __construct(protected Model $model) {}

    public function find(int $id)
    {
        return $this->model->find($id);
    }

    public function findAll()
    {
        return $this->model->all();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $record = $this->find($id);
        $record->update($data);

        return $record;
    }

    public function delete(int $id)
    {
        $record = $this->find($id);
        return $record->delete();
    }

    public function findAllPaginated(int $perPage): LengthAwarePaginator
    {
        return $this->paginateQuery($this->model->query(), $perPage);
    }

    protected function paginateQuery(Builder $query, int $perPage = 15): LengthAwarePaginator
    {
        return $query->paginate($perPage)->withQueryString();
    }
}
