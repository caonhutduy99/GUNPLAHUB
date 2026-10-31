@props([
    'name',
    'size' => '1em',
])

<span
    {{ $attributes->merge([
        'class' => 'gh-icon',
        'aria-hidden' => 'true',
    ]) }}
    style="--gh-icon-size: {{ $size }}; --gh-icon-url: url('{{ asset('icons/'.$name.'.svg') }}');"
></span>
