<?php

declare(strict_types=1);

namespace Bithost\Pdfviewhelpers\Model;

/* * *
 *
 * This file is part of the "PDF ViewHelpers" Extension for TYPO3 CMS.
 *
 *  (c) 2016 Markus Mächler <markus.maechler@bithost.ch>, Bithost GmbH
 *           Esteban Gehring <esteban.gehring@bithost.ch>, Bithost GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * * */

use Bithost\Pdfviewhelpers\Exception\ValidationException;
use Closure;
use Com\Tecnick\Pdf\Import\PageTemplateInterface;
use Com\Tecnick\Pdf\Tcpdf;
use TYPO3\CMS\Core\Core\Environment;

/**
 * BasePDF
 *
 * Extends the tc-lib-pdf document with a cursor based flow layout: content is placed at the current position,
 * breaks across pages automatically and the cursor is moved below it afterwards.
 *
 * All positions and sizes are given in the document unit and measured from the top left corner of the page.
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
 */
class BasePDF extends Tcpdf
{
    const SCOPE_THIS_PAGE = 'thisPage';
    const SCOPE_THIS_PAGE_INCLUDING_PAGE_BREAKS = 'thisPageIncludingPageBreaks';
    const SCOPE_DOCUMENT = 'document';

    const CORE_FONTS = ['courier', 'helvetica', 'times', 'symbol', 'zapfdingbats'];

    protected array $constructorArguments;

    protected float $posX = 0;

    protected float $posY = 0;

    protected array $textStyle = [
        'fontFamily' => 'helvetica',
        'fontStyle' => '',
        'fontSize' => 11,
        'color' => '#000',
        'lineHeight' => 1.25,
        'characterSpacing' => 0,
        'paddingLeft' => 0,
        'paddingRight' => 0,
    ];

    protected array $hyphenPatterns = [];

    protected int $jpegQuality = 100;

    /**
     * Custom font definition files indexed by font name, e.g. ['roboto' => '/path/roboto.json']
     */
    protected array $customFontFiles = [];

    protected ?Closure $documentHeaderClosure = null;

    protected ?Closure $pageHeaderClosure = null;

    protected string $pageHeaderScope = '';

    protected ?Closure $documentFooterClosure = null;

    protected ?Closure $pageFooterClosure = null;

    protected string $pageFooterScope = '';

    /**
     * If true the header is rendered as soon as a page is added, otherwise it is rendered by calling renderHeader()
     */
    protected bool $isAutoPageBreak = false;

    protected bool $isRenderingHeaderOrFooter = false;

    /**
     * Pages that have been prepared (template and header) and finished (footer), indexed by page id
     */
    protected array $preparedPages = [];
    protected array $finishedPages = [];

    protected string $sourceId = '';

    protected ?PageTemplateInterface $currentTemplate = null;

    protected bool $importTemplateOnThisPage = false;

    protected int $tableOfContentStartPage = -1;

    protected int $tableOfContentTargetPage = 1;

    protected string $tableOfContentName = '';

    protected float $lastHtmlEndY = 0;

    protected bool $isWritingHtml = false;

    /**
     * Pages added while writing HTML, their header and footers are rendered afterwards
     */
    protected array $pendingPages = [];

    /**
     * Header and footer texts containing page number aliases, they are drawn once the page numbers are known
     */
    protected array $deferredTexts = [];

    public function __construct(string $unit = 'mm', bool $subsetFonts = true, string $mode = '')
    {
        $this->constructorArguments = [$unit, $subsetFonts, $mode];

        // Local files, e.g. fonts or images in HTML, may be loaded from within the TYPO3 project only
        $typo3Paths = [dirname(__DIR__, 2), Environment::getProjectPath(), Environment::getPublicPath()];

        parent::__construct(
            unit: $unit,
            isunicode: true,
            subsetfont: $subsetFonts,
            mode: $mode,
            fileOptions: [
                'allowedPaths' => array_merge($this->defaultFileAllowedPaths(), $typo3Paths),
                'markupAllowedPaths' => array_merge($this->defaultMarkupAllowedPaths(), $typo3Paths),
            ],
        );

        // Lets tc-lib-pdf find the bundled and custom fonts by name, e.g. when HTML switches to bold text
        $fontStack = new FontStack($this->kunit, $this->subsetfont, $this->isunicode, $this->requiresEmbeddedFonts(), $this->file);
        $fontStack->setFontFileResolver(function (string $family, string $style): string {
            try {
                return $this->getFontFile($family, $style);
            } catch (ValidationException) {
                return '';
            }
        });
        $this->font = $fontStack;

        $this->enableDefaultPageContent();
    }

    /**
     * Creates an empty document with the same settings and a page like the current one, e.g. to measure content.
     */
    public function createShadow(): static
    {
        $shadow = new static(...$this->constructorArguments);
        $shadow->customFontFiles = $this->customFontFiles;
        $shadow->textStyle = $this->textStyle;
        $shadow->hyphenPatterns = $this->hyphenPatterns;

        $page = $this->page->getPage();
        $shadow->addPage([
            'width' => $page['width'],
            'height' => $page['height'],
            'margin' => $page['margin'],
            'autobreak' => $page['autobreak'],
        ]);
        $shadow->setXY($this->posX, $this->posY);

        return $shadow;
    }

