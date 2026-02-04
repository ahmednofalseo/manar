@props(['name', 'label' => null, 'required' => false, 'options' => [], 'selected' => null, 'placeholder' => 'اختر...', 'class' => '', 'error' => null])

@php
    $hasError = $errors->has($name) || $error;
    $errorMessage = $error ?? ($errors->first($name));
@endphp

<div class="select-wrapper {{ $attributes->get('wrapper-class', '') }}">
    @if($label)
    <label class="block text-gray-300 text-sm mb-2">
        {{ $label }}
        @if($required)
        <span class="text-red-400">*</span>
        @endif
    </label>
    @endif
    
    @if($hasError && $errorMessage)
    <p class="mb-2 text-sm text-red-400 flex items-center gap-1">
        <i class="fas fa-exclamation-circle"></i>
        {{ $errorMessage }}
    </p>
    @endif
    
    <select 
        name="{{ $name }}"
        {{ $required ? 'required' : '' }}
        class="w-full {{ $hasError ? 'border-red-500 bg-red-500/10' : '' }} {{ $class }}"
    >
        <option value="">{{ $placeholder }}</option>
        @foreach($options as $key => $value)
        <option 
            value="{{ $key }}" 
            {{ ($selected !== null && $selected == $key) || old($name) == $key ? 'selected' : '' }}
        >
            {{ $value }}
        </option>
        @endforeach
    </select>
    
    @if($hasError && !$errorMessage)
    <p class="mt-1 text-sm text-red-400">{{ $errors->first($name) }}</p>
    @endif
</div>
