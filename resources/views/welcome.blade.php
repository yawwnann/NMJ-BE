{{-- resources/views/welcome.blade.php --}}

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NMJ Company Profile</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="m-0 font-sans bg-gray-100 antialiased">

    <x-navbar />

    <main class="container mx-auto px-4 py-8">
        <x-banner-slider />
    </main>

    <div class="p-8 text-center bg-white mt-5 min-h-screen">
        <h1 class="text-3xl font-bold text-gray-800 mb-4">Selamat Datang di NMJ Company Profile</h1>
        <p class="text-gray-600">Ini adalah contoh layout halaman utama.</p>
    </div>

</body>

</html>