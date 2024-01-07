<x-layout>

    <x-bundle import="lodash" as="lodash" />

    <script type="module">
        var filter = await _bundle('lodash', 'filter');

        var users = [
            { 'user': 'barney', 'age': 36, 'active': true },
            { 'user': 'fred',   'age': 40, 'active': false }
        ];

        alert(
            JSON.stringify(filter(users, o => !o.active))
        )
    </script>

    <p>
        Filtering through a list using lodash.
        Imported from node_modules by importing lodash in it's entirity & later resolving by named export.
    </p>

    <p>
        Should alert object containing 'Fred'
    </p>

</x-layout>
