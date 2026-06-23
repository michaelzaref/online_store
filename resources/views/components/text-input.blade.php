@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-brand-text/20 focus:border-brand-secondary focus:ring-brand-secondary rounded-md shadow-sm']) }}>
