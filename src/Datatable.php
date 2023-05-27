<?php

namespace Redot\LivewireDatatable;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

abstract class Datatable extends Component
{
    use WithPagination;

    /**
     * Create button.
     */
    public string|bool $create = false;

    /**
     * Search term.
     */
    public string $search = '';

    /**
     * Sort field.
     */
    public string $sortField = '';

    /**
     * Sort direction.
     */
    public string $sortDirection = 'asc';

    /**
     * Per page.
     */
    public int $perPage = 10;

    /**
     * Per page options.
     */
    public array $perPageOptions = [10, 25, 50, 100];

    /**
     * Query builder.
     */
    abstract public function query(): Builder;

    /**
     * Data table columns.
     */
    abstract public function columns(): array;

    /**
     * Data table actions.
     */
    public function actions(): array
    {
        return [];
    }

    /**
     * Pagination view.
     */
    public function paginationView(): string
    {
        return 'livewire-datatable::pagination.default';
    }

    /**
     * Simple pagination view.
     */
    public function paginationSimpleView(): string
    {
        return 'livewire-datatable::pagination.simple';
    }

    /**
     * Reset page number when searching.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Reset page number when sorting.
     */
    public function updatingSortField(): void
    {
        $this->resetPage();
    }

    /**
     * Reset page number when changing per page.
     */
    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    /**
     * Apply search and sort to the query.
     */
    public function applySearch(Builder $query): void
    {
        if ($this->search === '') {
            return;
        }

        $query->where(function ($query) {
            foreach ($this->columns() as $column) {
                if ($column->searchable === true && $column->field !== null) {
                    $query->orWhere($column->field, 'like', '%'.$this->search.'%');
                }
            }
        });
    }

    /**
     * Sort the query.
     */
    public function applySort(Builder $query): void
    {
        if ($this->sortField === '') {
            return;
        }

        $query->orderBy($this->sortField, $this->sortDirection);
    }

    /**
     * Toggle sort direction.
     */
    public function sort(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'desc';
        }
    }

    /**
     * Build the component params.
     */
    public function params(): array
    {
        $params['columns'] = $this->columns();
        $params['actions'] = $this->actions();

        $query = $this->query();

        $this->applySearch($query);
        $this->applySort($query);

        $params['rows'] = $query->paginate($this->perPage);

        $searchables = array_filter($this->columns(), fn ($column) => $column->searchable === true);
        $params['searchable'] = count($searchables) > 0;

        return $params;
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire-datatable::datatable', $this->params());
    }
}