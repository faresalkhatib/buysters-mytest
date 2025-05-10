<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>Document</title>
</head>

<body class="lg:flex lg:overflow-auto">
    <!-- sidebar -->
  <x-sidebar/>
  <div class="main-content">
   <!-- navbar -->
      <x-navbar></x-navbar>
          <div class="content">
           {{ $slot }}
          </div>
  </div>
@vite(['resources/js/table.js'])
</body>
</html>
