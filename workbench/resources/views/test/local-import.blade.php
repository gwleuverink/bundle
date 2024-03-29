<x-layout>

    <x-import module="~/alert" as="alert" /> {{-- Should be rendered --}}
    <x-import module="~/alert" as="alert" /> {{-- Should be skipped --}}

    <script type="module">
        var module = await _import('alert');

        module('Hello World!')
    </script>

    <p>
        Importing a simple local module by it's path alias as defined in jsconfig.json
    </p>

    <p>
        Should alert 'Hello World!'
    </p>

</x-layout>
