<x-layout>

    <x-bundle import="~/alert" as="alert" /> {{-- Should be rendered --}}
    <x-bundle import="~/alert" as="alert" /> {{-- Should be skipped --}}

    <script type="module">
        var module = await _bundle('alert');

        module('Hello World!')
    </script>

    Hello World!

</x-layout>