    // ===| Pages |===========================================================

    /**
     * Adds a page. Without data the format and margins of the last page are used.
     */
    public function addPage(array $data = []): array
    {
        $page = parent::addPage($data);
        $this->setXY($this->getMargins()['left'], $this->getMargins()['top']);

        return $page;
    }

    /**
     * Adds a page using TCPDF like parameters, the format is either a name like A4 or an array [width, height].
     */
    public function addPageWithSettings(string $orientation, string|array $format, array $margins, bool $autoPageBreak): array
    {
        $data = [
            'orientation' => $orientation,
            'margin' => [
                'PL' => (float) $margins['left'],
                'PR' => (float) $margins['right'],
                'PT' => 0,
                'CT' => (float) $margins['top'],
                'CB' => $autoPageBreak ? (float) $margins['bottom'] : 0,
                'PB' => 0,
            ],
            'autobreak' => $autoPageBreak,
        ];

        if (is_array($format)) {
            [$data['width'], $data['height']] = array_map('floatval', array_values($format));
        } else {
            $data['format'] = $format;
        }

        return $this->addPage($data);
    }

    /**
     * Called by tc-lib-pdf whenever content enters a new page, either added explicitly or by an automatic page break.
     */
    public function defaultPageContent(int $pid = -1): string
    {
        $pid = $pid < 0 ? $this->page->getPageId() : $pid;

        // Content flowing into an already existing page, e.g. from a second column, must not prepare it again
        if (isset($this->preparedPages[$pid])) {
            return '';
        }

        $this->preparedPages[$pid] = true;

        if ($this->importTemplateOnThisPage && $this->currentTemplate !== null) {
            $this->drawTemplate($this->currentTemplate);
        }

        if ($this->isWritingHtml) {
            // The HTML renderer of tc-lib-pdf can not render a header or footer in the middle of rendering HTML
            $this->pendingPages[] = $pid;
        } else {
            $this->renderHeaderAndFooters($pid);
        }

        return '';
    }

    /**
     * Renders the footers of the pages before the given page and the header of the given page
     */
    protected function renderHeaderAndFooters(int $pid): void
    {
        foreach (array_keys($this->page->getPages()) as $pageId) {
            if ($pageId < $pid) {
                $this->finishPage($pageId);
            }
        }

        if ($this->isAutoPageBreak) {
            if ($this->pageHeaderScope === self::SCOPE_THIS_PAGE) {
                $this->pageHeaderClosure = null; // the page header is only used once
            }

            $currentPage = $this->getPageId();
            $this->setCurrentPage($pid);
            $this->renderHeader();
            $this->setCurrentPage($currentPage);
        }
    }

    /**
     * Returns the document unit: pt, mm, cm or in
     */
    public function getUnit(): string
    {
        return $this->constructorArguments[0];
    }

    public function getPageId(): int
    {
        return $this->page->getPageId();
    }

    /**
     * Moves the cursor to the given existing page, the position on the page is kept.
     */
    public function setPageId(int $pid): void
    {
        $this->setCurrentPage($pid);
    }

    public function getPageWidth(): float
    {
        return (float) $this->page->getPage()['width'];
    }

    public function getPageHeight(): float
    {
        return (float) $this->page->getPage()['height'];
    }

    /**
     * Returns the margins of the current page, the bottom margin is the automatic page break margin.
     */
    public function getMargins(): array
    {
        $margin = $this->page->getPage()['margin'];

        return [
            'left' => (float) $margin['PL'],
            'top' => (float) $margin['CT'],
            'right' => (float) $margin['PR'],
            'bottom' => (float) $margin['CB'],
        ];
    }

    public function getInnerPageWidth(): float
    {
        return $this->getPageWidth() - $this->getMargins()['left'] - $this->getMargins()['right'];
    }

    public function getInnerPageHeight(): float
    {
        return $this->getPageHeight() - $this->getMargins()['top'] - $this->getMargins()['bottom'];
    }

    /**
     * Returns the y position where the automatic page break occurs on the current page.
     */
    public function getPageBreakY(): float
    {
        return $this->getPageHeight() - $this->getMargins()['bottom'];
    }

    // ===| Cursor |==========================================================

    public function getX(): float
    {
        return $this->posX;
    }

    public function getY(): float
    {
        return $this->posY;
    }

    public function setX(float $posX): void
    {
        $this->posX = $posX;
    }

    /**
     * Sets the y position and resets x to the left margin. A negative value is measured from the bottom of the page.
     */
    public function setY(float $posY): void
    {
        $this->posY = $posY < 0 ? $this->getPageHeight() + $posY : $posY;
        $this->posX = $this->getMargins()['left'];
    }

    public function setXY(float $posX, float $posY): void
    {
        $this->setY($posY);
        $this->setX($posX);
    }

    /**
     * Moves the cursor down and resets x to the left margin. Content following below the page break adds a new page.
     */
    public function addVerticalSpace(float $height): void
    {
        $this->setY($this->posY + $height);
    }

