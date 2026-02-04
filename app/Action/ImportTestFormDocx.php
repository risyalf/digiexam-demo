<?php

namespace App\Action;

use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\IOFactory;

class ImportTestFormDocx
{
    public static function execute($filePath)
    {
        $phpWord = IOFactory::load($filePath);
        $finalData = [];

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if ($element instanceof Table) {
                    $rows = $element->getRows();
                    
                    $rowData = collect($rows)->skip(1);

                    foreach ($rowData->chunk(5) as $chunk) {
                        $questionRow = $chunk->first();
                        $answerRows = $chunk->skip(1);

                        $cells = $questionRow->getCells();
                        
                        $finalData[] = [
                            'type'     => self::getCellText($cells[1] ?? null),
                            'question' => self::getCellText($cells[2] ?? null),
                            'answers'  => $answerRows->map(function ($aRow) {
                                $aCells = $aRow->getCells();
                                $value = self::getCellText($aCells[3] ?? null);
                                
                                return [
                                    'text'  => self::getCellText($aCells[2] ?? null),
                                    'value' => $value === '1' ? 'true' : 'false',
                                ];
                            })->values()->toArray(),
                        ];
                    }
                }
            }
        }
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