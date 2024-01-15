<x-layout>

    <x-import module="lodash/filter" as="filter" />

    <script type="module">
        var filter = await _import('filter');

        var users = [
            { 'user': 'barney', 'age': 36, 'active': true },
            { 'user': 'fred',   'age': 40, 'active': false }
        ];

        alert(
            JSON.stringify(filter(users, o => o.active))
        )
    </script>

    <p>
        Filtering through a list using lodash.
        Imported from node_modules by per-method approach `import filter from lodash/filter`.
    </p>

    <p>
        Should alert object containing 'Barney'
    </p>

</x-layout>
