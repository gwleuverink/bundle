<?php // @codeCoverageIgnoreStart ?>
@once("bundle:$as")
<!--[BUNDLE: {{ $as }} from '{{ $module }}']-->
@if ($inline)
<script data-bundle="{{ $as }}" type="module" {{ $attributes }}>
    {!! file_get_contents($bundle) !!}
</script>
@else
<script src="{{ route('bundle:import', $bundle->getFilename(), false) }}" data-bundle="{{ $as }}" {{ $attributes }}></script>
@endif
<!--[ENDBUNDLE]>-->
@else {{-- @once else clause --}}
<!--[SKIPPED: {{ $as }} from '{{ $module }}']-->
@endonce
<?php // @codeCoverageIgnoreEnd ?>
