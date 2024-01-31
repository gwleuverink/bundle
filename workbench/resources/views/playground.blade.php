<x-layout>

    <x-import module="~/bootstrap/alpine" />
    <x-import module="~/invokes-callable" as="invoke" />

    <script type="module">
        const invoke = await _import('invoke');

        invoke(
            () => document.getElementById('output').innerHTML = 'Hello World!'
        )
    </script>

    <div id="output"></div>

</x-layout>
