<?php
namespace Raahin\RepositoryPattern\Contracts;

use Illuminate\Database\Eloquent\Model;

interface RepositoryInterface
{
    public function getModel();

    public function create($items);

    public function update(Model $modelItem, $items);

    public function updateQuietly(Model $modelItem, array $attributes = [], array $options = []);

    public function updateOrCreate(array $attributes, array $values = []);

    public function find($id, $columns = ['*']);

    public function findOrFail($id);

    public function findByField($field, $value = null, $columns = ['*']);

    public function firstWhere($field, $value = null);

    public function findWhereNotIn($field, array $values, $columns = ['*']);

    public function findWhereBetween($field, array $values, $columns = ['*']);

    public function delete(Model $modelItem);

    public function destroy(Model $modelItem, $ids);

    public function forceDelete(Model $modelItem);

    public function all();

    public function get($columns = ['*']);

    public function paginate($limit = null, $columns = ['*'], $pageName = 'page', $page = null);

    public function orderBy(string $column, $direction = 'asc');
    
    public function orderByDesc(string $column);

    public function where(string $column, string $condition = '=', $value = null);

    public function whereHas($relation, $closure);

    public function whereCount($field, $value);

    public function whereNull($field);

    public function whereBetween($field, array $values);

    public function hidden(array $fields);

    public function visible(array $fields);

    public function withCount($relations);

    public function with($relations);

    public function load($relations);

    public function loadMorph($relation, $relations);

    public function loadMissing($relations);

    public function loadAggregate($relations, $column, $function = null);

    public function loadCount($relations);

    public function loadMax($relations, $column);

    public function loadMin($relations, $column);

    public function loadSum($relations, $column);

    public function loadAvg($relations, $column);

    public function loadExists($relations);

    public function loadMorphAggregate($relation, $relations, $column, $function = null);

    public function loadMorphCount($relation, $relations);

    public function loadMorphMax($relation, $relations, $column);

    public function loadMorphMin($relation, $relations, $column);

    public function loadMorphSum($relation, $relations, $column);

    public function loadMorphAvg($relation, $relations, $column);

    public function has($relation);

    public function first($columns = ['*']);

    public function firstOrNew(array $attributes = []);

    public function firstOrCreate(array $attributes = []);

    public function count($columns = '*');

    public function take($limit);

    public function min($column);

    public function max($column);

    public function sum($column);

    public function avg($column);

    public function average($column);

    public function pluck($value, $key = null);

    public function limit($limit, $columns = ['*']);
}