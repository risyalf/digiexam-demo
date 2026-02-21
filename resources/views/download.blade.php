<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Lembar Jawab</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white py-16 rounded-xl shadow-lg text-center w-full max-w-sm">
        <h1 class="text-2xl font-semibold mb-12">Aplikasi Lembar Jawab Siswa</h1>

        <a href="{{ route('download.apk') }}"
           class="bg-blue-600 text-white px-6 py-3 rounded-lg text-lg font-medium hover:bg-blue-700 transition">
            Download
        </a>
    </div>
</body>
</html>