    /**
     * Continues on the next page, which is added if it does not exist yet. A next page exists e.g. in multiple columns.
     */
    public function nextPage(): void
    {
        if ($this->getPageId() < count($this->page->getPages()) - 1) {
            $this->setCurrentPage($this->getPageId() + 1);
            $this->setY($this->getMargins()['top']);
        } else {
            $this->addPage();
        }
    }

    /**
     * Continues on the next page if an element of the given height does not fit on the current page anymore.
     */
    public function checkPageBreak(float $height): void
    {
        if (
            !$this->isRenderingHeaderOrFooter
            && $this->page->isAutoPageBreakEnabled()
            && $this->posY + $height > $this->getPageBreakY()
        ) {
            $posX = $this->posX;
            $this->nextPage();
            $this->setX($posX);
        }
    }

    // ===| Text |============================================================

    public function setFont(string $fontFamily, string $fontStyle = '', ?float $fontSize = null): void
    {
        $this->textStyle['fontFamily'] = strtolower($fontFamily);
        $this->textStyle['fontStyle'] = strtoupper($fontStyle);

        if ($fontSize !== null) {
            $this->textStyle['fontSize'] = $fontSize;
        }
    }

    /**
     * @param float $fontSize Font size in points
     */
    public function setFontSize(float $fontSize): void
    {
        $this->textStyle['fontSize'] = $fontSize;
    }

    /**
     * @param string $color Color in HEX format, e.g. #000
     */
    public function setTextColor(string $color): void
    {
        $this->textStyle['color'] = $color;
    }

    /**
     * @param float $lineHeight Line height relative to the font size
     */
    public function setLineHeight(float $lineHeight): void
    {
        $this->textStyle['lineHeight'] = $lineHeight;
    }

    public function setCharacterSpacing(float $characterSpacing): void
    {
        $this->textStyle['characterSpacing'] = $characterSpacing;
    }

    public function setCellPadding(float $left, float $right): void
    {
        $this->textStyle['paddingLeft'] = $left;
        $this->textStyle['paddingRight'] = $right;
    }

    public function getTextStyle(): array
    {
        return $this->textStyle;
    }

    public function setTextStyle(array $textStyle): void
    {
        $this->textStyle = $textStyle;
    }

    /**
     * Enables automatic hyphenation using the given TeX hyphenation pattern file, an empty path disables it.
     */
    public function setHyphenation(string $hyphenFilePath): void
    {
        static $patterns = [];

        if ($hyphenFilePath !== '' && !isset($patterns[$hyphenFilePath])) {
            $patterns[$hyphenFilePath] = $this->loadTexHyphenPatterns($hyphenFilePath);
        }

        $this->hyphenPatterns = $hyphenFilePath === '' ? [] : $patterns[$hyphenFilePath];
    }

    /**
     * Writes text at the given position and moves the cursor below it. The text breaks across pages automatically.
     *
     * @param float|null $width The width of the text cell, null extends it to the right page margin
     * @param string $alignment L, C, R or J
     * @param float $minHeight The minimal height the cursor is moved down
     * @return array{page: int, y: float} The position of the first line, which might be on the next page
     */
    public function writeText(string $text, float $posX, float $posY, ?float $width = null, string $alignment = 'L', float $minHeight = 0): array
    {
        $width ??= $this->getPageWidth() - $this->getMargins()['right'] - $posX;
        $startPage = $this->getPageId();
        $firstLine = ['page' => $startPage, 'y' => $posY];
        $font = $this->insertFont();
        $lineHeight = $this->getLineHeight($font);
        $lineSpace = $lineHeight - $this->toUnit($font['height']);

        if ($text === '') {
            $endY = $posY + $lineHeight;
        } else {
            $arguments = $this->getTextCellArguments($this->breakLongWords($text, $width), $width, $alignment, $lineSpace);

            $this->setTexHyphenPatterns($this->hyphenPatterns);

            try {
                if ($this->isRenderingHeaderOrFooter || $this->containsPageNumberAlias($text)) {
                    // Header and footer do not break across pages and may be placed into the page margins.
                    // Page number aliases are replaced when the document is output, as tc-lib-pdf only does it for core fonts.
                    // The text is rendered in any case in order to get its height.
                    $arguments += ['posx' => $posX, 'posy' => $posY];
                    $content = $this->getHeaderOrFooterText($font, $arguments);
                    $height = $this->getLastCellBBox()['h'] + $lineSpace;

                    if (!$this->isRenderingHeaderOrFooter && $posY + $height > $this->getPageBreakY()) {
                        $this->setXY($posX, $posY);
                        $this->checkPageBreak($height);
                        $arguments['posy'] = $posY = $this->posY;
                        $firstLine = ['page' => $this->getPageId(), 'y' => $posY];
                    }

                    if ($this->containsPageNumberAlias($text)) {
                        $this->deferredTexts[] = ['p' => $this->getPageId(), 'textStyle' => $this->textStyle, 'arguments' => $arguments];
                    } else {
                        $this->page->addContent($content);
                    }

                    $endY = $posY + $height;
                } else {
                    $endY = $this->writeTextCell($arguments, $font, $posX, $posY, $lineHeight, $firstLine);
                }
            } finally {
                $this->setTexHyphenPatterns([]);
            }
        }

        $this->font->popLastFont();

        if ($startPage === $this->getPageId()) {
            $endY = max($endY, $posY + $minHeight);
        }

        $this->setY($endY);

        return $firstLine;
    }

