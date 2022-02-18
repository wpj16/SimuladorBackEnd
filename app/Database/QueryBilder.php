<?php

namespace App\Database;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use App\Database\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBilderBase;

class QueryBilder extends Builder
{
    const ORDEM = 'ordem';
    const FILTRO = 'filtro';
    const PAGINA = 'pagina';
    const LIMITE = 'limite';

    private $before = [];
    private $paginate = [];


    public function __construct(QueryBilderBase $query)
    {
        parent::__construct($query);
        $this->defaultPaginate();
        $this->defaultOnDelete();
    }

    private function defaultOnDelete()
    {
        parent::onDelete(function ($query) {
            $this->onDelete = null;
            $deleteAt = (method_exists($this->model, 'getDeleteAtColumn') &&  is_callable([$this->model, 'getDeleteAtColumn']));
            $deleteValue = (method_exists($this->model, 'getDeleteAtValue') &&  is_callable([$this->model, 'getDeleteAtValue']));
            $value = $deleteValue ? $this->model->getDeleteAtValue() : 0;
            if ($deleteAt && ($column = $this->model->getDeleteAtColumn())) {
                return $query->update([$column => $value]);
            }
            return $this->delete();
        });
    }

    private function defaultPaginate()
    {
        $request =  app('Illuminate\Http\Request');
        $parameters = $request->all();
        $request->merge([
            self::FILTRO => $parameters[self::FILTRO] ?? null,
            self::PAGINA => $parameters[self::PAGINA] ?? 1,
            self::LIMITE => $parameters[self::LIMITE] ?? 20,
            self::ORDEM => $parameters[self::ORDEM] ?? [],
        ]);
        Validator::request($request)
            ->rules([
                self::FILTRO => 'default:null',
                self::PAGINA => 'numeric',
                self::LIMITE => 'numeric',
                self::ORDEM => 'array',
            ])
            ->error(function () {
                $data = [
                    self::FILTRO =>  null,
                    self::PAGINA => 1,
                    self::LIMITE => 20,
                    self::ORDEM => [],
                ];
                $this->setPaginateParams($data);
            })
            ->success(function ($data) {
                $this->setPaginateParams($data);
            })
            ->validate();
        return $this;
    }

    public function before(Closure $before)
    {
        $this->before[] = $before;
        return $this;
    }

    public function beforeAppend(Closure $before)
    {
        array_push($this->before, $before);
        return $this;
    }

    public function beforePrepend(Closure $before)
    {
        array_unshift($this->before, $before);
        return $this;
    }

    public function setPaginateParams(array $params = []): QueryBilder
    {
        $this->paginate = [
            self::FILTRO => $params[self::FILTRO] ?? null,
            self::PAGINA => $params[self::PAGINA] ?? 1,
            self::LIMITE => $params[self::LIMITE] ?? 20,
            self::ORDEM => $params[self::ORDEM] ?? [],
        ];
        return $this;
    }

    public function columnsSearch(array|string $columns): QueryBilder
    {
        $columns = (is_array($columns) ? $columns : [$columns]);
        $columnsSearch = [];
        foreach ($columns as $column => $default) {
            if (is_numeric($column) && is_numeric(strpos($default, ':'))) {
                $columnArray = explode(':', $default);
                $column = trim(array_shift($columnArray));
                $default = trim(array_shift($columnArray));
            }
            if (is_numeric($column)) {
                $column = $default;
                $default = null;
            }
            $columnsSearch[$column] = $default;
        }
        $this->beforePrepend(function ($query) use ($columnsSearch) {
            $firstColumn = array_key_first($columnsSearch);
            $firstValue = array_shift($columnsSearch);
            $firstValue = $this->paginate[self::FILTRO] ?? $firstValue;
            if (strlen($firstValue) > 0) {
                $query->where($firstColumn, 'like', "%$firstValue%");
                foreach ($columnsSearch as $column => $value) {
                    $value = $this->paginate[self::FILTRO] ?? $value;
                    $query->orWhere($column, 'like', "%$value%");
                }
            }
            return $query;
        });
        return $this;
    }

