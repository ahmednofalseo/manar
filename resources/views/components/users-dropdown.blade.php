@props(['name' => 'user_id', 'selected' => null, 'required' => false, 'placeholder' => 'اختر الموظف', 'multiple' => false, 'class' => ''])

@php
    $users = \App\Models\User::where('status', 'active')->orderBy('name')->get();
    $hasError = $errors->has($name);
@endphp

<select 
    name="{{ $name }}{{ $multiple ? '[]' : '' }}"
    {{ $required ? 'required' : '' }}
    {{ $multiple ? 'multiple' : '' }}
    class="w-full bg-white/5 border rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 {{ $hasError ? 'border-red-500 bg-red-500/10 focus:ring-red-500/40' : 'border-white/10 focus:ring-primary-400/40' }} {{ $class }}"
>
    <option value="">{{ $placeholder }}</option>
    @foreach($users as $user)
        @if($multiple)
            <option value="{{ $user->id }}" {{ $selected && in_array($user->id, (array)$selected) ? 'selected' : '' }}>
                {{ $user->name }}@if($user->job_title) - {{ $user->job_title }}@endif
            </option>
        @else
            <option value="{{ $user->id }}" {{ $selected == $user->id ? 'selected' : '' }}>
                {{ $user->name }}@if($user->job_title) - {{ $user->job_title }}@endif
            </option>
        @endif
    @endforeach
</select>