    /**
     * Writes the text breaking across pages and returns the y position below it
     */
    protected function writeTextCell(array $arguments, array $font, float $posX, float $posY, float $lineHeight, array &$firstLine): float
    {
        $region = $this->page->getRegion();

        // tc-lib-pdf drops the text if not even the first line fits into the region, so the break is done here.
        // This is the same calculation tc-lib-pdf uses to decide how many lines fit into the region.
        if ((int) (($region['RY'] + $region['RH'] - $posY + $arguments['linespace']) / $lineHeight) < 1) {
            $pid = $this->getPageId();
            $currentRegion = $this->page->getPage()['currentRegion'];
            $region = $this->moveToNextRegion();

            if ($pid === $this->getPageId() && $currentRegion === $this->page->getPage()['currentRegion']) {
                // without automatic page break the text is outside of the page
                return $posY + $lineHeight;
            }

            $posY = (float) $region['RY'];
            $firstLine = ['page' => $this->getPageId(), 'y' => $posY];
        }

        $this->page->addContent($font['out'] . $this->graph->add(['fillColor' => $this->textStyle['color']]));
        $this->addTextCell(...$arguments, posx: $posX - $region['RX'], posy: $posY - $region['RY']);
        $this->graph->pop();

        $bbox = $this->getLastCellBBox();

        // the last line has the height of the font, like TCPDF every line takes the full line height
        return $bbox['y'] + $bbox['h'] + $arguments['linespace'];
    }

    protected function containsPageNumberAlias(string $text): bool
    {
        return str_contains($text, $this->page::PAGE_NUM) || str_contains($text, $this->page::PAGE_TOT);
    }

    protected function getTextCellArguments(string $text, float $width, string $alignment, float $lineSpace): array
    {
        $cell = $this->defcell;
        $cell['padding'] = [
            'T' => 0,
            'R' => $this->toPoints($this->textStyle['paddingRight']),
            'B' => 0,
            'L' => $this->toPoints($this->textStyle['paddingLeft']),
        ];

        return [
            'txt' => $text,
            'width' => $width,
            'linespace' => $lineSpace,
            'valign' => 'T',
            'halign' => $alignment,
            'cell' => $cell,
            'styles' => ['all' => ['lineWidth' => 0]],
            'underline' => str_contains($this->textStyle['fontStyle'], 'U'),
            'drawcell' => false,
        ];
    }

    /**
     * Writes a single line of text once the document is output, "#PAGE#" is replaced by the final page number the
     * internal link points to. This is used for page numbers in a table of content as pages might still be moved.
     * The cursor is not moved.
     */
    public function writeLinkPageNumber(string $text, string $link, float $posX, float $posY, float $width, string $alignment = 'R'): void
    {
        $this->deferredTexts[] = [
            'p' => $this->getPageId(),
            'link' => $link,
            'textStyle' => $this->textStyle,
            'arguments' => $this->getTextCellArguments($text, $width, $alignment, 0) + ['posx' => $posX, 'posy' => $posY],
        ];
    }

    /**
     * Moves to the next page region the same way tc-lib-pdf does on automatic page breaks and returns it.
     */
    protected function moveToNextRegion(): array
    {
        $pid = $this->getPageId();
        $this->page->getNextRegion($pid);

        if ($this->getPageId() > $pid) {
            $this->setPageContext($this->getPageId());
        }

        return $this->page->getRegion();
    }

    /**
     * Allows breaking words that do not fit into a line, tc-lib-pdf only breaks lines between words
     */
    protected function breakLongWords(string $text, float $width): string
    {
        if ($this->hyphenPatterns !== []) {
            // automatic hyphenation adds break points to long words
            return $text;
        }

        $maxWidth = $this->toPoints($width - $this->textStyle['paddingLeft'] - $this->textStyle['paddingRight']);

        return (string) preg_replace_callback('/\S{2,}/u', function (array $match) use ($maxWidth): string {
            $characters = mb_str_split($match[0]);

            if ($this->font->getOrdArrWidth(array_map('mb_ord', $characters)) <= $maxWidth) {
                return $match[0];
            }

            // zero width spaces are invisible break points
            return implode("\u{200B}", $characters);
        }, $text);
    }

    protected function getHeaderOrFooterText(array $font, array $arguments): string
    {
        return $this->graph->getStartTransform()
            . $font['out']
            . $this->graph->getStyleCmd(['fillColor' => $this->textStyle['color']])
            . $this->getTextCell(...$arguments)
            . $this->graph->getStopTransform();
    }

