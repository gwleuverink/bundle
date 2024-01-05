@once
<!--[BUNDLE: {{ $as }} from '{{ $import }}']-->
<script>
    {!! $bundle !!}
</script>
<!--[ENDBUNDLE]>-->
@else
<!--[SKIPPED: {{ $as }} from '{{ $import }}']-->
@endonce
