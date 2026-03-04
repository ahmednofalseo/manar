@props(['name' => 'user_id', 'selected' => null, 'required' => false, 'placeholder' => 'اختر الموظف', 'multiple' => false, 'class' => ''])

@php
    $users = \App\Models\User::where('status', 'active')->orderBy('name')->get();
    $hasError = $errors->has($name);
@endphp

<div class="select-wrapper">
    <select 
        name="{{ $name }}{{ $multiple ? '[]' : '' }}"
        {{ $required ? 'required' : '' }}
        {{ $multiple ? 'multiple' : '' }}
        class="w-full bg-[#173343]/90 border-2 {{ $hasError ? 'border-red-500 bg-red-500/10' : 'border-white/30' }} rounded-lg px-4 py-2 text-white font-semibold focus:outline-none focus:ring-2 {{ $hasError ? 'focus:ring-red-500/40' : 'focus:ring-[#1db8f8] focus:border-[#1db8f8]' }} transition-all duration-200 {{ $class }}"
        style="background-color: rgba(23, 51, 67, 0.9); color: #ffffff; font-weight: 600; max-width: 100%;"
    >
        <option value="" style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">{{ $placeholder }}</option>
        @foreach($users as $user)
            @if($multiple)
                <option value="{{ $user->id }}" {{ $selected && in_array($user->id, (array)$selected) ? 'selected' : '' }} style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">
                    {{ $user->name }}@if($user->job_title) - {{ $user->job_title }}@endif
                </option>
            @else
                <option value="{{ $user->id }}" {{ $selected == $user->id ? 'selected' : '' }} style="background-color: #173343; color: #ffffff; font-weight: 600; padding: 12px;">
                    {{ $user->name }}@if($user->job_title) - {{ $user->job_title }}@endif
                </option>
            @endif
        @endforeach
    </select>
</div>

