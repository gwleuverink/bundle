<x-layout>

    <x-import module="~/alert" as="alert" />
    {{-- <x-import module="~/alert" as="alert" inline /> --}}

    <script type="module">
        var module = await _bundle('alert');

        module('Hello World!')
    </script>

    Hello World!

</x-layout>
