@props(['id' => '', 'text' => 'Submit', 'type' => 'submit'])

<button id="{{ $id ?? $name }}" class="bg-primary px-4 py-2 rounded-md text-sm font-medium text-background cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed active:scale-98 duration-100 transition-transform" type="{{ $type }}">
    {{ $text }}
</button>