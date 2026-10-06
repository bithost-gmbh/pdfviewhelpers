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
 * PageViewHelper
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
 */
class PageViewHelper extends AbstractPDFViewHelper
{
    /**
     * @inheritDoc
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();

        $this->registerArgument('autoPageBreak', 'boolean', 'If true a new page is added if the content exceeds the current page.', false, $this->settings['page']['autoPageBreak']);
        $this->registerArgument('margin', 'array', 'The margin of this page.', false, []);
        $this->registerArgument('importPage', 'integer', 'The page number to be imported as template.', false, $this->settings['page']['importPage']);
        $this->registerArgument('importPageOnAutomaticPageBreak', 'boolean', 'If true the template is also applied if an automatic page break occurs.', false, $this->settings['page']['importPageOnAutomaticPageBreak']);
        $this->registerArgument('orientation', 'string', 'The orientation of the page: portrait, landscape', false, $this->settings['page']['orientation']);
        $this->registerArgument('format', 'string|array', 'The format of the page, e.g. A4 or an array of [width, height]', false, $this->settings['page']['format']);
        $this->registerArgument('keepMargins', 'boolean', 'If true overwrites the default page margins with the current margins.', false, $this->settings['page']['keepMargins']);
        $this->registerArgument('tableOfContentPage', 'boolean', 'If true the page will be rendered as a table of content page, e.g. it can be moved to the front.', false, $this->settings['page']['tableOfContentPage']);
    }

    /**
     * @inheritDoc
     *
     * @throws Exception
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->arguments['margin'] = array_merge($this->settings['page']['margin'] ?? [], $this->arguments['margin']);
        $this->arguments['orientation'] = $this->conversionService->convertSpeakingOrientationToTcpdfOrientation($this->arguments['orientation']);

        $this->setDefaultHeaderFooterScope(BasePDF::SCOPE_THIS_PAGE_INCLUDING_PAGE_BREAKS);
    }

    /**
     * @inheritDoc
     *
     * @throws Exception
     */
    public function render(): void
    {
        $pdf = $this->getPDF();
        $template = null;

        if (!empty($this->arguments['importPage'])) {
            try {
                $template = $pdf->importSourcePage((int) $this->arguments['importPage']);
            } catch (\Exception $e) {
                throw new Exception('Could not import page. ' . $e->getMessage() . ' ERROR: 1538067206 ', 1538067206, $e);
            }
        }

        $margins = $this->arguments['margin'];

        if ($this->arguments['keepMargins'] && $pdf->page->getPages() !== []) {
            // like TCPDF only the horizontal margins of the current page are kept
            $margins['left'] = $pdf->getMargins()['left'];
            $margins['right'] = $pdf->getMargins()['right'];
        }

        $pdf->setIsAutoPageBreak(false);
        $pdf->setImportTemplateOnThisPage(false);
        $pdf->addPageWithSettings($this->arguments['orientation'], $this->arguments['format'], $margins, (bool) $this->arguments['autoPageBreak']);

        if ($template !== null) {
            $pdf->drawTemplate($template);
        }

        if ($this->arguments['tableOfContentPage']) {
            $pdf->startTableOfContentPage();
        }

        $this->setPageNeedsHeader(true);
        $pdf->setIsAutoPageBreak(true);
        $pdf->setHeaderClosure(null, BasePDF::SCOPE_THIS_PAGE_INCLUDING_PAGE_BREAKS);
        $pdf->setFooterClosure(null, BasePDF::SCOPE_THIS_PAGE_INCLUDING_PAGE_BREAKS);
        $pdf->setCurrentTemplate($template);
        $pdf->setImportTemplateOnThisPage($template !== null && $this->arguments['importPageOnAutomaticPageBreak']);

        $this->renderChildren();

        if ($this->pageNeedsHeader()) {
            //no auto page break occurred or no element was rendered, we still need to set the header
            $this->setPageNeedsHeader(false);
            $pdf->renderHeader();
        }

        if ($this->arguments['tableOfContentPage']) {
            $pdf->moveTableOfContent();
        }

        //reset default header and footer scope to document
        $this->setDefaultHeaderFooterScope(BasePDF::SCOPE_DOCUMENT);
    }

    protected function setDefaultHeaderFooterScope(string $scope): void
    {
        $this->viewHelperVariableContainer->addOrUpdate('DocumentViewHelper', 'defaultHeaderFooterScope', $scope);
    }

    protected function setPageNeedsHeader(bool $needsHeader): void
    {
        $this->viewHelperVariableContainer->addOrUpdate('DocumentViewHelper', 'pageNeedsHeader', $needsHeader);
    }

    protected function pageNeedsHeader(): bool
    {
        return (bool) $this->viewHelperVariableContainer->get('DocumentViewHelper', 'pageNeedsHeader');
    }
}