    /**
     * Draws the deferred header and footer texts replacing the page number aliases, this works for all fonts.
     */
    protected function drawDeferredTexts(): void
    {
        $currentPage = $this->getPageId();
        $textStyle = $this->textStyle;
        $numberOfPages = count($this->page->getPages());

        foreach ($this->deferredTexts as $deferredText) {
            $this->setCurrentPage($deferredText['p']);
            $this->textStyle = $deferredText['textStyle'];
            $arguments = $deferredText['arguments'];
            $arguments['txt'] = str_replace(
                [$this->page::PAGE_NUM, $this->page::PAGE_TOT, '#PAGE#'],
                [(string) ($deferredText['p'] + 1), (string) $numberOfPages, (string) (($this->links[$deferredText['link'] ?? '']['p'] ?? -1) + 1)],
                $arguments['txt']
            );

            $font = $this->insertFont();
            $this->page->addContent($this->getHeaderOrFooterText($font, $arguments));
            $this->font->popLastFont();
        }

        $this->deferredTexts = [];
        $this->textStyle = $textStyle;
        $this->setCurrentPage($currentPage);
    }

    /**
     * Returns the width of a single line of text using the current text style.
     */
    public function getTextWidth(string $text): float
    {
        $this->insertFont();

        try {
            return $this->getStringWidth($text);
        } finally {
            $this->font->popLastFont();
        }
    }

    /**
     * Returns the height of one line of text using the current text style.
     */
    public function getLineHeight(?array $font = null): float
    {
        if ($font === null) {
            $font = $this->insertFont();
            $this->font->popLastFont();
        }

        return $this->toUnit($this->textStyle['lineHeight'] * $font['size']);
    }

    /**
     * Writes HTML at the given position and moves the cursor below it. The content breaks across pages automatically.
     * The current text style is used as default font, CSS can be used to style the content.
     */
    public function writeHtml(string $html, float $posX, float $posY, ?float $width = null): void
    {
        $width ??= $this->getPageWidth() - $this->getMargins()['right'] - $posX;

        // tc-lib-pdf does not align a line correctly if it ends with whitespace after a tag, e.g. "<span>Text</span> </div>".
        // Whitespace at the end of a block element is not rendered anyway.
        $html = (string) preg_replace('/>\s+(?=<\/(?:div|p|li|td|th|h[1-6]|blockquote)>)/i', '>', $html);

        $font = $this->insertFont();
        $this->setTexHyphenPatterns($this->hyphenPatterns);

        try {
            if ($this->isRenderingHeaderOrFooter) {
                // Header and footer do not break across pages and may be placed into the page margins, see pageBreak()
                $this->page->addContent(
                    $this->graph->getStartTransform()
                    . $font['out']
                    . $this->getHTMLCell($html, $posX, $posY, $width)
                    . $this->graph->getStopTransform()
                );
            } else {
                $this->page->addContent($font['out']);
                $this->isWritingHtml = true;
                $this->addHTMLCell($html, $posX, $posY, $width);
            }
        } finally {
            $this->isWritingHtml = false;
            $this->setTexHyphenPatterns([]);
            $this->font->popLastFont();
        }

        // rendering the header and footer might render HTML as well
        $endY = $this->lastHtmlEndY;
        $pendingPages = $this->pendingPages;
        $this->pendingPages = [];

        foreach ($pendingPages as $pid) {
            $this->renderHeaderAndFooters($pid);
        }

        $this->setY($endY);
    }

    /**
     * Called by the HTML renderer of tc-lib-pdf if the content does not fit into the page region anymore
     */
    protected function pageBreak(): int
    {
        // Header and footer stay on their page, the content continues below the page region
        if ($this->isRenderingHeaderOrFooter) {
            return $this->getPageId();
        }

        return parent::pageBreak();
    }

    protected function renderHTMLCellFragments(array &$hrc, float &$tpx, float &$tpy, float &$tpw, float &$tph, callable $appendFragment): void
    {
        parent::renderHTMLCellFragments($hrc, $tpx, $tpy, $tpw, $tph, $appendFragment);

        // The cursor position after the rendered HTML is not exposed otherwise
        $this->lastHtmlEndY = $tpy;
    }

    /**
     * Inserts the font of the current text style into the font stack, it has to be removed with popLastFont().
     */
    protected function insertFont(): array
    {
        $family = $this->textStyle['fontFamily'];
        $style = str_replace('U', '', $this->textStyle['fontStyle']);

        return $this->font->insert(
            $this->pon,
            $family,
            $style,
            (float) $this->textStyle['fontSize'],
            $this->toPoints((float) $this->textStyle['characterSpacing']),
            null,
            $this->getFontFile($family, $style),
        );
    }

