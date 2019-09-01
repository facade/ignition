{{-- Intentional typo for test --}}

This is some contents

<footer>{{ $footerDescriptin['hi'] }}</footer>

@isset($something)
    {{ $something }}

    @if ($something)
        <!-- $footerDescriptin -->
    @endif
@endisset

Test
