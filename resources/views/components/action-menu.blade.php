@props([
    'id' => null,
    'attribute' => 'user',
    'actions' => null,
    'label' => '',
    'action' => '',
    'icon' => null,
    'className' => 'btn btn-light',
    'danger' => false,
    'position' => null,
])

@if (is_array($actions) && count($actions) > 0)

    <div class="dropdown action-menu{{ $position ? ' action-menu--' . $position : '' }}">

        <button
            class="{{ $className }}"
            type="button"
            data-bs-toggle="dropdown"
            aria-expanded="false"
            aria-label="Actions"
        >
            <i class="bi bi-three-dots"></i>
        </button>

        <ul class="dropdown-menu dropdown-menu-end">

            @foreach ($actions as $item)

                <li>
                    <button
                        class="dropdown-item{{ !empty($item['danger']) ? ' text-danger' : '' }}{{ !empty($item['className']) ? ' ' . $item['className'] : '' }}"
                        type="button"
                        data-{{ $attribute }}-action="{{ $item['action'] ?? '' }}"
                        @if ($id !== null)
                            data-{{ $attribute }}-id="{{ $id }}"
                        @endif
                    >
                        @if (!empty($item['icon']))
                            <i class="bi bi-{{ $item['icon'] }} me-1"></i>
                        @endif

                        {{ $item['label'] ?? '' }}
                    </button>
                </li>

            @endforeach

        </ul>

    </div>

@else

    <button
        class="{{ $className }}{{ $danger ? ' text-danger' : '' }}"
        type="button"
        data-{{ $attribute }}-action="{{ $action }}"
        @if ($id !== null)
            data-{{ $attribute }}-id="{{ $id }}"
        @endif
    >
        @if ($icon)
            <i class="bi bi-{{ $icon }} me-1"></i>
        @endif

        {{ $label }}
    </button>

@endif