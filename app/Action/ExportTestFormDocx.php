<?php

namespace App\Action;

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\TextAlignment;

class ExportTestFormDocx
{
    public static function execute($fileName)
    {
        return response()->streamDownload(function () {
            $styleBorderTopCell = ['borderTopSize' => 12, 'borderTopColor' => '000000',];
            $styleBorderBottomCell = ['borderBottomSize' => 12, 'borderBottomColor' => '000000',];
            $styleBorderLeftRightCell = ['borderLeftSize' => 12, 'borderLeftColor' => '000000', 'borderRightSize' => 12, 'borderRightColor' => '000000',];

            $centeredPara = ['alignment' => Jc::CENTER];
            $valignCenter = ['valign' => 'center'];

            $countQuestion = 50;
            $countQuestionOption = 5;
            $phpWord = new PhpWord;
            $section = $phpWord->addSection();
            $section->addText("Form Import Soal Pilihan Ganda berdasarkan Topik yang dipilih", [
                'bold' => true                
            ], [
                'alignment' => Jc::CENTER,
                'textAlignment' => TextAlignment::CENTER
            ]);
            $section->addTextBreak(1);
            $section->addText("MASUKKAN ANGKA '1' JIKA JAWABAN BENAR, '0' ATAU KOSONGI JIKA SALAH");
            $section->addTextBreak(1);

            $table = $section->addTable();

            $table->addRow();
            $table->addCell(1000, [...$styleBorderTopCell, ...$styleBorderLeftRightCell])->addText("NO.");
            $table->addCell(2000, [...$styleBorderTopCell, ...$styleBorderLeftRightCell])->addText("JENIS");
            $table->addCell(4000, [...$styleBorderTopCell, ...$styleBorderLeftRightCell])->addText("ISI");
            $table->addCell(2000, [...$styleBorderTopCell, ...$styleBorderLeftRightCell])->addText("JAWABAN");

            for ($i = 1; $i <= $countQuestion; $i++) {

                $table->addRow();
                $table->addCell(1000, [...$styleBorderTopCell, ...$styleBorderLeftRightCell, ...$valignCenter, 'vMerge' => 'restart'])
                    ->addText($i, null, $centeredPara);
                $table->addCell(2000, [...$styleBorderTopCell, ...$styleBorderLeftRightCell])->addText("PILIHAN GANDA / ESSAY");
                $table->addCell(4000, [...$styleBorderTopCell, ...$styleBorderLeftRightCell])->addText($i == 1 ? "(MASUKKAN PERTANYAAN DAN GAMBAR DI SINI)" : "");
                $table->addCell(2000, [...$styleBorderTopCell, ...$styleBorderLeftRightCell, 'bgColor' => 'FFFF00'])->addText("");

                for ($j = 1; $j <= $countQuestionOption; $j++) {
                    $currentBorderStyle = [...$styleBorderBottomCell, ...$styleBorderTopCell];

                    $table->addRow();
                    $table->addCell(1000, [...$styleBorderLeftRightCell, ...$currentBorderStyle, 'vMerge' => 'continue']);
                    $table->addCell(2000, [...$styleBorderLeftRightCell, ...$currentBorderStyle])->addText("JAWABAN");
                    $table->addCell(4000, [...$styleBorderLeftRightCell, ...$currentBorderStyle])->addText($i == 1 ? "(MASUKKAN OPSI PERTANYAAN ATAU KOSONGI)" : "");
                    $nilai = "";
                    if ($i == 1) {
                        $nilai = $j == 1 ? 1 : 0;
                    }
                    $table->addCell(2000, [...$styleBorderLeftRightCell, ...$currentBorderStyle])->addText($nilai);
                }
            }

            $writer = IOFactory::createWriter($phpWord);
            $writer->save('php://output');
        }, $fileName);
    }
}
