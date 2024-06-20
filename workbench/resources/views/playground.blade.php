<x-layout>

    <x-import module="~/bootstrap/alpine-iife-with-plugin" />

    <h1
        id="component"
        x-text="message"
        x-data="{
            message: typeof Alpine.persist === 'function'
                ? 'Plugin loaded!'
                : 'Test failed'
        }"
    ></h1>

</x-layout>
