<?php

namespace Bithost\Pdfviewhelpers\ViewHelpers;

/* * *
 *
 * This file is part of the "PDF ViewHelpers" Extension for TYPO3 CMS.
 *
 *  (c) 2016 Markus Mächler <markus.maechler@bithost.ch>, Bithost GmbH
 *           Esteban Marin <esteban.marin@bithost.ch>, Bithost GmbH
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
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Marin <esteban.marin@bithost.ch>
 */
class PageViewHelper extends AbstractPDFViewHelper
{
    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('autoPageBreak', 'boolean', '', false, $this->settings['page']['autoPageBreak']);
        $this->registerArgument('margin', 'array', '', false, []);
        $this->registerArgument('importPage', 'integer', '', false, $this->settings['page']['importPage']);
        $this->registerArgument('importPageOnAutomaticPageBreak', 'boolean', '', false, $this->settings['page']['importPageOnAutomaticPageBreak']);
        $this->registerArgument('orientation', 'string', '', false, $this->settings['page']['orientation']);
        $this->registerArgument('format', 'string', '', false, $this->settings['page']['format']);
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    public function initialize()
    {
        parent::initialize();

        $this->arguments['margin'] = array_merge($this->settings['page']['margin'], $this->arguments['margin']);
        $this->arguments['orientation'] = $this->conversionService->convertSpeakingOrientationToTcpdfOrientation($this->arguments['orientation']);

        $this->setDefaultHeaderFooterScope(BasePDF::SCOPE_THIS_PAGE_INCLUDING_PAGE_BREAKS);
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    public function render()
    {
        $templateId = -1;
        $hasImportedPage = !empty($this->arguments['importPage']);

        if ($hasImportedPage) {
            try {
                $templateId = $this->getPDF()->importPage($this->arguments['importPage']);
            } catch (\Exception $e) {
                throw new Exception('Could not import page. ' . $e->getMessage() . ' ERROR: 1538067206 ', 1538067206, $e);
            }
        }

        $this->getPDF()->setIsAutoPageBreak(false);
        $this->getPDF()->setImportTemplateOnThisPage(false);
        $this->getPDF()->SetMargins($this->arguments['margin']['left'], $this->arguments['margin']['top'], $this->arguments['margin']['right']);
        $this->getPDF()->SetAutoPageBreak($this->arguments['autoPageBreak'], $this->arguments['margin']['bottom']);

        $this->getPDF()->AddPage($this->arguments['orientation'], $this->arguments['format']);

        if ($hasImportedPage) {
            $this->getPDF()->setCurrentTemplateId($templateId);
            $this->getPDF()->useTemplate($templateId);
        }

        $this->setPageNeedsHeader(true);
        $this->getPDF()->setIsAutoPageBreak(true);
        $this->getPDF()->setHeaderClosure(null, BasePDF::SCOPE_THIS_PAGE_INCLUDING_PAGE_BREAKS);
        $this->getPDF()->setFooterClosure(null, BasePDF::SCOPE_THIS_PAGE_INCLUDING_PAGE_BREAKS);
        $this->getPDF()->setImportTemplateOnThisPage($hasImportedPage && $this->arguments['importPageOnAutomaticPageBreak']);

        $this->renderChildren();

        if ($this->pageNeedsHeader()) {
            //no auto page break occurred or no element was rendered, we still need to set the header
            $this->setPageNeedsHeader(false);
            $this->getPDF()->renderHeader();
        }

        //reset default header and footer scope to document
        $this->setDefaultHeaderFooterScope(BasePDF::SCOPE_DOCUMENT);
    }

    /**
     * @param string $scope
     */
    protected function setDefaultHeaderFooterScope($scope)
    {
        $this->viewHelperVariableContainer->addOrUpdate('DocumentViewHelper', 'defaultHeaderFooterScope', $scope);
    }

    /**
     * @param boolean $needsHeader
     */
    protected function setPageNeedsHeader($needsHeader)
    {
        $this->viewHelperVariableContainer->addOrUpdate('DocumentViewHelper', 'pageNeedsHeader', $needsHeader);
    }

    /**
     * @return boolean
     */
    protected function pageNeedsHeader()
    {
        return $this->viewHelperVariableContainer->get('DocumentViewHelper', 'pageNeedsHeader');
    }
}
