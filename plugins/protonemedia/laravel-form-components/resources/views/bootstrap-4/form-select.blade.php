<div class="form-group {{ $col }} {{ $errors->has($name) ? 'has-error' : '' }}">
    <x-form-label :label="$label" :for="$attributes->get('id') ?: $id()" />

    @if((!empty($prepend) or !empty($append)))
    <div class="input-group">
    @endif

    @if(!empty($prepend))
        <label class="input-group-text" for="{{ $id }}">{{ __($prepend) }}</label>
    @endif

    <select
        @if($isWired())
            wire:model{!! $wireModifier() !!}="{{ $name }}"
        @endif

        name="{{ $name }}"

        @if($multiple)
            multiple
        @endif

        data-placeholder="{{ $placeholder }}"

        @if($label && !$attributes->get('id'))
            id="{{ $id() }}"
        @endif

        @php
        $class = 'form-control';
        if($api){
            $class = $class.' '.$api;
        }
        if($search){
            $class = $class.' search';
        }
        // Add dropdown icon style class
        $class = $class.' dropdown-select';
        @endphp

        {!! $attributes->merge([
            'class' => $class,
        ]) !!}>

        @if(!isset($placeholder) || $placeholder === false || $placeholder === '')
            <option value="" disabled selected>{{ __('- Select ' . ($label ?? 'Option') . ' -') }}</option>
        @endif

        @forelse($options as $key => $option)
            <option value="{{ $key }}" @if($isSelected($key)) selected="selected" @endif>
                {{ $option }}
            </option>
        @empty
            {!! $slot !!}
        @endforelse
    </select>

    <style>
    select.form-control.dropdown-select {
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 16px 16px;
        padding-right: 40px !important;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }

    select.form-control.dropdown-select:focus {
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='18,15 12,9 6,15'%3e%3c/polyline%3e%3c/svg%3e");
    }
    </style>

    @if(!empty($append))
        <label class="input-group-text" for="{{ $id }}">{{ __($append) }}</label>
    @endif

	@if((!empty($prepend) or !empty($append)))
	</div>
	@endif

    {!! $help ?? null !!}

    @if($hasErrorAndShow($name))
        <x-form-errors :name="$name" />
    @endif

    @if($search)
    @push('javascript')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Check if TomSelect is loaded
                if (typeof window.TomSelect === 'undefined') {
                    console.warn('TomSelect library is not loaded. Please include tom-select.js');
                    return;
                }

                // Initialize TomSelect for this select element
                const selectElement = document.getElementById('{{ $id }}');
                if (selectElement && !selectElement.dataset.tomselectInitialized) {
                    selectElement.dataset.tomselectInitialized = 'true';

                    try {
                        new TomSelect(selectElement, {
                            create: false,
                            sortField: {
                                field: "text",
                                direction: "asc"
                            },
                            placeholder: selectElement.getAttribute('data-placeholder') || 'Choose an option',
                            allowEmptyOption: true,
                            maxOptions: 1000,
                            plugins: {
                                dropdown_input: {},
                                remove_button: {
                                    title: 'Remove this item',
                                }
                            }
                        });
                    } catch (error) {
                        console.error('Error initializing TomSelect:', error);
                    }
                }
            });
        </script>
    @endpush
    @endif

    @if($search)
    @push('javascript')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Get all select elements with the .search class
                const searchSelects = document.querySelectorAll('select.search');

                searchSelects.forEach(function(select) {
                    // Add input event listener for filtering
                    select.addEventListener('input', function() {
                        const filterText = this.value.toLowerCase();
                        const options = this.querySelectorAll('option');

                        // Show/hide options based on filter text
                        options.forEach(function(option) {
                            const optionText = option.text.toLowerCase();

                            // Keep the first option (empty/placeholder) always visible
                            if (option === options[0] || optionText.includes(filterText)) {
                                option.style.display = '';
                            } else {
                                option.style.display = 'none';
                            }
                        });

                        // Reset selection if current selection doesn't match filter
                        if (filterText && this.selectedOptions[0] && this.selectedOptions[0].style.display === 'none') {
                            this.selectedIndex = 0;
                        }
                    });

                    // Add change event listener to ensure valid selection
                    select.addEventListener('change', function() {
                        const selectedOption = this.selectedOptions[0];

                        // If selected option is hidden (filtered out), reset to first option
                        if (selectedOption && selectedOption.style.display === 'none') {
                            this.selectedIndex = 0;
                        }
                    });
                });
            });
        </script>
    @endpush
    @endif
</div>