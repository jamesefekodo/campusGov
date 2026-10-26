@extends('layouts.userLayout')

@section('title', 'Frontend Theme')

@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4 mb-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 individual-review-card-container">
                    <div class="col-md-12 individual-review-card">
                        <div class="col-md-12 m-auto">
                            <div class="my_form_container pb-5">
                                <h4 class="block text-center py-2 text-uppercase type-b-page-header">
                                    Frontend Theme
                                </h4>

                                <div class="row g-4 mb-5 p-4">
                                    @foreach ($data['themes'] as $theme)
                                        @php
                                            $isActive = $data['activeTheme'] &&
                                                $data['activeTheme']->id === $theme->id;
                                        @endphp

                                        <div class="col-md-6 col-lg-4">
                                            <div class="card h-100 shadow-sm border-0 theme-card">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                                        <div>
                                                            <h5 class="mb-1">
                                                                {{ $theme->name }}
                                                            </h5>

                                                            <small class="text-muted">
                                                                {{ $theme->description }}
                                                            </small>
                                                        </div>

                                                        @if ($isActive)
                                                            <span class="badge bg-primary">
                                                                Active
                                                            </span>
                                                        @endif
                                                    </div>

                                                    <div class="d-flex gap-2 mb-3">
                                                        @foreach (['primary', 'secondary', 'accent', 'background'] as $colorKey)
                                                            <div class="theme-color-preview"
                                                                style="background: {{ $theme->default_colors[$colorKey] ?? '#000000' }};"
                                                                title="{{ ucfirst($colorKey) }}">
                                                            </div>
                                                        @endforeach
                                                    </div>

                                                    @if ($isActive)
                                                        <div class="d-grid gap-2">
                                                            <button class="btn btn-secondary w-100" disabled>
                                                                Current Theme
                                                            </button>

                                                            <form method="POST"
                                                                action="{{ route('frontendThemes.reset', $theme) }}">
                                                                @csrf

                                                                <button class="btn btn-outline-secondary w-100">
                                                                    Reset to {{ $theme->name }} Default
                                                                </button>
                                                            </form>
                                                        </div>
                                                    @else
                                                        <form method="POST"
                                                            action="{{ route('frontendThemes.select', $theme) }}">
                                                            @csrf

                                                            <button class="btn btn-primary w-100">
                                                                Use This Theme
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                @if ($data['activeTheme'])
                                    <div class="card shadow-sm border-0">
                                        <div class="card-header bg-white">
                                            <h5 class="mb-0">
                                                Customize {{ $data['activeTheme']->name }} Colors
                                            </h5>
                                        </div>

                                        <div class="card-body">
                                            <form method="POST"
                                                action="{{ route('frontendThemes.updateColors') }}">
                                                @csrf

                                                <div class="row g-3">
                                                    @foreach ([
                                                        'primary' => 'Primary Color',
                                                        'secondary' => 'Secondary Color',
                                                        'accent' => 'Accent Color',
                                                        'background' => 'Background Color',
                                                    ] as $key => $label)
                                                        @php
                                                            $color = trim($data['activeColors'][$key] ?? '#000000');

                                                            if (preg_match('/^#([0-9a-f]{3})$/i', $color, $matches)) {
                                                                $digits = $matches[1];

                                                                $color = '#' .
                                                                    $digits[0] . $digits[0] .
                                                                    $digits[1] . $digits[1] .
                                                                    $digits[2] . $digits[2];
                                                            }

                                                            if (!preg_match('/^#[0-9a-f]{6}$/i', $color)) {
                                                                $color = '#000000';
                                                            }

                                                            $color = strtoupper($color);
                                                        @endphp

                                                        <div class="col-md-6 col-lg-3">
                                                            <label for="theme-hex-{{ $key }}" class="form-label">
                                                                {{ $label }}
                                                            </label>

                                                            <div class="theme-color-pair" data-theme-color-pair>
                                                                <input type="color"
                                                                    value="{{ strtolower($color) }}"
                                                                    class="theme-color-swatch"
                                                                    aria-label="{{ $label }} selector"
                                                                    data-theme-picker>

                                                                <input type="text"
                                                                    id="theme-hex-{{ $key }}"
                                                                    name="color_overrides[{{ $key }}]"
                                                                    value="{{ $color }}"
                                                                    class="form-control theme-hex-input"
                                                                    placeholder="#006A4E"
                                                                    maxlength="7"
                                                                    autocomplete="off"
                                                                    spellcheck="false"
                                                                    required
                                                                    data-theme-hex>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                <button class="btn btn-primary mt-4">
                                                    Save Custom Colors
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .theme-card {
                border-radius: 1rem;
                transition: all 0.25s ease;
            }

            .theme-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
            }

            .theme-color-preview {
                width: 38px;
                height: 38px;
                border-radius: 10px;
                border: 1px solid rgba(0, 0, 0, 0.1);
            }

            .theme-color-pair {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .theme-color-pair .theme-color-swatch {
                display: block;
                flex: 0 0 48px;
                width: 48px;
                height: 42px;
                margin: 0;
                padding: 3px;
                border: 1px solid #ced4da;
                border-radius: 7px;
                background: #fff;
                cursor: pointer;
            }

            .theme-color-pair .theme-hex-input {
                flex: 1;
                width: 100%;
                min-width: 0;
                height: 42px;
                font-family: monospace;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            (() => {
                if (window.themeHexSyncInstalled) return;
                window.themeHexSyncInstalled = true;

                function normalizeHex(value) {
                    let digits = value.trim().replace(/^#/, '');

                    if (/^[0-9a-f]{3}$/i.test(digits)) {
                        digits = digits.split('').map(char => char + char).join('');
                    }

                    return /^[0-9a-f]{6}$/i.test(digits)
                        ? '#' + digits.toUpperCase()
                        : null;
                }

                function synchronize(target, finalChange) {
                    if (!(target instanceof HTMLInputElement)) return;

                    const pair = target.closest('[data-theme-color-pair]');
                    if (!pair) return;

                    const picker = pair.querySelector('[data-theme-picker]');
                    const hex = pair.querySelector('[data-theme-hex]');

                    if (target === picker) {
                        hex.value = picker.value.toUpperCase();
                        hex.setCustomValidity('');
                        return;
                    }

                    if (target !== hex) return;

                    const value = normalizeHex(hex.value);

                    if (value) {
                        picker.value = value.toLowerCase();
                        hex.setCustomValidity('');

                        if (finalChange) {
                            hex.value = value;
                        }
                    } else {
                        hex.setCustomValidity(
                            'Enter a HEX color such as #006A4E or #FFF.'
                        );
                    }
                }

                document.addEventListener('input', event => {
                    synchronize(event.target, false);
                });

                document.addEventListener('change', event => {
                    synchronize(event.target, true);
                });

                document.addEventListener('focusout', event => {
                    if (event.target.matches?.('[data-theme-hex]')) {
                        synchronize(event.target, true);
                    }
                });

                document.addEventListener('submit', event => {
                    const form = event.target;

                    if (!(form instanceof HTMLFormElement)) return;

                    const inputs = form.querySelectorAll('[data-theme-hex]');

                    for (const hex of inputs) {
                        const value = normalizeHex(hex.value);

                        if (!value) {
                            event.preventDefault();
                            hex.setCustomValidity(
                                'Enter a HEX color such as #006A4E or #FFF.'
                            );
                            hex.reportValidity();
                            return;
                        }

                        hex.value = value;
                        hex.setCustomValidity('');

                        const pair = hex.closest('[data-theme-color-pair]');
                        pair.querySelector('[data-theme-picker]').value =
                            value.toLowerCase();
                    }
                });
            })();
        </script>
    @endpush
@endsection