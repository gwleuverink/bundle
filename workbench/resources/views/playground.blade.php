<x-layout>

    <x-import module="~/alert" as="alert" />
    <x-import module="~/function-is-evaluated" as="foo-bar" />

    {{-- <x-import module="~/alert" as="alert" inline /> --}}

    <script type="module">
        var module = await _import('alert');

        module('Hello World!')
    </script>

    Hello World!

</x-layout>
