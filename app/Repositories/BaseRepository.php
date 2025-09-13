<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class BaseRepository
{
    protected Model $model;

    protected array $searchableColumns = [];

    protected array $filterableColumns = [];

    protected array $sortableColumns = [];

    protected array $selectableColumns = [];

    protected array $includableRelations = [];

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function find(int $id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $record = $this->model->find($id);
        $record->update($data);

        return $record;
    }

    public function delete(int $id)
    {
        return $this->model->destroy($id);
    }

    /**
     * Advanced filtering method similar to nest-paginate
     * Supports pagination, sorting, searching, filtering with operators, column selection, and relations
     */
    public function filter(Request $request): array
    {
        $query = $this->model->newQuery();

        // Apply column selection
        $this->applySelect($query, $request);

        // Apply relations/includes
        $this->applyIncludes($query, $request);

        // Apply search across columns
        $this->applySearch($query, $request);

        // Apply filters with operators
        $this->applyFilters($query, $request);

        // Apply sorting
        $this->applySorting($query, $request);

        // Apply pagination
        $paginator = $this->applyPagination($query, $request);

        // Transform to custom structure
        return $this->transformPaginatorResponse($paginator);
    }

    /**
     * Apply column selection (select specific fields)
     */
    protected function applySelect(Builder $query, Request $request): void
    {
        $select = $request->get('select');
        if ($select && is_string($select)) {
            $columns = array_map('trim', explode(',', $select));
            $allowedColumns = empty($this->selectableColumns) ? ['*'] : $this->selectableColumns;

            if ($allowedColumns !== ['*']) {
                $columns = array_intersect($columns, $allowedColumns);
            }

            if (! empty($columns)) {
                // Always include the primary key
                if (! in_array('id', $columns)) {
                    $columns[] = 'id';
                }
                $query->select($columns);
            }
        }
    }

    /**
     * Apply includes/relations
     */
    protected function applyIncludes(Builder $query, Request $request): void
    {
        $include = $request->get('include');
        if ($include && is_string($include)) {
            $relations = array_map('trim', explode(',', $include));
            $allowedRelations = $this->includableRelations;

            if (! empty($allowedRelations)) {
                $relations = array_intersect($relations, $allowedRelations);
            }

            if (! empty($relations)) {
                $query->with($relations);
            }
        }
    }

    /**
     * Apply search across multiple columns
     */
    protected function applySearch(Builder $query, Request $request): void
    {
        $search = $request->get('search');
        if ($search && ! empty($this->searchableColumns)) {
            $query->where(function ($q) use ($search) {
                foreach ($this->searchableColumns as $column) {
                    $q->orWhere($column, 'LIKE', "%{$search}%");
                }
            });
        }
    }

    /**
     * Apply filters with operators
     * Supports: $eq, $not, $null, $in, $gt, $gte, $lt, $lte, $btw, $ilike, $sw, $contains
     */
    protected function applyFilters(Builder $query, Request $request): void
    {
        $filters = $request->get('filter', []);
        if (! is_array($filters)) {
            return;
        }

        foreach ($filters as $field => $conditions) {
            // Skip if field is not filterable
            if (! empty($this->filterableColumns) && ! in_array($field, $this->filterableColumns)) {
                continue;
            }

            if (! is_array($conditions)) {
                // Simple equality filter
                $query->where($field, $conditions);

                continue;
            }

            foreach ($conditions as $operator => $value) {
                $this->applyFilterOperator($query, $field, $operator, $value);
            }
        }
    }

    /**
     * Apply individual filter operator
     */
    protected function applyFilterOperator(Builder $query, string $field, string $operator, $value): void
    {
        switch ($operator) {
            case '$eq':
                $query->where($field, '=', $value);
                break;
            case '$not':
                $query->where($field, '!=', $value);
                break;
            case '$null':
                if ($value) {
                    $query->whereNull($field);
                } else {
                    $query->whereNotNull($field);
                }
                break;
            case '$in':
                if (is_array($value)) {
                    $query->whereIn($field, $value);
                }
                break;
            case '$gt':
                $query->where($field, '>', $value);
                break;
            case '$gte':
                $query->where($field, '>=', $value);
                break;
            case '$lt':
                $query->where($field, '<', $value);
                break;
            case '$lte':
                $query->where($field, '<=', $value);
                break;
            case '$btw':
                if (is_array($value) && count($value) === 2) {
                    $query->whereBetween($field, $value);
                }
                break;
            case '$ilike':
                $query->where($field, 'LIKE', "%{$value}%");
                break;
            case '$sw': // starts with
                $query->where($field, 'LIKE', "{$value}%");
                break;
            case '$contains':
                $query->where($field, 'LIKE', "%{$value}%");
                break;
        }
    }

    /**
     * Apply sorting by multiple columns
     */
    protected function applySorting(Builder $query, Request $request): void
    {
        $sort = $request->get('sort');
        if ($sort && is_string($sort)) {
            $sortFields = array_map('trim', explode(',', $sort));

            foreach ($sortFields as $sortField) {
                $direction = 'asc';
                if (str_starts_with($sortField, '-')) {
                    $direction = 'desc';
                    $sortField = substr($sortField, 1);
                }

                // Check if field is sortable
                if (empty($this->sortableColumns) || in_array($sortField, $this->sortableColumns)) {
                    $query->orderBy($sortField, $direction);
                }
            }
        }
    }

    /**
     * Apply pagination (JSON:API compliant)
     */
    protected function applyPagination(Builder $query, Request $request): LengthAwarePaginator
    {
        $page = max(1, (int) $request->get('page', 1));
        $limit = min(100, max(1, (int) $request->get('limit', 20)));

        return $query->paginate($limit, ['*'], 'page', $page);
    }

    /**
     * Transform paginator response to custom structure with data, meta, and links
     */
    protected function transformPaginatorResponse(LengthAwarePaginator $paginator): array
    {
        return [
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'from' => $paginator->firstItem(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'to' => $paginator->lastItem(),
                'total' => $paginator->total(),
            ],
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
        ];
    }

    /**
     * Set searchable columns for this repository
     */
    public function setSearchableColumns(array $columns): self
    {
        $this->searchableColumns = $columns;

        return $this;
    }

    /**
     * Set filterable columns for this repository
     */
    public function setFilterableColumns(array $columns): self
    {
        $this->filterableColumns = $columns;

        return $this;
    }

    /**
     * Set sortable columns for this repository
     */
    public function setSortableColumns(array $columns): self
    {
        $this->sortableColumns = $columns;

        return $this;
    }

    /**
     * Set selectable columns for this repository
     */
    public function setSelectableColumns(array $columns): self
    {
        $this->selectableColumns = $columns;

        return $this;
    }

    /**
     * Set includable relations for this repository
     */
    public function setIncludableRelations(array $relations): self
    {
        $this->includableRelations = $relations;

        return $this;
    }
}
