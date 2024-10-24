<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="icon" type="image/png" href="data:image/png;base64,iVBORw0KGgo=">

    @unless(app()->runningInConsole())
    <script src="https://cdn.tailwindcss.com"></script>
    @endunless
</head>
<body class="p-4">

{{ $slot }}

</body>
</html>
