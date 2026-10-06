<?php

declare(strict_types=1);

namespace Bithost\Pdfviewhelpers\ViewHelpers;

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

use Bithost\Pdfviewhelpers\Exception\Exception;
use Bithost\Pdfviewhelpers\Model\BasePDF;

/**
 * TableOfContentViewHelper
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
 */
class TableOfContentViewHelper extends AbstractPDFViewHelper
{
    /**
     * @inheritDoc
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();

        $this->registerArgument('page', 'integer', 'Indicates at what place in the document the table of content will be rendered.', false, $this->settings['tableOfContent']['page']);
        $this->registerArgument('numbersFont', 'string', 'The font used to render the numbers. Note that a monospaced font must be used in order to guarantee correct alignment.', false, $this->settings['tableOfContent']['numbersFont']);
        $this->registerArgument('filter', 'string', 'The filter used to fill up the space between the entry title and the page number.', false, $this->settings['tableOfContent']['filter']);
        $this->registerArgument('name', 'string', 'The name used for the table of content bookmark.', false, $this->settings['tableOfContent']['name']);
        $this->registerArgument('htmlMode', 'boolean', 'If true HTML mode is used.', false, $this->settings['tableOfContent']['htmlMode']);
        $this->registerArgument('fontFamily', 'string', 'The font family for the entries.', false, $this->settings['tableOfContent']['fontFamily']);
        $this->registerArgument('fontSize', 'float', 'The font size for the entries.', false, $this->settings['tableOfContent']['fontSize']);
        $this->registerArgument('lineHeight', 'float', 'The line height for the entries.', false, $this->settings['tableOfContent']['lineHeight']);
        $this->registerArgument('characterSpacing', 'float', 'The character spacing for the entries.', false, $this->settings['tableOfContent']['characterSpacing']);
        $this->registerArgument('padding', 'array', 'The padding for the entries.', false, []);
    }

    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();

        if (empty($this->arguments['numbersFont'])) {
            $this->arguments['numbersFont'] = $this->settings['generalText']['fontFamily'];
        }

        if (empty($this->arguments['fontFamily'])) {
            $this->arguments['fontFamily'] = $this->settings['generalText']['fontFamily'];
        }

        if (empty($this->arguments['fontSize'])) {
            $this->arguments['fontSize'] = $this->settings['generalText']['fontSize'];
        }

        if (empty($this->arguments['lineHeight'])) {
            $this->arguments['lineHeight'] = $this->settings['generalText']['lineHeight'];
        }

        if (empty($this->arguments['characterSpacing'])) {
            $this->arguments['characterSpacing'] = $this->settings['generalText']['characterSpacing'];
        }

        $this->arguments['padding'] = array_merge($this->settings['tableOfContent']['padding'] ?? [], $this->arguments['padding']);

        $this->viewHelperVariableContainer->addOrUpdate('TableOfContentViewHelper', 'bookmarkTemplates', []);
    }

    /**
     * @inheritDoc
     *
     * @throws Exception
     */
    public function render(): void
    {
        $pdf = $this->getPDF();
        $pdf->setFont($this->arguments['fontFamily'], '', (float) $this->arguments['fontSize']);
        $pdf->setLineHeight((float) $this->arguments['lineHeight']);
        $pdf->setCharacterSpacing((float) $this->arguments['characterSpacing']);
        $pdf->setCellPadding(0, 0);
        $pdf->setTextColor($this->settings['generalText']['color']);

        if ($this->arguments['htmlMode']) {
            $this->renderChildren();
            $bookmarkTemplates = $this->viewHelperVariableContainer->get('TableOfContentViewHelper', 'bookmarkTemplates');

            if (empty($bookmarkTemplates)) {
                throw new Exception('No html bookmark templates found, please add templates using the HtmlBookmarkTemplateViewHelper. ERROR: 1555308328', 1555308328);
            }

            $renderEntry = fn (BasePDF $pdf, array $bookmark, string $pageNumber) => $this->renderHtmlEntry($pdf, $bookmark, $pageNumber, $bookmarkTemplates);
        } else {
            $renderEntry = $this->renderTextEntry(...);
        }

        $pdf->writeTableOfContent((int) $this->arguments['page'], (string) $this->arguments['name'], $renderEntry);
    }

