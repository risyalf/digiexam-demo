<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">

    <style>
        @page {
            size: A4 portrait;
            margin: 10mm;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .page {
            width: 190mm;
            height: 277mm;

            display: grid;
            grid-template-columns: repeat(2, 95mm);
            grid-template-rows: repeat(5, 55mm);

            page-break-after: always;
        }

        .card {
            border: 1px solid #000;
            padding: 3mm;
            box-sizing: border-box;

            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .header {
            font-size: 10px;
            font-weight: bold;
            text-align: center;
        }

        .content {
            display: flex;
            gap: 3mm;
        }

        .photo {
            width: 20mm;
            height: 25mm;
            border: 1px solid #000;
            object-fit: cover;
        }

        .info {
            font-size: 9px;
            flex: 1;
        }

        .footer {
            font-size: 8px;
        }
    </style>

</head>

<body>

    @foreach ($participants->chunk(10) as $pageParticipants)
        <div class="page">

            @foreach ($pageParticipants as $participant)
                <div class="card">

                    <div class="header">
                        KARTU LOGIN<br>
                        TES KEMAMPUAN AKADEMIK SMK 2025
                    </div>

                    <div class="content">

                        <div class="info">

                            Nama: {{ $participant->user->name }}<br>
                            Kelas: {{ $groupName }}<br>
                            No. Test: {{ $participant->test_number }}<br>
                            Password: {{ $participant->test_password }}</b>

                        </div>

                    </div>

                </div>
            @endforeach

        </div>
    @endforeach

</body>

</html>
