<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>Document</title>
</head>

<body class="h-full">
    {{ $slot }}

@vite(['resources/js/firebase.js' , 'resources/js/login.js'])
</body>
</html>
