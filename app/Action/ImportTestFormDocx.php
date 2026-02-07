<?php

namespace App\Action;

use Exception;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\IOFactory;

class ImportTestFormDocx
{
    public static function execute(string $filePath): array
    {
        $phpWord = IOFactory::load($filePath);
        $finalData = [];

        $questionIsNull = false;

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if ($element instanceof Table) {
                    $rows = $element->getRows();

                    $rowData = collect($rows)->skip(1);

                    foreach ($rowData->chunk(5) as $chunk) {
                        $questionRow = $chunk->first();
                        $answerRows = $chunk->skip(1);

                        $cells = $questionRow->getCells();

                        $number = self::getCellText($cells[0] ?? null);
                        $type = self::getCellText($cells[1] ?? null);
                        $question = self::getCellText($cells[2] ?? null);
                        $answers = $answerRows->map(function ($aRow, $index) {
                            $aCells = $aRow->getCells();
                            $value = self::getCellText($aCells[3] ?? null);

                            return [
                                'id' => $index,
                                'text'  => self::getCellText($aCells[2] ?? null),
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
                            'question' => $question,
                            'answers'  => $answers->toArray(),
                        ];
                    }
                }
            }
        }

        return $finalData;
    }

    protected static function getCellText($cell): string
    {
        if (!$cell) return '';

        $fullText = '';
        foreach ($cell->getElements() as $element) {
            if (method_exists($element, 'getText')) {
                $fullText .= $element->getText();
            } elseif ($element instanceof TextRun) {
                foreach ($element->getElements() as $textElement) {
                    if (method_exists($textElement, 'getText')) {
                        $fullText .= $textElement->getText();
                    }
                }
            }
        }

        return trim($fullText);
    }
}
