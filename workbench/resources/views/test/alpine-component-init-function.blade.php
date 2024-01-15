<x-layout>

    <script src="//unpkg.com/alpinejs" defer></script>
    <x-import module="lodash/filter" as="filter" />

    <div x-data="{
            users: [
                { 'user': 'barney', 'age': 36, 'active': true },
                { 'user': 'fred',   'age': 40, 'active': false }
            ],

            async init() {
                const filter = await _import('filter');
                let filtered = filter(this.users, o => o.active)
                $el.innerHTML = JSON.stringify(filtered)
            }
        }"
        style="padding: 2em; background-color: skyblue;"
    >
    </div>

    <p>
        Filtering through a list using lodash.
        Imported with per-method approach & used in an Alpine component inside the init() function.
    </p>

    <p>
        Should render 'Barney' inside a blue div
    </p>

</x-layout>
