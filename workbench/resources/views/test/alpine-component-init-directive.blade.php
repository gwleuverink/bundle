<x-layout>

    <script src="//unpkg.com/alpinejs" defer></script>
    <x-bundle import="lodash/filter" as="filter" />

    <div x-data="{
            users: [
                { 'user': 'barney', 'age': 36, 'active': true },
                { 'user': 'fred',   'age': 40, 'active': false }
            ]
        }"

        x-init="
            const filter = await _bundle('filter');
            let filtered = filter(users, o => !o.active)
            $el.innerHTML = JSON.stringify(filtered)
        "
        style="padding: 2em; background-color: red;"
    >
    </div>

    <p>
        Filtering through a list using lodash.
        Imported with per-method approach & used in an Alpine component inside x-init directive.
    </p>

    <p>
        Should render 'Fred' inside a red div
    </p>

</x-layout>
