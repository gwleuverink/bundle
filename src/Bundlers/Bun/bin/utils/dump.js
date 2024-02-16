export function dd(output) {
    console.error(output);
    process.exit(1);
}

/** Outputs a object to be caught by BundlingFailedException */
export function exit(id, message = "", output = "") {
    console.error(
        JSON.stringify({
            id: "bundle:" + id,
            message,
            output,
        })
    );

    process.exit(1);
}