    public function columnsSearchWith(array|string $relation, array $default = []): QueryBilder
    {
        $relationships = (is_array($relation) ? $relation : [$relation]);
        foreach ($relationships as $relationship) {
            $relationshipArray = array_filter(explode(':', $relationship));
            $relationshipKey = trim(array_shift($relationshipArray));
            $relationshipColumns = trim(array_shift($relationshipArray));
            $relationshipColumns = array_filter(explode(',', $relationshipColumns));
            $relationshipColumns = array_fill_keys($relationshipColumns, null);
            $this->beforeAppend(function ($query) use ($relationshipKey, $relationshipColumns, $default) {
                return $query->orWhereHas($relationshipKey, function ($query) use ($relationshipKey, $relationshipColumns, $default) {
                    $firstColumn = array_key_first($relationshipColumns);
                    $firstValue = array_shift($relationshipColumns);
                    $firstValue = $this->paginate[self::FILTRO] ?? $default[$relationshipKey . '.' . $firstColumn] ?? $default[$firstColumn] ?? $firstValue;
                    if (strlen($firstValue) > 0) {
                        $query->where($firstColumn, 'like', "%$firstValue%");
                        foreach ($relationshipColumns as $column => $value) {
                            $value = $this->paginate[self::FILTRO] ?? $default[$relationshipKey . '.' . $column] ?? $default[$column] ?? $value;
                            $query->orWhere($column, 'like', "%$value%");
                        }
                    }
                    return $query;
                });
            });
        }
        return $this;
    }

    public function columnsOrderBy(array|string $columns, string $order = 'asc'): QueryBilder
    {
        $scape = "/[\\r\\n\\t\\s]/";
        $columns = (is_array($columns) ? $columns : [$columns => $order]);
        $columnsOrders = [];
        foreach ($columns as $column => $order) {
            $order = preg_replace($scape, '', $order);
            $column = preg_replace($scape, '', $column);
            if (is_numeric(strpos($column, ','))) {
                $columnsArray = explode(',', $column);
                $columnsArray = array_fill_keys($columnsArray, $order);
                $columnsOrders = array_merge($columnsOrders, $columnsArray);
            } else {
                $columnsOrders = array_merge($columnsOrders, [$column => $order]);
            }
        }
        $this->beforeAppend(function ($query) use ($columnsOrders) {
            $columnsDynamic = $this->paginate[self::ORDEM] ?? [];
            $columnsDynamic = array_change_key_case($columnsDynamic, CASE_LOWER);
            foreach ($columnsOrders as $column => $order) {
                $order = $columnsDynamic[strtolower($column)] ?? $order;
                $order = in_array(strtolower($order), ['asc', 'desc']) ? $order : 'asc';
                $query->orderBy($column, $order);
            }
            return $query;
        });
        return $this;
    }

    public function columnsOrderByWith(array|string $relation, string $order = 'asc'): QueryBilder
    {
        $scape = "/[\\r\\n\\t\\s]/";
        $relation = (is_array($relation) ? $relation : [$relation => $order]);
        $columnsOrders = [];
        foreach ($relation as $relationship => $order) {
            $order = preg_replace($scape, '', $order);
            $relationship = preg_replace($scape, '', $relationship);
            if (is_numeric(strpos($relationship, ':')) && in_array(strtolower($order), ['asc', 'desc'])) {
                $relationshipsArray = explode(':', $relationship);
                $relationship = array_shift($relationshipsArray);
                $columnsOrders = array_filter(explode(',', array_shift($relationshipsArray)));
                $columnsOrders = array_fill_keys($columnsOrders, $order);
                $this->beforePrepend(function ($query) use ($relationship, $columnsOrders) {
                    $columnsDynamic = $this->paginate[self::ORDEM] ?? [];
                    $columnsDynamic = array_change_key_case($columnsDynamic, CASE_LOWER);
                    $eagerLoads = $query->getEagerLoads();
                    return $query->with([$relationship => function ($queryOrderBy) use ($relationship, $columnsOrders, $columnsDynamic, $eagerLoads) {
                        $preQuery = $eagerLoads[$relationship] ?? null;
                        $query = (($preQuery instanceof Closure) ? $preQuery($queryOrderBy) :  $queryOrderBy);
                        $queryOrderBy = $query ?: $queryOrderBy;
                        foreach ($columnsOrders as $column => $order) {
                            $order = $columnsDynamic[strtolower($column)] ?? $order;
                            $order = in_array(strtolower($order), ['asc', 'desc']) ? $order : 'asc';
                            $queryOrderBy->orderBy($column, $order);
                        }
                        return $queryOrderBy;
                    }]);
                });
            }
        }
        return $this;
    }

    public function get($columns = ['*'])
    {
        $instance = $this;
        foreach ($this->before as $before) {
            $instance = $before($instance) ?: $instance;
        }
        return parent::get($columns);
    }

    public function validate(Request $request): Validate
    {
        return (new Validate($request));
    }

