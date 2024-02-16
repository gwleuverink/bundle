export function dd(output) {
    console.error(output);
    process.exit(1);
}

export function error(id, output = '') {
    console.error({
        id: 'bundle:' + id,
        output
    });

    process.exit(1);
}
