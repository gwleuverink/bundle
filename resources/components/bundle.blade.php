@once("bundle:$as")
<!--[BUNDLE: {{ $as }} from '{{ $import }}']-->
@if($inline)
<script data-bundle="{{ $as }}" {{ $attributes }}>
    {!! file_get_contents($bundle) !!}
</script>
@else
<script src="{{ route('x-bundle', $bundle->getFilename(), false) }}" data-bundle="{{ $as }}" {{ $attributes }}></script>
@endif
<!--[ENDBUNDLE]>-->
@else {{-- @once else clause --}}
<!--[SKIPPED: {{ $as }} from '{{ $import }}']-->
@endonce
