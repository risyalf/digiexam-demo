<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Lembar Jawab</title>
</head>

<body class="bg-gray-100 flex flex-col items-center justify-center min-h-screen">
    <h1 class="text-2xl font-semibold mb-12">Aplikasi Lembar Jawab Siswa</h1>

    <br>
    <h1 class="text-xl font-semibold mb-12">Build Baru</h1>
    <div class="bg-white py-16 rounded-xl shadow-lg text-center w-full max-w-sm">
        <div class="mb-8">
            <a href="{{ route('download.apk.new.filename', 'assessment_full.apk') }}"
                class="bg-blue-600 text-white px-6 py-3 rounded-lg text-lg font-medium hover:bg-blue-700 transition">
                Download
            </a>
            <br>
            <div class="mt-3">
                (Ukuran : 49 MB)
            </div>
            <div>
                Versi full, direkomendasikan untuk HP keluaran 2017 ke atas, kompatibel untuk HP Android 6 sampai Android 15
            </div>
            <br>
        </div> 
        <br>

        <div class="mb-8">
            <a href="{{ route('download.apk.new.filename', 'assessment_v_new.apk') }}"
                class="bg-blue-600 text-white px-6 py-3 rounded-lg text-lg font-medium hover:bg-blue-700 transition">
                Download
            </a>
            <br>
            <div class="mt-3">
                (Ukuran : 18 MB)
            </div>
            <div>
                Versi ringan, direkomendasikan untuk semua HP yang rilis di atas 2015, kompatibel untuk HP Android 6 sampai Android 15
            </div>
            <br>
        </div>
        <br>
        <div class="">
            <a href="{{ route('download.apk.new.filename', 'assessment_v_old.apk') }}"
                class="bg-blue-600 text-white px-6 py-3 rounded-lg text-lg font-medium hover:bg-blue-700 transition">
                Download
            </a>
            <br>
            <div class="mt-3">
                (Ukuran : 15 MB)
            </div>
            <div>
                Mendukung Android 5.0 Lollipop dan 5.1, untuk perangkat yang masih memakai HP generasi lama.
            </div>
            <br>
        </div>
        <br>
    </div>
    <br>
    <h1 class="text-xl font-semibold mb-12">Build Lama</h1>
    <div class="bg-white py-16 rounded-xl shadow-lg text-center w-full max-w-sm">
        <div class="mb-8">
            <a href="{{ route('download.apk.old.filename', 'assessment_full.apk') }}"
                class="bg-blue-600 text-white px-6 py-3 rounded-lg text-lg font-medium hover:bg-blue-700 transition">
                Download
            </a>
            <br>
            <div class="mt-3">
                (Ukuran : 49 MB)
            </div>
            <div>
                Versi full, direkomendasikan untuk HP keluaran 2017 ke atas, kompatibel untuk HP Android 6 sampai Android 15
            </div>
            <br>
        </div>
        <br>

        <div class="mb-8">
            <a href="{{ route('download.apk.old.filename', 'assessment_v_new.apk') }}"
                class="bg-blue-600 text-white px-6 py-3 rounded-lg text-lg font-medium hover:bg-blue-700 transition">
                Download
            </a>
            <br>
            <div class="mt-3">
                (Ukuran : 18 MB)
            </div>
            <div>
                Versi ringan, direkomendasikan untuk semua HP yang rilis di atas 2015, kompatibel untuk HP Android 6 sampai Android 15
            </div>
            <br>
        </div>
        <br>
        <div class="">
            <a href="{{ route('download.apk.old.filename', 'assessment_v_old.apk') }}"
                class="bg-blue-600 text-white px-6 py-3 rounded-lg text-lg font-medium hover:bg-blue-700 transition">
                Download
            </a>
            <br>
            <div class="mt-3">
                (Ukuran : 15 MB)
            </div>
            <div>
                Mendukung Android 5.0 Lollipop dan 5.1, untuk perangkat yang masih memakai HP generasi lama.
            </div>
            <br>
        </div>
        <br>
    </div>
</body>

</html>