    public function update(array $values)
    {
        $eventsFire = (method_exists($this->model, 'fireModelEvent') &&  is_callable([$this->model, 'fireModelEvent']));
        if ($eventsFire) $this->model->fireModelEvent('updating', true);
        //
        $values = parent::addUpdatedAtColumn($values);
        $values = $this->addUserUpdateAt($values);
        $update = parent::toBase()->update($values);
        //
        if ($eventsFire && $update) $this->model->fireModelEvent('updated', true);
        //
        return  $update;
    }

    public function upsert(array $values, $uniqueBy, $update = null)
    {
        $eventsFire = (method_exists($this->model, 'fireModelEvent') &&  is_callable([$this->model, 'fireModelEvent']));
        if (empty($values)) {
            return 0;
        }
        //
        if (!is_array(reset($values))) {
            $values = [$values];
        }
        //
        $updated = false;
        if (!empty($update)) {
            $updated = true;
            if ($eventsFire) $this->model->fireModelEvent('updating', true);
        }
        //
        if (is_null($update)) {
            $update = array_keys(reset($values));
        }
        //
        $update = $this->addUpdatedAtToUpsertColumns($update);
        $update = $this->addUserUpdateAt($update);
        //
        $data = $this->toBase()->upsert(
            $this->addTimestampsToUpsertValues($values),
            $uniqueBy,
            $update
        );
        //
        if (!empty($update)) {
            if ($eventsFire && $updated) $this->model->fireModelEvent('updated', true);
        }
        return $data;
    }

    public function decrement($column, $amount = 1, array $extra = [])
    {
        $eventsFire = (method_exists($this->model, 'fireModelEvent') &&  is_callable([$this->model, 'fireModelEvent']));
        //
        if ($eventsFire) $this->model->fireModelEvent('updating', true);
        //
        $data = $this->toBase()->decrement(
            $column,
            $amount,
            $this->addUserUpdateAt($this->addUpdatedAtColumn($extra))
        );
        //
        if ($eventsFire) $this->model->fireModelEvent('updated', true);
        return $data;
    }

    /**
     * Paginate the given query.
     *
     * @param  int|null  $perPage
     * @param  array  $columns
     * @param  string  $pageName
     * @param  int|null  $page
     * @return \App\Database\LengthAwarePaginator
     *
     * @throws \InvalidArgumentException
     */
    public function paginate($columns = ['*'], $perPage = null, $pageName = 'pagina', $page = null): LengthAwarePaginator
    {
        $instance = $this;
        foreach ($this->before as $before) {
            $instance = $before($instance) ?: $instance;
        }

        $page = ($this->paginate[self::PAGINA] ?? Paginator::resolveCurrentPage($pageName)) ?: $page;

        $perPage = ($this->paginate[self::LIMITE] ??  $this->model->getPerPage()) ?: $perPage;

        $results = ($total = $this->toBase()->getCountForPagination())
            ? $this->forPage($page, $perPage)->get($columns)
            : $this->model->newCollection();

        return $this->paginator($results, $total, $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);
    }

    /**
     * Create a new length-aware paginator instance.
     *
     * @param  \Illuminate\Support\Collection  $items
     * @param  int  $total
     * @param  int  $perPage
     * @param  int  $currentPage
     * @param  array  $options
     * @return \App\Database\LengthAwarePaginator
     */
    protected function paginator($items, $total, $perPage, $currentPage, $options): LengthAwarePaginator
    {
        return new LengthAwarePaginator($items, $total, $perPage, $currentPage, $options);
    }

    protected function addUserUpdateAt(array $values = [])
    {
        $userUpdateAt = (method_exists($this->model, 'getUserUpdatedAtColumn') &&  is_callable([$this->model, 'getUserUpdatedAtColumn']));
        $segments = preg_split('/\s+as\s+/i', $this->query->from);
        $endSegment = end($segments);
        if (!empty($userUpdateAt) && ($column = $this->model->getUserUpdatedAtColumn())) {
            $qualifiedColumn = empty($endSegment) ? trim($column) : (trim($endSegment) . '.' . trim($column));
            $user = $this->model->getUserUpdatedAtValue() ?: null;
            $isObject = array_filter($values, function ($value) {
                return !is_array($value);
            });
            if (empty($isObject)) {
                array_walk($values, function (&$value) use ($qualifiedColumn, $user, $column) {
                    if (is_array($value) && empty(in_array($column, $value)) && empty(in_array($qualifiedColumn, $value))) {
                        $value =  array_merge([$qualifiedColumn => $user], $value);
                    }
                    return $value;
                });
            } else {
                if (is_array($values) && empty(in_array($column, $values)) && empty(in_array($qualifiedColumn, $values))) {
                    $values =  array_merge([$qualifiedColumn => $user], $values);
                }
            }
        }
        return $values;
    }
}
