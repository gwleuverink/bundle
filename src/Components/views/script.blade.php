<?php // @codeCoverageIgnoreStart ?>
@once("bundle:$module:$as")
<?php if($inline) { ?>
<script data-module="{{ $module }}" data-alias="{{ $as }}" type="module">
    {!! file_get_contents($bundle) !!}
</script>
<?php } else { ?>
<script src="{{ route('bundle:import', $bundle->getFilename(), false) }}" data-module="{{ $module }}" data-alias="{{ $as }}" type="module"></script>
<?php } ?>
@else {{-- @once else clause --}}
<!--[SKIPPED: {{ $as }} from '{{ $module }}']-->
@endonce
<?php // @codeCoverageIgnoreEnd ?>
