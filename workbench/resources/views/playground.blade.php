<x-layout>

    <x-bundle import="~/alert" as="alert" />
    {{-- <x-bundle import="~/alert" as="alert" inline /> --}}

    <script type="module">
        var module = await _bundle('alert');

        module('Hello World!')
    </script>

    Hello World!

</x-layout>
