<x-layout>

    <x-import module="~/output-to-id" as="output" />
    <x-import module="lodash/filter" as="filter" />

    <script type="module">
        const filter = await _import('filter');
        const output = await _import('output');

        let data = [
            { 'name': 'Foo', 'active': false },
            { 'name': 'Wello World!', 'active': true }
        ];

        // Filter only active
        let filtered = filter(data, o => o.active)

        output('output', filtered[0].name)
    </script>

    <div id="output"></div>

</x-layout>
