@props(['label', 'type' => 'text', 'name', 'placeholder' => '', 'value' => '', 'required' => false, 'id' => ''])

<article>
    <label class="text-sm font-medium">{{ $label }}</label>
    <input type="{{ $type }}" 
           name="{{ $name }}" 
           placeholder="{{ $placeholder }}" 
           value="{{ $value }}" 
           {{ $id ? "id={$id}" : '' }}
           {{ $required ? 'required' : '' }} 
           class="w-full rounded-lg border border-secondary/30 px-3 py-2 text-sm focus:border-primary focus:ring-primary/20" />
</article>