    /**
     * Returns the font definition file of either a custom font or one of the bundled core fonts.
     */
    protected function getFontFile(string $family, string $style): string
    {
        $suffix = (str_contains($style, 'B') ? 'b' : '') . (str_contains($style, 'I') ? 'i' : '');

        if (isset($this->customFontFiles[$family . $suffix])) {
            return $this->customFontFiles[$family . $suffix];
        }

        if (isset($this->customFontFiles[$family])) {
            return $this->customFontFiles[$family];
        }

        if (!in_array($family, self::CORE_FONTS, true)) {
            throw new ValidationException('Unknown font "' . $family . '", please add it as custom font. ERROR: 1759737601', 1759737601);
        }

        if (in_array($family, ['symbol', 'zapfdingbats'], true)) {
            $suffix = '';
        }

        $fontPath = dirname(__DIR__, 2) . '/Resources/Private/Fonts/';

        // PDF/A, PDF/UA and PDF/X require embedded fonts
        if ($this->requiresEmbeddedFonts()) {
            return $fontPath . 'pdfa/pdfa' . $family . $suffix . '.json';
        }

        return $fontPath . 'core/' . $family . $suffix . '.json';
    }

    public function addCustomFontFile(string $fontName, string $fontFile): void
    {
        $this->customFontFiles[$fontName] = $fontFile;
    }

    // ===| Graphics and images |=============================================

    /**
     * @param array $style tc-lib-pdf line style, e.g. ['lineWidth' => 0.25, 'lineColor' => '#000']
     */
    public function drawLine(float $fromX, float $fromY, float $toX, float $toY, array $style): void
    {
        $this->page->addContent(
            $this->graph->getStartTransform()
            . $this->graph->getLine($fromX, $fromY, $toX, $toY, $style)
            . $this->graph->getStopTransform()
        );
    }

    public function drawRect(float $posX, float $posY, float $width, float $height, string $fillColor): void
    {
        $this->page->addContent(
            $this->graph->getStartTransform()
            . $this->graph->getBasicRect($posX, $posY, $width, $height, 'F', ['fillColor' => $fillColor])
            . $this->graph->getStopTransform()
        );
    }

    public function setJpegQuality(int $jpegQuality): void
    {
        $this->jpegQuality = $jpegQuality;
    }

    /**
     * Draws an image and moves the cursor below it. A new page is added if the image does not fit on the current page.
     *
     * @param string $data The image file contents
     * @param string $type Either "image" for raster images or "svg"
     * @param float|null $width If width and height are null the image size is used, if only one is given the aspect ratio is kept
     * @param string $alignment L, C or R aligns the image between $alignLeft and $alignRight, any other value keeps $posX
     * @param bool $fitOnPage If true the image is scaled down to fit into the page margins
     */
    public function drawImage(
        string $data,
        string $type,
        float $posX,
        float $posY,
        ?float $width = null,
        ?float $height = null,
        string $alignment = '',
        float $alignLeft = 0,
        float $alignRight = 0,
        bool $fitOnPage = true,
        string $link = '',
    ): void {
        if ($type === 'svg') {
            $size = $this->getSVGSize($data);
            $imageWidth = $size['width'];
            $imageHeight = $size['height'];
        } else {
            $imageId = $this->image->add('@' . $data, null, null, false, $this->jpegQuality);
            $size = $this->image->getImageDimensionsByKey($this->image->getKey('@' . $data, 0, 0, $this->jpegQuality));
            $imageWidth = $this->toUnit($size['width']);
            $imageHeight = $this->toUnit($size['height']);
        }

        if ($imageWidth <= 0 || $imageHeight <= 0) {
            throw new ValidationException('The image size could not be determined. ERROR: 1759737602', 1759737602);
        }

        $ratio = $imageWidth / $imageHeight;

        if ($width && $height) {
            // keep the aspect ratio inside the given box
            $width = min($width, $height * $ratio);
            $height = $width / $ratio;
        } elseif ($width) {
            $height = $width / $ratio;
        } elseif ($height) {
            $width = $height * $ratio;
        } else {
            $width = $imageWidth;
            $height = $imageHeight;
        }

        if (!$this->isRenderingHeaderOrFooter) {
            $maxHeight = $this->getInnerPageHeight();
            $maxWidth = $alignRight > $alignLeft ? $alignRight - $alignLeft : $this->getPageWidth() - $this->getMargins()['right'] - $posX;

            if ($this->page->isAutoPageBreakEnabled() && $height > $maxHeight) {
                $height = $maxHeight;
                $width = $height * $ratio;
            }

            if ($fitOnPage && $width > $maxWidth) {
                $width = $maxWidth;
                $height = $width / $ratio;
            }

            $this->posY = $posY;
            $this->checkPageBreak($height);
            $posY = $this->posY;
        }

        $posX = match ($alignment) {
            'L' => $alignLeft,
            'C' => ($alignLeft + $alignRight - $width) / 2,
            'R' => $alignRight - $width,
            default => $posX,
        };

        if ($type === 'svg') {
            $svgId = $this->addSVG('@' . $data, $posX, $posY, $width, $height, $this->getPageHeight());
            $content = $this->getSetSVG($svgId);
        } else {
            $content = $this->image->getSetImage($imageId, $posX, $posY, $width, $height, $this->getPageHeight());
        }

        $this->page->addContent($this->graph->getStartTransform() . $content . $this->graph->getStopTransform());

        if ($link !== '') {
            $this->page->addAnnotRef($this->setLink($posX, $posY, $width, $height, $link));
        }

        $this->setY($posY + $height);
    }

