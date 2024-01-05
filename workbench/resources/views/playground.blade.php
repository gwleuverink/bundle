<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

    <x-bundle import="~/alert" as="alert" />

    <script type="module">
        var module = await _bundle('alert');

        module('Hello World!')
    </script>

    Hello World!

</body>
</html>
