<x-layout>

    <x-import module="lodash" as="lodash" />

    <script type="module">
        var filter = await _import('lodash', 'filter');

        //
    </script>

    <h1 class="text-2xl">Playground</h1>

    <p>
        Edit to start tinkering ✨
    </p>

    @php
        $filePath = \Orchestra\Testbench\Foundation\Env::get('TESTBENCH_WORKING_PATH', getcwd()) . '/workbench/resources/views/playground.blade.php';
    @endphp

    <code class="text-sm bg-slate-200 inline-block my-1 px-1.5 py-0.5 rounded select-all cursor-pointer">
        {{ $filePath }}
    </code>

    <div class="text-xs max-w-6xl my-8 p-4 rounded bg-slate-100 overflow-x-scroll">
        <code class="whitespace-pre-wrap w-[1000%]">{{ file_get_contents($filePath) }}</code>
    </div>

</x-layout>
