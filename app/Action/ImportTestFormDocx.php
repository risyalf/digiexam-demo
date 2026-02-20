<?php

namespace App\Action;

use Exception;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\Element\Image;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\IOFactory;

class ImportTestFormDocx
{
    protected $files = [];

    public static function execute(string $id, string $filePath): array
    {
        $phpWord = IOFactory::load($filePath);
        $finalData = [];

        $questionIsNull = false;

        $countQuestionOption = 5;

        $number = 0;

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if ($element instanceof Table) {
                    $rows = $element->getRows();

                    $rowData = collect($rows)->skip(1);

                    foreach ($rowData->chunk($countQuestionOption + 1) as $chunk) {
                        $questionRow = $chunk->first();
                        $answerRows = $chunk->skip(1);

                        $cells = $questionRow->getCells();

                        $number++;
                        $type = self::getCellQuestion($cells[1] ?? null, $id);
                        $question = self::getCellQuestion($cells[2] ?? null, $id);
                        $answers = $answerRows->map(function ($aRow, $index) use($id) {
                            $aCells = $aRow->getCells();
                            $value = self::getCellQuestion($aCells[3] ?? null, $id);

                            return [
                                'id' => $index,
                                'text'  => self::getCellQuestion($aCells[2] ?? null, $id),
                                'value' => $value === '1' ? 'true' : 'false',
                            ];
                        })->values();

                        if (!$type) {
                            throw new Exception("TERDAPAT JENIS PERTANYAAN YANG KOSONG PADA NOMOR " . $number);
                        }

                        if (!$question) {
                            $questionIsNull = true;
                            continue;
                        }

                        if ($questionIsNull) {
                            throw new Exception("SOAL PADA NOMOR " . (int)$number - 1 . " KOSONG!");
                        }

                        if (!in_array($type, ['PILIHAN GANDA', 'ESSAY'])) {
                            throw new Exception("JENIS PERTANYAAN {$type} SALAH PADA NOMOR " . $number . ". PILIH SALAH SATU : PILIHAN GANDA ATAU ESSAY");
                        }

                        $correctAnswer = false;
                        foreach ($answers as $key => $answer) {
                            if (!$answer['text']) {
                                throw new Exception("ADA JAWABAN YANG KOSONG DI NOMOR " . $number);
                            }
                            $answer['text'] = "<p>".$answer['text']."</p>";
                            if (!$correctAnswer) {
                                $correctAnswer = $answer['value'] == "true";
                            }
                        }

                        if (!$correctAnswer) {
                            throw new Exception("TIDAK ADA JAWABAN BENAR PADA SOAL NOMOR : " . $number);
                        }

                        $finalData[] = [
                            'number'   => $number,
                            'type'     => $type,
                            'question' => "<p>".$question."</p>",
                            'answers'  => $answers->toArray(),
                        ];
                    }
                }
            }
        }

        return $finalData;
    }

    protected static function getCellQuestion($cell, $id): string
    {
        if (!$cell) return '';

        $fullText = "";
        foreach ($cell->getElements() as $key => $element) {
            if ($key != 0) {
                $fullText .= "<br>";
            }
            if ($element instanceof TextRun) {
                $childElements = $element->getElements();
                foreach ($childElements as $key => $element) {
                    if ($element instanceof Image) {
                        $name = uniqid();
    
                        $url = SaveImage::execute($id, $name, $element->getImageString(), strtolower($element->getImageExtension()));
    
                        $url = asset('storage/' . $url);
                        $url = "<img src='$url' style='max-width:100px; display:inline;'>";
                        $fullText .= $url;
                    }
                    else if (method_exists($element, 'getText')) {
                        $fullText .= $element->getText();
                    }
                }
            }
        }

        return trim($fullText);
    }
}
