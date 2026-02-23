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
            display: flex;
            flex-wrap: wrap;

            page-break-after: always;
        }

        .card {
            border: 1px solid #000;
            box-sizing: border-box;
            margin-bottom: 1mm;

            display: inline-block;
            justify-content: space-between;
            width: 46%;
        }

        .header {
            font-size: 10px;
            font-weight: bold;
            text-align: center;
            padding: 3mm 0;
            border-bottom-color: #000;
            border-bottom-width: 2px;
            border-bottom-style: solid
        }

        .content {
            display: flex;
            gap: 3mm;
            padding: 3mm;
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

            @foreach ($pageParticipants as $key => $participant)
                <div class="card">

                    <div class="header">
                        KARTU LOGIN<br>
                        {{ $moduleName }}
                    </div>

                    <div class="content">

                        <div class="info">
                            <table>
                                <tr>
                                    <td>Nama</td>
                                    <td>:</td>
                                    <td>{{ $participant->user->name }}</td>
                                </tr>
                                <tr>
                                    <td>Nomor Induk Siswa</td>
                                    <td>:</td>
                                    <td>{{ $participant->user->nis }}</td>
                                </tr>
                                <tr>
                                    <td>Kelas</td>
                                    <td>:</td>
                                    <td>{{ $participant->participantGroup->name }}</td>
                                </tr>
                                <tr>
                                    <td>Username</td>
                                    <td>:</td>
                                    <td>{{ $participant->test_number }}</td>
                                </tr>
                                <tr>
                                    <td>Password</td>
                                    <td>:</td>
                                    <td>{{ $participant->test_password }}</td>
                                </tr>
                            </table>

                        </div>

                    </div>

                </div>
            @endforeach

        </div>
    @endforeach

</body>

</html>
