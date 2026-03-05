<?php

namespace App\Action;

use Exception;
use PhpOffice\PhpWord\Element\Image;
use PhpOffice\PhpWord\Element\ListItem;
use PhpOffice\PhpWord\Element\ListItemRun;
use PhpOffice\PhpWord\Element\Link;
use PhpOffice\PhpWord\Element\PreserveText;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\Element\TextBreak;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\SimpleType\NumberFormat;
use PhpOffice\PhpWord\Style;
use PhpOffice\PhpWord\Style\ListItem as ListItemStyle;
use PhpOffice\PhpWord\Style\Numbering;

use function PHPUnit\Framework\stringContains;

class ImportTestFormDocx
{
    protected const DEFAULT_FONT_FAMILY = '"Times New Roman", Times, serif';
    protected const DEFAULT_FONT_SIZE_PX = 16;
    protected const DEFAULT_TAB_SIZE = 4;

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

                    if ($number == 41) {
                        $a = 0;
                    }

                    $type     = self::parseCell($cells[1] ?? null, $id);
                    $question = self::parseCell($cells[2] ?? null, $id);
                    $answers  = $answerRows->map(function ($aRow, $index) use ($id) {

                        $aCells = $aRow->getCells();
                        $value  = self::parseCell($aCells[3] ?? null, $id);
                        $cleanValue = trim(strip_tags($value));

                        return [
                            'id'    => $index,
                            'text'  => self::parseCell($aCells[2] ?? null, $id),
                            'value' => $cleanValue === '1' ? 'true' : 'false',
                        ];
                    })->values();

                    if (!$type) {
                        break;
                    }

                    $cleanQuestion = trim(strip_tags($question));
                    

