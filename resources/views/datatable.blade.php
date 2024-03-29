@php
    $id = uniqid('datatable-');
    $colspan = count($columns) + ($actions ? 1 : 0);
@endphp

<div class="card livewire-datatable" @style(['max-height: ' . $maxHeight]) id="{{ $id }}">
    @if ($headerable)
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
                @if ($title)
                    <h3 class="card-title">{{ $title }}</h3>
                @endif

                @if ($subtitle)
                    <h5 class="card-subtitle text-muted">{{ $subtitle }}</h5>
                @endif
            </div>

            <div class="d-flex gap-2">
                @if ($searchable)
                    <div class="input-icon">
                        <input type="text" wire:model.live="search" class="form-control" placeholder="{{ __('Search') }}..." />
                        <span class="input-icon-addon">
                            <i class="{{ config('livewire-datatable.icons.search') }}"></i>
                        </span>
                    </div>
                @endif

                @if (count($headerButtons) > 0)
                    <div class="dropdown">
                        <a href="#" class="btn dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="{{ config('livewire-datatable.icons.actions') }}"></i>
                            <span class="ms-2">{{ __('Actions') }}</span>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end">
                            @foreach ($headerButtons as $button)
                                <li>{!! $button->render() !!}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-vcenter card-table text-nowrap">
            <thead @class(['sticky-top' => $fixedHeader])>
                <tr>
                    @foreach ($columns as $column)
                        <th style="width: {{ $column->width }}; min-width: {{ $column->width }}; max-width: {{ $column->width }}" @class([$column->class, 'sortable' => $column->sortable])>
                            @if ($column->sortable)
                                <a href="#" class="text-decoration-none text-muted" wire:click.prevent="sort('{{ $column->field }}')">
                                    {{ $column->label }}

                                    @php $icon = $sortField === $column->field ? $sortDirection : 'none'; @endphp
                                    <i class="ms-1 {{ config('livewire-datatable.icons.sort-' . $icon) }}"></i>
                                </a>
                            @else
                                {{ $column->label }}
                            @endif
                        </th>
                    @endforeach

                    @if ($actions)
                        <th style="width: 1%" datatable-actions>{{ __('Actions') }}</th>
                    @endif
                </tr>
            </thead>

            <tbody wire:loading.class="opacity-50">
                @forelse ($rows as $row)
                    <tr>
                        @foreach ($columns as $column)
                            <td class="text-truncate {{ $column->class }}" style="width: {{ $column->width }}; min-width: {{ $column->width }}; max-width: {{ $column->width }}">
                                {!! $column->value($row) ?: '-' !!}
                            </td>
                        @endforeach

                        @if ($actions)
                            <td datatable-actions>
                                <div class="d-flex gap-1">
                                    @foreach ($actions as $action)
                                        {!! $action->render($row) !!}
                                    @endforeach
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $colspan }}" class="text-center py-5">
                            {{ __('No records found') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer d-flex gap-5 align-items-center justify-content-between">
        <div class="d-flex gap-2 align-items-center">
            <select class="form-select w-auto" wire:model.live="perPage">
                @foreach ($perPageOptions as $option)
                    <option value="{{ $option }}">{{ $option }}</option>
                @endforeach
            </select>

            <div class="text-muted">
                {{ __('Showing') }}
                <span class="fw-bold">{{ $rows->firstItem() ?: 0 }}</span>
                {{ __('to') }}
                <span class="fw-bold">{{ $rows->lastItem() ?: 0 }}</span>
                {{ __('of') }}
                <span class="fw-bold">{{ $rows->total() }}</span>
                {{ __('results') }}
            </div>
        </div>

        <div class="my-1">{{ $rows->links() }}</div>
    </div>
</div>