    // ===| Header and footer |===============================================

    public function setHeaderClosure(?Closure $closure = null, string $scope = self::SCOPE_DOCUMENT): void
    {
        if ($scope === self::SCOPE_DOCUMENT) {
            $this->documentHeaderClosure = $closure;
        } elseif (in_array($scope, [self::SCOPE_THIS_PAGE, self::SCOPE_THIS_PAGE_INCLUDING_PAGE_BREAKS])) {
            $this->pageHeaderScope = $scope;
            $this->pageHeaderClosure = $closure;
        }
    }

    public function setFooterClosure(?Closure $closure = null, string $scope = self::SCOPE_DOCUMENT): void
    {
        if ($scope === self::SCOPE_DOCUMENT) {
            $this->documentFooterClosure = $closure;
        } elseif (in_array($scope, [self::SCOPE_THIS_PAGE, self::SCOPE_THIS_PAGE_INCLUDING_PAGE_BREAKS])) {
            $this->pageFooterScope = $scope;
            $this->pageFooterClosure = $closure;
        }
    }

    public function setIsAutoPageBreak(bool $isAutoPageBreak): void
    {
        $this->isAutoPageBreak = $isAutoPageBreak;
    }

    /**
     * Renders the header on the current page
     */
    public function renderHeader(): void
    {
        $this->renderHeaderOrFooter(
            $this->pageHeaderClosure ?? $this->documentHeaderClosure,
            $this->basePdfHeader(...),
            $this->getMargins()['top'],
        );
    }

    /**
     * Renders the footer of the given page once
     */
    protected function finishPage(int $pid): void
    {
        if (isset($this->finishedPages[$pid])) {
            return;
        }

        $this->finishedPages[$pid] = true;
        $currentPage = $this->getPageId();
        $this->setCurrentPage($pid);

        $this->renderHeaderOrFooter(
            $this->pageFooterClosure ?? $this->documentFooterClosure,
            $this->basePdfFooter(...),
            $this->getPageBreakY(),
        );

        if ($this->pageFooterScope === self::SCOPE_THIS_PAGE) {
            $this->pageFooterClosure = null; // the page footer is only used once
        }

        $this->setCurrentPage($currentPage);
    }

    protected function renderHeaderOrFooter(?Closure $closure, Closure $hook, float $posY): void
    {
        $state = [$this->posX, $this->posY, $this->textStyle, $this->hyphenPatterns, $this->isRenderingHeaderOrFooter];
        $this->isRenderingHeaderOrFooter = true;
        $this->setY($posY);

        try {
            if ($closure !== null) {
                $closure();
            }

            $hook();
        } finally {
            [$this->posX, $this->posY, $this->textStyle, $this->hyphenPatterns, $this->isRenderingHeaderOrFooter] = $state;
        }
    }

    /**
     * Can be overwritten to render a header on every page
     */
    protected function basePdfHeader(): void
    {
    }

    /**
     * Can be overwritten to render a footer on every page
     */
    protected function basePdfFooter(): void
    {
    }

    /**
     * Renders the remaining footers and returns the PDF document.
     */
    public function getOutPDFString(): string
    {
        foreach (array_keys($this->page->getPages()) as $pid) {
            $this->finishPage($pid);
        }

        $this->drawDeferredTexts();

        return parent::getOutPDFString();
    }

    // ===| Templates |=======================================================

    /**
     * Sets the PDF file pages are imported from and returns its number of pages.
     */
    public function setSourceFile(string $path): int
    {
        $this->sourceId = $this->setImportSourceFile($path);

        return $this->getSourcePageCount($this->sourceId);
    }

    public function importSourcePage(int $pageNumber): PageTemplateInterface
    {
        return $this->importPage($this->sourceId, $pageNumber);
    }

    /**
     * Draws an imported page as template on the current page
     */
    public function drawTemplate(PageTemplateInterface $template): void
    {
        $this->useImportedPage($template, 0, 0);
    }

    /**
     * Sets the template that is drawn on pages added by automatic page breaks
     */
    public function setCurrentTemplate(?PageTemplateInterface $template): void
    {
        $this->currentTemplate = $template;
    }

    public function setImportTemplateOnThisPage(bool $importTemplateOnThisPage): void
    {
        $this->importTemplateOnThisPage = $importTemplateOnThisPage;
    }

    /**
     * Appends all pages of the given PDF file as new pages having their original size.
     */
    public function attachPdf(string $path): void
    {
        $sourceId = $this->setImportSourceFile($path);
        $margins = $this->page->getPages() === [] ? ['left' => 0, 'top' => 0, 'right' => 0, 'bottom' => 0] : $this->getMargins();

        for ($pageNumber = 1; $pageNumber <= $this->getSourcePageCount($sourceId); $pageNumber++) {
            $template = $this->importPage($sourceId, $pageNumber);
            $width = $this->toUnit($template->getWidth());
            $height = $this->toUnit($template->getHeight());

            $this->addPageWithSettings($width > $height ? 'L' : 'P', [$width, $height], $margins, true);
            $this->useImportedPage($template, 0, 0, $width, $height);
        }
    }