    /**
     * Renders an entry like "Title ........ 3" with the page number aligned right
     */
    protected function renderTextEntry(BasePDF $pdf, array $bookmark, string $pageNumber): void
    {
        $padding = $this->arguments['padding'];
        $textStyle = $pdf->getTextStyle();
        $pdf->setY($pdf->getY() + $padding['top']);

        // the first level is bold, deeper levels get smaller and indented
        $pdf->setFont($this->arguments['fontFamily'], $bookmark['l'] === 0 ? $bookmark['s'] . 'B' : $bookmark['s'], $this->arguments['fontSize'] - $bookmark['l']);
        $pdf->setTextColor($bookmark['c'] ?: $textStyle['color']);

        $posX = $pdf->getMargins()['left'] + $padding['left'] + $pdf->getTextWidth('    ') * $bookmark['l'];
        $width = $pdf->getPageWidth() - $pdf->getMargins()['right'] - $padding['right'] - $posX;

        $pdf->setFont($this->arguments['numbersFont'], '');
        $numberWidth = $pdf->getTextWidth(' ' . $pageNumber);
        $fillerWidth = $this->arguments['filter'] === '' ? 0 : $pdf->getTextWidth($this->arguments['filter']);

        $pdf->setFont($this->arguments['fontFamily'], $bookmark['l'] === 0 ? $bookmark['s'] . 'B' : $bookmark['s']);
        $title = $bookmark['t'] . ' ';
        $titleWidth = $pdf->getTextWidth($title);
        $firstLine = $pdf->writeText($title, $posX, $pdf->getY(), $width - $numberWidth);
        $endPage = $pdf->getPageId();
        $endY = $pdf->getY();

        $filler = '';

        if ($fillerWidth > 0 && $titleWidth < $width - $numberWidth) {
            $filler = str_repeat($this->arguments['filter'], (int) floor(($width - $numberWidth - $titleWidth) / $fillerWidth));
        }

        // the page number is written once the document is complete, as pages might still be moved by another table of content
        $link = $pdf->addInternalLink($bookmark['p'], $bookmark['y']);
        $pdf->setPageId($firstLine['page']);
        $pdf->setFont($this->arguments['numbersFont'], '');
        $pdf->writeLinkPageNumber($filler . ' #PAGE#', $link, $posX, $firstLine['y'], $width);
        $pdf->page->addAnnotRef($pdf->setLink($posX, $firstLine['y'], $width, $pdf->getLineHeight(), $link));

        $pdf->setTextStyle($textStyle);
        $pdf->setPageId($endPage);
        $pdf->setY($endY + $padding['bottom']);
    }

    /**
     * Renders an entry using the HTML template of its level, the markers #TOC_DESCRIPTION# and #TOC_PAGE_NUMBER# are replaced
     */
    protected function renderHtmlEntry(BasePDF $pdf, array $bookmark, string $pageNumber, array $bookmarkTemplates): void
    {
        $template = $bookmarkTemplates[$bookmark['l']] ?? $bookmarkTemplates[max(array_keys($bookmarkTemplates))];
        $html = str_replace(['#TOC_DESCRIPTION#', '#TOC_PAGE_NUMBER#'], [htmlspecialchars($bookmark['t']), $pageNumber], $template);
        $posX = $pdf->getMargins()['left'];
        $startPage = $pdf->getPageId();
        $startY = $pdf->getY();

        $pdf->writeHtml($html, $posX, $startY, $pdf->getInnerPageWidth());

        if ($startPage === $pdf->getPageId()) {
            $link = $pdf->addInternalLink($bookmark['p'], $bookmark['y']);
            $pdf->page->addAnnotRef($pdf->setLink($posX, $startY, $pdf->getInnerPageWidth(), $pdf->getY() - $startY, $link));
        }
    }
}
