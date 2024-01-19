<?php // @codeCoverageIgnoreStart ?>
@once("bundle:$module:$as")
<!--[BUNDLE: {{ $as }} from '{{ $module }}']-->
@if ($inline)
<script data-module="{{ $module }}" data-alias="{{ $as }}" type="module" {{ $attributes }}>
    {!! file_get_contents($bundle) !!}
</script>
@else
<script src="{{ route('bundle:import', $bundle->getFilename(), false) }}" data-module="{{ $module }}" data-alias="{{ $as }}" type="module" {{ $attributes }}></script>
@endif
<!--[ENDBUNDLE]>-->
@else {{-- @once else clause --}}
<!--[SKIPPED: {{ $as }} from '{{ $module }}']-->
@endonce
<?php // @codeCoverageIgnoreEnd ?>
