@once("bundle:$as")
<!--[BUNDLE: {{ $as }} from '{{ $import }}']-->
<script data-bundle="{{ $as }}">
    {!! $bundle !!}
</script>
<!--[ENDBUNDLE]>-->
@else
<!--[SKIPPED: {{ $as }} from '{{ $import }}']-->
@endonce
