<?php

namespace App\Action;

use Exception;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\Element\Image;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\IOFactory;

class ImportTestFormDocx
{
    public static function execute(string $id, string $filePath): array
    {
        $phpWord   = IOFactory::load($filePath);
        $finalData = [];

        $questionIsNull = false;
        $countQuestionOption = 5;
        $number = 0;

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {

                if (!($element instanceof Table)) continue;

                $rows = $element->getRows();
                $rowData = collect($rows)->skip(1); // skip header

                foreach ($rowData->chunk($countQuestionOption + 1) as $chunk) {

                    $questionRow = $chunk->first();
                    $answerRows  = $chunk->skip(1);

                    $cells = $questionRow->getCells();

                    $number++;

                    $type     = self::parseCell($cells[1] ?? null, $id);
                    $question = self::parseCell($cells[2] ?? null, $id);
                    $answers  = $answerRows->map(function ($aRow, $index) use ($id) {

                        $aCells = $aRow->getCells();
                        $value  = self::parseCell($aCells[3] ?? null, $id);
                        $cleanValue = trim(strip_tags($value));

                        return [
                            'id'    => $index,
                            'text'  => "<p>" . self::parseCell($aCells[2] ?? null, $id) . "</p>",
                            'value' => $cleanValue === '1' ? 'true' : 'false',
                        ];
                    })->values();

                    if (!$type) {
                        break;
                    }

                    if (!$question) {
                        $questionIsNull = true;
                        continue;
                    }

                    if ($questionIsNull) {
                        throw new Exception("SOAL PADA NOMOR " . ($number - 1) . " KOSONG!");
                    }

                    $cleanType = trim(strip_tags($type));

                    if (!in_array($cleanType, ['PILIHAN GANDA', 'ESSAY'])) {
                        throw new Exception("JENIS PERTANYAAN {$cleanType} SALAH PADA NOMOR {$number}");
                    }

                    // check correct answer exists
                    if (!$answers->contains(fn($a) => $a['value'] === 'true')) {
                        throw new Exception("TIDAK ADA JAWABAN BENAR PADA SOAL NOMOR : " . $number);
                    }

                    $finalData[] = [
                        'number'   => $number,
                        'type'     => $cleanType,
                        'question' => "<p>{$question}</p>",
                        'answers'  => $answers->toArray(),
                    ];
                }
            }
        }

        return $finalData;
    }

    /**
     * Parse satu cell DOCX → HTML dengan styling
     */
    protected static function parseCell($cell, string $id): string
    {
        if (!$cell) return '';

        $html = "";

        foreach ($cell->getElements() as $element) {

            // Handle TextRun — yang isinya inline text & style
            if ($element instanceof TextRun) {

                foreach ($element->getElements() as $child) {

                    // Image
                    if ($child instanceof Image) {
                        $html .= self::prepareImage($child, $id);
                        continue;
                    }

                    // Text with style
                    if ($child instanceof Text) {
                        $html .= self::prepareStyledText($child);
                        continue;
                    }
                }
            }
        }

        return trim($html);
    }

    /**
     * Convert inline image
     */
    protected static function prepareImage(Image $img, string $id): string
    {
        $name = uniqid();
        $url = SaveImage::execute(
            $id,
            $name,
            $img->getImageString(),
            strtolower($img->getImageExtension())
        );

        $url = asset('storage/' . $url);
        return "<img src='{$url}' style='max-width:100px; display:inline;'>";
    }

    /**
     * Convert styled text from DOCX → HTML
     */
    protected static function prepareStyledText(Text $textElement): string
    {
        $text = $textElement->getText();
        $style = $textElement->getFontStyle();

        if (!$style) {
            return htmlspecialchars($text);
        }

        $open  = "";
        $close = "";

        // Bold
        if ($style->isBold()) {
            $open  .= "<b>";
            $close = "</b>" . $close;
        }

        // Italic
        if ($style->isItalic()) {
            $open  .= "<i>";
            $close = "</i>" . $close;
        }

        // Underline
        $underline = $style->getUnderline();
        if ($underline && strtolower($underline) !== 'none') {
            $open  .= "<u>";
            $close = "</u>" . $close;
        }

        // Font color
        $colorStyle = "";
        if ($style->getColor()) {
            $color = $style->getColor();
            $colorStyle .= "color:#{$color};";
        }

        // Highlight / Background color
        if ($style->getBgColor()) {
            $bg = $style->getBgColor();
            $colorStyle .= "background-color:#{$bg};";
        }

        // Font size
        if ($style->getSize()) {
            $size = $style->getSize();
            $colorStyle .= "font-size:{$size}px;";
        }

        if ($colorStyle) {
            $open  .= "<span style='{$colorStyle}'>";
            $close = "</span>" . $close;
        }

        return $open . htmlspecialchars($text) . $close;
    }
}