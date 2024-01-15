export function foo() {
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('output').innerHTML = 'Foo'
    })
}

export function bar() {
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('output').innerHTML = 'Bar'
    })
}
