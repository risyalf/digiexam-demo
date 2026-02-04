<?php

namespace App\Action;

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

class ExportTestFormDocx
{
    public static function execute($fileName)
    {
        return response()->streamDownload(function () {
            $styleBorderTopCell = [
                'borderTopSize' => 12,
                'borderTopColor' => '000000',
            ];

            $styleBorderBottomCell = [
                'borderBottomSize' => 12,
                'borderBottomColor' => '000000',
            ];

            $styleBorderLeftRightCell = [
                'borderLeftSize' => 12,
                'borderLeftColor' => '000000',
                'borderRightSize' => 12,
                'borderRightColor' => '000000',
            ];

            $jumlahSoal = 50;
            $phpWord = new PhpWord;
            $section = $phpWord->addSection();
            $table = $section->addTable();

            $table->addRow();
            $table->addCell(1000, [...$styleBorderTopCell, ...$styleBorderLeftRightCell])->addText("NO.");
            $table->addCell(2000, [...$styleBorderTopCell, ...$styleBorderLeftRightCell])->addText("JENIS");
            $table->addCell(4000, [...$styleBorderTopCell, ...$styleBorderLeftRightCell])->addText("ISI");
            $table->addCell(2000, [...$styleBorderTopCell, ...$styleBorderLeftRightCell])->addText("JAWABAN");

            for ($i = 1; $i <= $jumlahSoal; $i++) {

                $table->addRow();
                $table->addCell(1000, [...$styleBorderTopCell, ...$styleBorderLeftRightCell])->addText($i);
                $table->addCell(2000, [...$styleBorderTopCell, ...$styleBorderLeftRightCell])->addText("PILIHAN GANDA / ESSAY");
                $table->addCell(4000, [...$styleBorderTopCell, ...$styleBorderLeftRightCell])->addText($i == 1 ? "(MASUKKAN PERTANYAAN DAN GAMBAR DI SINI)" : "");
                $table->addCell(2000, [...$styleBorderTopCell, ...$styleBorderLeftRightCell, 'bgColor' => 'FFFF00'])->addText("");

                $table->addRow();
                $table->addCell(1000, $styleBorderLeftRightCell)->addText("");
                $table->addCell(2000, $styleBorderLeftRightCell)->addText("JAWABAN");
                $table->addCell(4000, $styleBorderLeftRightCell)->addText($i == 1 ? "(MASUKKAN OPSI PERTANYAAN ATAU KOSONGI)" : "");
                $table->addCell(2000, $styleBorderLeftRightCell)->addText($i == 1 ? "(MASUKKAN ANGKA '1' JIKA JAWABAN BENAR, '0' JIKA SALAH)" : "");

                $table->addRow();
                $table->addCell(1000, $styleBorderLeftRightCell)->addText("");
                $table->addCell(2000, $styleBorderLeftRightCell)->addText("JAWABAN");
                $table->addCell(4000, $styleBorderLeftRightCell)->addText($i == 1 ? "(MASUKKAN OPSI PERTANYAAN ATAU KOSONGI)" : "");
                $table->addCell(2000, $styleBorderLeftRightCell)->addText($i == 1 ? "(MASUKKAN ANGKA '1' JIKA JAWABAN BENAR, '0' JIKA SALAH)" : "");

                $table->addRow();
                $table->addCell(1000, $styleBorderLeftRightCell)->addText("");
                $table->addCell(2000, $styleBorderLeftRightCell)->addText("JAWABAN");
                $table->addCell(4000, $styleBorderLeftRightCell)->addText($i == 1 ? "(MASUKKAN OPSI PERTANYAAN ATAU KOSONGI)" : "");
                $table->addCell(2000, $styleBorderLeftRightCell)->addText($i == 1 ? "(MASUKKAN ANGKA '1' JIKA JAWABAN BENAR, '0' JIKA SALAH)" : "");

                $table->addRow();
                $table->addCell(1000, [...$styleBorderBottomCell, ...$styleBorderLeftRightCell])->addText("");
                $table->addCell(2000, [...$styleBorderBottomCell, ...$styleBorderLeftRightCell])->addText("JAWABAN");
                $table->addCell(4000, [...$styleBorderBottomCell, ...$styleBorderLeftRightCell])->addText($i == 1 ? "(MASUKKAN OPSI PERTANYAAN ATAU KOSONGI)" : "");
                $table->addCell(2000, [...$styleBorderBottomCell, ...$styleBorderLeftRightCell])->addText($i == 1 ? "(MASUKKAN ANGKA '1' JIKA JAWABAN BENAR, '0' JIKA SALAH)" : "");
            }

            $writer = IOFactory::createWriter($phpWord);
            $writer->save('php://output');
        }, $fileName);
    }
}
