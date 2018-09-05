<?php

namespace Bithost\Pdfviewhelpers\Model;

/***
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
 ***/

use FPDI;
use Closure;

/**
 * BasePDF
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Marin <esteban.marin@bithost.ch>
 */
class BasePDF extends FPDI
{
    /**
     * Indicating whether the current page is using a template.
     * This is needed to automatically add the template on an automatic page break.
     *
     * @var bool
     */
    protected $importTemplateOnThisPage = false;

    /**
     * Document wide HeaderViewHelper
     *
     * @var Closure
     */
    protected $documentHeaderClosure = null;

    /**
     * Page wide HeaderViewHelper, overwriting Document wide HeaderViewHelper
     *
     * @var Closure
     */
    protected $pageHeaderClosure = null;

    /**
     * @var string
     */
    protected $pageHeaderScope = null;

    /**
     * Document wide FooterViewHelper
     *
     * @var Closure
     */
    protected $documentFooterClosure = null;

    /**
     * Page wide FooterViewHelper, overwriting Document wide FooterViewHelper
     *
     * @var Closure
     */
    protected $pageFooterClosure = null;

    /**
     * Indicating whether page break is triggered by PageViewHelper or an auto page break
     *
     * @var boolean
     */
    protected $isAutoPageBreak = false;

    /**
     * @var string
     */
    protected $pageFooterScope = null;

    const SCOPE_THIS_PAGE = 'thisPage';
    const SCOPE_THIS_PAGE_INCLUDING_PAGE_BREAKS = 'thisPageIncludingPageBreaks';
    const SCOPE_DOCUMENT = 'document';

    /**
     * @return void
     */
    protected function setHeader()
    {
        //disable TCPDF default behaviour in order to be able to overwrite header and footer on page level
    }

    /**
     * @return void
     */
    protected function setFooter()
    {
        //disable TCPDF default behaviour in order to be able to overwrite header and footer on page level
    }

    /**
     * Custom method to call setHeader to render header after the children of a PageViewHelper are rendered.
     * This allows to overwrite header within a PageViewHelper.
     *
     * @return void
     */
    public function renderHeader()
    {
        parent::setHeader();
    }

    /**
     * Custom method to call setFooter to render footer after the children of a PageViewHelper are rendered.
     * This allows to overwrite footer within a PageViewHelper.
     *
     * @return void
     */
    public function renderFooter()
    {
        parent::setFooter();
    }

    /**
     * Use this method uf you extend BasePDF and want to render a header by accessing the TCPDF API directly
     *
     * @return void
     */
    public function basePdfHeader()
    {
    }

    /**
     * Use this method uf you extend BasePDF and want to render a footer by accessing the TCPDF API directly
     *
     * @return void
     */
    public function basePdfFooter()
    {
    }

    /**
     * @return void
     */
    public function Header() // phpcs:ignore
    {
        if ($this->pageHeaderClosure instanceof Closure) {
            $this->pageHeaderClosure->__invoke();
        } else if ($this->documentHeaderClosure instanceof Closure) {
            $this->documentHeaderClosure->__invoke();
        }

        $this->basePdfHeader();
    }

    /**
     * @return void
     */
    public function Footer() // phpcs:ignore
    {
        if ($this->pageFooterClosure instanceof Closure) {
            $this->pageFooterClosure->__invoke();
        } else if ($this->documentFooterClosure instanceof Closure) {
            $this->documentFooterClosure->__invoke();
        }

        $this->basePdfFooter();
    }

    /**
     * Fixes importPage not working with autoPageBreak=1, see https://github.com/bithost-gmbh/pdfviewhelpers/issues/41
     *
     * @return void
     */
    public function AddPage($orientation = '', $format = '', $rotationOrKeepmargins = false, $tocpage = false) // phpcs:ignore
    {
        parent::AddPage($orientation, $format, $rotationOrKeepmargins, $tocpage);

        if ($this->isAutoPageBreak) {
            if ($this->pageHeaderScope === self::SCOPE_THIS_PAGE) {
                $this->pageHeaderClosure = null; //remove page header closure as it should only be used once
            }

            if ($this->pageFooterScope === self::SCOPE_THIS_PAGE) {
                $this->pageFooterClosure = null; //remove page footer closure as it should only be used once
            }

            $this->renderHeader();
            $this->renderFooter();
        }

        if ($this->importTemplateOnThisPage && $this->tpl !== 0) {
            $this->useTemplate($this->tpl);
        }
    }

    /**
     * @param bool $importTemplateOnThisPage
     *
     * @return void
     */
    public function setImportTemplateOnThisPage($importTemplateOnThisPage)
    {
        $this->importTemplateOnThisPage = $importTemplateOnThisPage;
    }

    /**
     * @param Closure $closure
     * @param string $scope
     *
     * @return void
     */
    public function setHeaderClosure(Closure $closure = null, $scope = self::SCOPE_DOCUMENT)
    {
        if ($scope === self::SCOPE_DOCUMENT) {
            $this->documentHeaderClosure = $closure;
        } else if (in_array($scope, [self::SCOPE_THIS_PAGE, self::SCOPE_THIS_PAGE_INCLUDING_PAGE_BREAKS])) {
            $this->pageHeaderScope = $scope;
            $this->pageHeaderClosure = $closure;
        }
    }

    /**
     * @param Closure $closure
     * @param string $scope
     *
     * @return void
     */
    public function setFooterClosure(Closure $closure = null, $scope = self::SCOPE_DOCUMENT)
    {
        if ($scope === self::SCOPE_DOCUMENT) {
            $this->documentFooterClosure = $closure;
        } else if (in_array($scope, [self::SCOPE_THIS_PAGE, self::SCOPE_THIS_PAGE_INCLUDING_PAGE_BREAKS])) {
            $this->pageFooterScope = $scope;
            $this->pageFooterClosure = $closure;
        }
    }

    /**
     * @param boolean $isAutoPageBreak
     */
    public function setIsAutoPageBreak($isAutoPageBreak)
    {
        $this->isAutoPageBreak = $isAutoPageBreak;
    }
}
