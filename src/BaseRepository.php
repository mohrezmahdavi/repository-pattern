<?php
namespace Raahin\RepositoryPattern;

use Closure;
use Illuminate\Container\Container as Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Raahin\RepositoryPattern\Contracts\RepositoryInterface;
use Raahin\RepositoryPattern\Exceptions\RepositoryException;

abstract class BaseRepository implements RepositoryInterface
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var int
     */
    protected $perPage = 15;

    /**
     * @var Closure
     */
    protected $scopeQuery = null;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->makeModel();
    }

    /**
     * Specify Model class name
     *
     * @return string
     */
    abstract public function model();

    /**
     * @return Model
     * @throws RepositoryException
     */
    public function makeModel()
    {
        $model = $this->app->make($this->model());
        if(!$model instanceof Model)
        {
            throw new RepositoryException("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }
        return $this->model = $model;
    }

    /**
     * Return the current Model instance
     * 
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @throws RepositoryException
     */
    public function resetModel()
    {
        $this->makeModel();
    }

    /**
     * Query Scope
     *
     * @param \Closure $scope
     *
     * @return $this
     */
    public function scopeQuery(\Closure $scope)
    {
        $this->scopeQuery = $scope;

        return $this;
    }

    /**
     * Reset Query Scope
     *
     * @return $this
     */
    public function resetScope()
    {
        $this->scopeQuery = null;

        return $this;
    }

    /**
     * Apply scope in current Query
     *
     * @return $this
     */
    protected function applyScope()
    {
        if (isset($this->scopeQuery) && is_callable($this->scopeQuery)) {
            $callback = $this->scopeQuery;
            $this->model = $callback($this->model);
        }

        return $this;
    }

    public function create($items)
    {
        $model = $this->model->create($items);
        $this->resetModel();
        return $model;
    }

    public function update(Model $modelItem, $items)
    {
        $this->applyScope();
        $model = $modelItem->update($items);
        $this->resetModel();
        return $model;
    }

    public function updateQuietly(Model $modelItem, array $attributes = [], array $options = [])
    {
        return $modelItem->updateQuietly($attributes, $options);
    }

    public function updateOrCreate(array $attributes, array $values = [])
    {
        $this->applyScope();
        $model = $this->model->updateOrCreate($attributes, $values);
        $this->resetModel();
        return $model;
    }

    public function find($id, $columns = ['*'])
    {
        $this->applyScope();
        $model = $this->model->find($id,$columns);
        $this->resetModel();
        return $model;
    }

    public function findOrFail($id)
    {
        return $this->model->findOrFail($id);
    }

    public function findByField($field, $value = null, $columns = ['*'])
    {
        $this->applyScope();
        $model = $this->model->where($field, '=', $value)->get($columns);
        $this->resetModel();
        return $model;
    }

    public function firstWhere($field, $value = null)
    {
        $this->applyScope();
        $model = $this->model->firstWhere($field, $value);
        $this->resetModel();
        return $model;
    }

    public function findWhereNotIn($field, array $values, $columns = ['*'])
    {
        $this->applyScope();
        $model = $this->model->whereNotIn($field, $values)->get($columns);
        $this->resetModel();
        return $model;
    }

    public function findWhereBetween($field, array $values, $columns = ['*'])
    {
        $this->applyScope();
        $model = $this->model->whereBetween($field, $values)->get($columns);
        $this->resetModel();
        return $model;
    }

    public function delete(Model $modelItem)
    {
        $this->applyScope();
        $this->resetModel();
        return $modelItem->delete();
    }

    public function destroy(Model $modelItem, $ids)
    {
        $this->applyScope();
        $this->resetModel();
        return $modelItem->destroy($ids);
    }

    public function forceDelete(Model $modelItem)
    {
        $this->applyScope();
        $this->resetModel();
        return $modelItem->forceDelete();
    }

    public function all($columns = ['*'])
    {
        $this->applyScope();
        $results = $this->model->all($columns);
        $this->resetModel();
        $this->resetScope();
        return $results;
    }

    public function get($columns = ['*'])
    {
        $this->applyScope();
        $results = $this->model->get($columns);
        $this->resetModel();
        $this->resetScope();
        return $results;
    }

    public function paginate($limit = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $this->applyScope();
        $limit = is_null($limit) ? $this->perPage : $limit;
        $results = $this->model->paginate($limit = null, $columns, $pageName, $page);
        $this->resetModel();
        $this->resetScope();
        return $results;
    }

    public function orderBy(string $column, $direction = 'asc')
    {
        $this->model = $this->model->orderBy($column, $direction);

        return $this;
    }

    public function orderByDesc(string $column)
    {
        $this->model = $this->model->orderByDesc($column);

        return $this;
    }

    public function where(string $column, string $condition = '=', $value = null)
    {
        $this->model = $this->model->where($column, $condition, $value);

        return $this;
    }

    public function whereHas($relation, $closure)
    {
        $this->model = $this->model->whereHas($relation, $closure);

        return $this;
    }

    public function whereCount($field, $value)
    {
        $this->applyScope();
        $results = $this->model->where($field, $value)->count();
        $this->resetModel();
        $this->resetScope();
        return $results;
    }

    public function whereNull($field)
    {
        $this->model = $this->model->whereNull($field);
        return $this;
    }

    public function whereBetween($field, array $values)
    {
        $this->model = $this->model->whereBetween($field, $values);
        return $this;
    }

    public function hidden(array $fields)
    {
        $this->model->setHidden($fields);

        return $this;
    }

    public function visible(array $fields)
    {
        $this->model->setVisible($fields);

        return $this;
    }

    public function withCount($relations)
    {
        $this->model = $this->model->withCount($relations);
        return $this;
    }

    public function with($relations)
    {
        $this->model = $this->model->with($relations);

        return $this;
    }

    public function load($relations)
    {
        $this->model = $this->model->load($relations);
        return $this->model;
    }

    public function loadMorph($relation, $relations)
    {
        $this->model = $this->model->loadMorph($relation, $relations);
        return $this->model;
    }

    public function loadMissing($relations)
    {
        $this->model = $this->model->loadMissing($relations);
        return $this->model;
    }

    public function loadAggregate($relations, $column, $function = null)
    {
        $this->model = $this->model->loadAggregate($relations, $column, $function);
        return $this->model;
    }

    public function loadCount($relations)
    {
        $this->applyScope();
        $results = $this->model->loadCount($relations);
        $this->resetModel();
        $this->resetScope();
        return $results;
    }

    public function loadMax($relations, $column)
    {
        $this->applyScope();
        $results = $this->model->loadMax($relations, $column);
        $this->resetModel();
        $this->resetScope();
        return $results;
    }

    public function loadMin($relations, $column)
    {
        $this->applyScope();
        $results = $this->model->loadMin($relations, $column);
        $this->resetModel();
        $this->resetScope();
        return $results;
    }

    public function loadSum($relations, $column)
    {
        $this->applyScope();
        $results = $this->model->loadSum($relations, $column);
        $this->resetModel();
        $this->resetScope();
        return $results;
    }

    public function loadAvg($relations, $column)
    {
        $this->applyScope();
        $results = $this->model->loadAvg($relations, $column);
        $this->resetModel();
        $this->resetScope();
        return $results;
    }

    public function loadExists($relations)
    {
        $this->applyScope();
        $results = $this->model->loadExists($relations);
        $this->resetModel();
        $this->resetScope();
        return $results;
    }

    public function loadMorphAggregate($relation, $relations, $column, $function = null)
    {
        $this->model =  $this->model->loadMorphAggregate($relation, $relations, $column, $function);
        return $this->model;
    }

    public function loadMorphCount($relation, $relations)
    {
        return $this->model->loadMorphCount($relation, $relations);
    }

    public function loadMorphMax($relation, $relations, $column)
    {
        return $this->model->loadMorphMax($relation, $relations, $column);
    }

    public function loadMorphMin($relation, $relations, $column)
    {
        return $this->model->loadMorphMin($relation, $relations, $column);
    }

    public function loadMorphSum($relation, $relations, $column)
    {
        return $this->model->loadMorphSum($relation, $relations, $column);
    }

    public function loadMorphAvg($relation, $relations, $column)
    {
        return $this->model->loadMorphAvg($relation, $relations, $column);
    }

    public function has($relation)
    {
        $this->model = $this->model->has($relation);

        return $this;
    }

    public function first($columns = ['*'])
    {
        $this->applyScope();
        $results = $this->model->first($columns);
        $this->resetModel();
        return $results;
    }

    public function firstOrNew(array $attributes = [])
    {
        $this->applyScope();
        $model = $this->model->firstOrNew($attributes);
        $this->resetModel();
        return $model;
    }

    public function firstOrCreate(array $attributes = [])
    {
        $this->applyScope();
        $model = $this->model->firstOrCreate($attributes);
        $this->resetModel();
        return $model;
    }

    public function count($columns = '*')
    {
        $this->applyScope();
        $result = $this->model->count($columns);
        $this->resetModel();
        $this->resetScope();
        return $result;
    }

    public function take($limit)
    {
        $this->model = $this->model->take($limit);
        return $this->model;
    }

    public function min($column)
    {
        $this->applyScope();
        $result = $this->model->min($column);
        $this->resetModel();
        $this->resetScope();
        return $result;
    }

    public function max($column)
    {
        $this->applyScope();
        $result = $this->model->max($column);
        $this->resetModel();
        $this->resetScope();
        return $result;
    }

    public function sum($column)
    {
        $this->applyScope();
        $result = $this->model->sum($column);
        $this->resetModel();
        $this->resetScope();
        return $result;
    }

    public function avg($column)
    {
        $this->applyScope();
        $result = $this->model->avg($column);
        $this->resetModel();
        $this->resetScope();
        return $result;
    }

    public function average($column)
    {
        $this->applyScope();
        $result = $this->model->average($column);
        $this->resetModel();
        $this->resetScope();
        return $result;
    }

    public function pluck($value, $key = null)
    {
        $this->applyScope();
        $result = $this->model->pluck($value, $key);
        $this->resetModel();
        $this->resetScope();
        return $result;
    }

    public function limit($limit, $columns = ['*'])
    {
        $this->take($limit);

        return $this->all($columns);
    }
}