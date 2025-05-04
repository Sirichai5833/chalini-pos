<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>chalini</title>
    
    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Optional: Custom CSS --}}
    <style>
        body {
            padding-top: 70px;
        }
        .navbar-brand {
            font-weight: bold;
        }
    </style>
</head>
<body>

    {{-- 🔝 Navbar --}}
  
    {{-- 🔻 Main Content --}}
    <div class="container py-4">
        @yield('content')
    </div>

    {{-- 🧩 Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
