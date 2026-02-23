<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Lembar Jawab</title>
</head>

<body class="bg-gray-100 flex flex-col items-center justify-center min-h-screen">
    <h1 class="text-2xl font-semibold mb-12">Aplikasi Lembar Jawab Siswa</h1>

    <div class="bg-white py-16 rounded-xl shadow-lg text-center w-full max-w-sm">
    <div class="mb-8">
        <a href="{{ route('download.apk.filename', 'assessment_full.apk') }}"
           class="bg-blue-600 text-white px-6 py-3 rounded-lg text-lg font-medium hover:bg-blue-700 transition">
            Download
        </a>
        <br>
        <div class="mt-3">
            (Ukuran : 49 MB. Untuk Versi Full)
        </div>
    </div>
    <br>

    <div class="mb-8">
        <a href="{{ route('download.apk.filename', 'assessment_9_15.apk') }}"
           class="bg-blue-600 text-white px-6 py-3 rounded-lg text-lg font-medium hover:bg-blue-700 transition">
            Download
        </a>
        <br>
        <div class="mt-3">
            (Ukuran : 18 MB. Untuk Versi Android Baru)
        </div>
    </div>
    <br>
    <div class="">
        <a href="{{ route('download.apk.filename', 'assessment_1_8.apk') }}"
           class="bg-blue-600 text-white px-6 py-3 rounded-lg text-lg font-medium hover:bg-blue-700 transition">
            Download
        </a>
        <br>
        <div class="mt-3">
            (Ukuran : 15 MB. Untuk Versi Android Lama)
        </div>
    </div>
    </div>
</body>
</html>