                    if (!$cleanQuestion) {

                        if (!str_contains($question, '<img')) {
                            $questionIsNull = true;
                            continue;
                        }
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
                        'question' => $question,
                        'answers'  => $answers->toArray(),
                    ];
                }
            }
        }

        return $finalData;
    }

    /**
     * Parse satu cell DOCX → HTML, mempertahankan:
     * - paragraf
     * - line break (TextBreak)
     * - tab/space (via white-space + tab-size)
     * - styling inline (bold/italic/underline/color/bg)
     * - gambar inline
     */
    protected static function parseCell($cell, string $id): string
    {
        if (!$cell) return '';

        $blocks = [];
        $elements = $cell->getElements();
        $i = 0;
        $count = count($elements);

        while ($i < $count) {
            $element = $elements[$i];

            if (self::isListElement($element)) {
                $listElements = [];
                while ($i < $count && self::isListElement($elements[$i])) {
                    $listElements[] = $elements[$i];
                    $i++;
                }
                $listHtml = self::renderListBlock($listElements, $id);
                if ($listHtml !== '') {
                    $blocks[] = $listHtml;
                }
                continue;
            }

            if ($element instanceof TextRun) {
                $inlineHtml = self::parseInlineElements($element->getElements(), $id);
                if ($inlineHtml !== '') {
                    $blocks[] = self::wrapParagraph($inlineHtml);
                }
                $i++;
                continue;
            }

            if ($element instanceof Text) {
                $inlineHtml = self::prepareStyledText($element);
                if ($inlineHtml !== '') {
                    $blocks[] = self::wrapParagraph($inlineHtml);
                }
                $i++;
                continue;
            }

            if ($element instanceof Image) {
                $blocks[] = self::wrapParagraph(self::prepareImage($element, $id));
                $i++;
                continue;
            }

            if ($element instanceof TextBreak) {
                $blocks[] = self::wrapParagraph('<br>');
                $i++;
                continue;
            }

            $i++;
        }

        $html = trim(implode('', array_filter($blocks, fn ($b) => $b !== '')));
        return self::wrapCellContainer($html);
    }

    protected static function wrapCellContainer(string $innerHtml): string
    {
        $innerHtml = trim($innerHtml);
        if ($innerHtml === '') {
            return '';
        }

        $fontFamily = self::DEFAULT_FONT_FAMILY;
        $fontSize = self::DEFAULT_FONT_SIZE_PX;
        $tabSize = self::DEFAULT_TAB_SIZE;

        return "<div style=\"font-family:{$fontFamily}; font-size:{$fontSize}px; white-space:pre-wrap; tab-size:{$tabSize}; -moz-tab-size:{$tabSize};\">{$innerHtml}</div>";
    }

    /**
     * Parse inline elements in a paragraph/run.
     *
     * @param array<int, mixed> $elements
     */
    protected static function parseInlineElements(array $elements, string $id): string
    {
        $html = '';

        foreach ($elements as $child) {
            if ($child instanceof Image) {
                $html .= self::prepareImage($child, $id);
                continue;
            }

            if ($child instanceof Text) {
                $html .= self::prepareStyledText($child);
                continue;
            }

            if ($child instanceof PreserveText) {
                $html .= self::escapeTextPreserveWhitespace($child->getText());
                continue;
            }

            if ($child instanceof Link) {
                $text = method_exists($child, 'getText') ? (string) $child->getText() : '';
                $source = method_exists($child, 'getSource') ? (string) $child->getSource() : '';
                $href = htmlspecialchars($source, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                $label = self::escapeTextPreserveWhitespace($text);
                $html .= "<a href=\"{$href}\" target=\"_blank\" rel=\"noopener noreferrer\">{$label}</a>";
                continue;
            }

            if ($child instanceof TextBreak) {
                $html .= '<br>';
                continue;
            }
        }

        return trim($html);
    }

    protected static function isListElement(mixed $element): bool
    {
        return $element instanceof ListItem || $element instanceof ListItemRun;
    }

    /**
     * @param array<int, ListItem|ListItemRun> $listElements
     */
    protected static function renderListBlock(array $listElements, string $id): string
    {
        if (count($listElements) === 0) {
            return '';
        }

        $root = ['children' => []];
        $stack = [&$root];

        foreach ($listElements as $listElement) {
            $depth = method_exists($listElement, 'getDepth') ? (int) $listElement->getDepth() : 0;
            if ($depth < 0) {
                $depth = 0;
            }

            $style = method_exists($listElement, 'getStyle') ? $listElement->getStyle() : null;
            $tag = self::inferListTag($style, $depth);
            $innerHtml = self::renderListItemInner($listElement, $id);

            while (count($stack) > $depth + 1) {
                array_pop($stack);
            }

            $parent = &$stack[count($stack) - 1];
            $parent['children'][] = [
                'tag' => $tag,
                'html' => $innerHtml,
                'children' => [],
            ];

            $newIndex = array_key_last($parent['children']);
            $stack[] = &$parent['children'][$newIndex];
            unset($parent, $newIndex);
        }

        return self::renderListNodes($root['children']);
    }

    protected static function renderListNodes(array $nodes): string
    {
        $html = '';
        $i = 0;
        $count = count($nodes);

        while ($i < $count) {
            $tag = $nodes[$i]['tag'] ?? 'ul';
            $listStyleType = $tag === 'ol' ? 'decimal' : 'disc';
            $html .= "<{$tag} style=\"margin:0; padding-left:1.25em; list-style-position:outside !important; list-style-type:{$listStyleType} !important;\">";

            while ($i < $count && (($nodes[$i]['tag'] ?? 'ul') === $tag)) {
                $liInner = (string) ($nodes[$i]['html'] ?? '');
                $liStyle = "display:list-item; list-style-position:outside !important; list-style-type:{$listStyleType} !important;";
                $html .= "<li style=\"{$liStyle}\">{$liInner}";
                $children = $nodes[$i]['children'] ?? [];
                if (is_array($children) && count($children) > 0) {
                    $html .= self::renderListNodes($children);
                }
                $html .= "</li>";
                $i++;
            }

            $html .= "</{$tag}>";
        }

        return $html;
    }

    protected static function inferListTag(?ListItemStyle $style, int $depth): string
    {
        if ($style) {
            $listType = $style->getListType();
            if ($listType !== null) {
                if (in_array($listType, [ListItemStyle::TYPE_NUMBER, ListItemStyle::TYPE_NUMBER_NESTED, ListItemStyle::TYPE_ALPHANUM], true)) {
                    return 'ol';
                }
                return 'ul';
            }

            $numStyle = $style->getNumStyle();
            if ($numStyle) {
                $numStyleObject = Style::getStyle($numStyle);
                if ($numStyleObject instanceof Numbering) {
                    $levels = $numStyleObject->getLevels();
                    if (isset($levels[$depth])) {
                        $format = $levels[$depth]->getFormat();
                        if ($format && $format !== NumberFormat::BULLET && $format !== NumberFormat::NONE) {
                            return 'ol';
                        }
                    }
                }
            }
        }

        return 'ul';
    }

    protected static function renderListItemInner(ListItem|ListItemRun $listElement, string $id): string
    {
        if ($listElement instanceof ListItemRun) {
            return self::parseInlineElements($listElement->getElements(), $id);
        }

        $textObject = $listElement->getTextObject();
        return self::prepareStyledText($textObject);
    }

    protected static function wrapParagraph(string $innerHtml): string
    {
        $innerHtml = trim($innerHtml);
        if ($innerHtml === '') {
            return '';
        }

        return "<p style=\"margin:0;\">{$innerHtml}</p>";
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
            return self::escapeTextPreserveWhitespace($text);
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

        // Font family & size are normalized by wrapper (paragraph/list item)

        if ($colorStyle) {
            $open  .= "<span style='{$colorStyle}'>";
            $close = "</span>" . $close;
        }

        return $open . self::escapeTextPreserveWhitespace($text) . $close;
    }

    protected static function escapeTextPreserveWhitespace(string $text): string
    {
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $escaped = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        return str_replace("\n", '<br>', $escaped);
    }
}