    // ===| Bookmarks and table of content |==================================

    public function addBookmark(string $text, int $level = 0, string $fontStyle = '', string $color = ''): void
    {
        $this->setBookmark($text, '', $level, $this->getPageId(), 0, $this->posY, $fontStyle, $color);
    }

    /**
     * Marks the current page as first page of a table of content, see moveTableOfContent()
     */
    public function startTableOfContentPage(): void
    {
        $this->tableOfContentStartPage = $this->getPageId();
    }

    /**
     * Writes the table of content entries, page numbers already account for the table of content pages being moved.
     *
     * @param int $targetPage The page number the table of content is moved to
     * @param string $name The name of the bookmark pointing to the table of content, empty for none
     * @param Closure $renderEntry Function(BasePDF $pdf, array $bookmark, string $pageNumber) writing one entry
     */
    public function writeTableOfContent(int $targetPage, string $name, Closure $renderEntry): void
    {
        if ($this->tableOfContentStartPage < 0) {
            throw new ValidationException('The table of content must be placed on a table of content page. ERROR: 1759737603', 1759737603);
        }

        $this->tableOfContentTargetPage = $targetPage;
        $this->tableOfContentName = $name;

        $bookmarks = array_filter($this->outlines, fn (array $outline) => $outline['p'] < $this->tableOfContentStartPage);

        // The number of table of content pages is only known after rendering it, so it is rendered into a shadow first
        $shadow = $this->createShadow();
        $this->writeTableOfContentEntries($shadow, $bookmarks, $targetPage, 0, $renderEntry);
        $numberOfPages = $this->getPageId() - $this->tableOfContentStartPage + $shadow->getPageId() + 1;

        $this->writeTableOfContentEntries($this, $bookmarks, $targetPage, $numberOfPages, $renderEntry);
    }

    protected function writeTableOfContentEntries(BasePDF $pdf, array $bookmarks, int $targetPage, int $numberOfPages, Closure $renderEntry): void
    {
        foreach ($bookmarks as $bookmark) {
            $pageNumber = $bookmark['p'] + 1;

            if ($pageNumber >= $targetPage) {
                $pageNumber += $numberOfPages;
            }

            $renderEntry($pdf, $bookmark, (string) $pageNumber);
        }
    }

    /**
     * Moves the table of content pages starting with startTableOfContentPage() to the page number given in writeTableOfContent().
     */
    public function moveTableOfContent(): void
    {
        if ($this->tableOfContentStartPage < 0) {
            return;
        }

        $startPage = $this->tableOfContentStartPage;
        $endPage = $this->getPageId();
        $target = max(0, $this->tableOfContentTargetPage - 1);
        $name = $this->tableOfContentName;
        $this->tableOfContentStartPage = -1;
        $this->tableOfContentTargetPage = 1;
        $this->tableOfContentName = '';

        foreach (array_keys($this->page->getPages()) as $pid) {
            $this->finishPage($pid);
        }

        if ($target < $startPage) {
            for ($pid = $startPage; $pid <= $endPage; $pid++) {
                $this->page->move($pid, $target + $pid - $startPage);
            }

            // tc-lib-pdf does not update page references when moving pages
            $numberOfPages = $endPage - $startPage + 1;
            $movePage = function (int $pid) use ($startPage, $endPage, $target, $numberOfPages): int {
                if ($pid >= $startPage && $pid <= $endPage) {
                    return $pid - $startPage + $target;
                }

                return $pid >= $target && $pid < $startPage ? $pid + $numberOfPages : $pid;
            };

            foreach ([&$this->outlines, &$this->links, &$this->dests, &$this->deferredTexts] as &$references) {
                foreach ($references as &$reference) {
                    $reference['p'] = $movePage($reference['p']);
                }
                unset($reference);
            }
            unset($references);

            // PDF/UA structure elements reference their pages by "pid", also within nested kids
            $moveStructure = function (array $data) use (&$moveStructure, $movePage): array {
                foreach ($data as $key => $value) {
                    if ($key === 'pid' && is_int($value)) {
                        $data[$key] = $movePage($value);
                    } elseif (is_array($value)) {
                        $data[$key] = $moveStructure($value);
                    }
                }

                return $data;
            };
            $this->pdfuaStructLog = $moveStructure($this->pdfuaStructLog);
            $this->pdfuaStructStack = $moveStructure($this->pdfuaStructStack);
            $this->pdfuapagemcid = array_combine(array_map($movePage, array_keys($this->pdfuapagemcid)), $this->pdfuapagemcid);

            $this->setCurrentPage(count($this->page->getPages()) - 1);
        } else {
            $target = $startPage;
        }

        if ($name !== '') {
            $this->setBookmark($name, '', 0, $target, 0, 0);

            // insert the bookmark before the bookmarks of the following pages to keep the document order
            $bookmark = array_pop($this->outlines);
            $index = 0;

            while (isset($this->outlines[$index]) && $this->outlines[$index]['p'] < $target) {
                $index++;
            }

            array_splice($this->outlines, $index, 0, [$bookmark]);
        }
    }
}
