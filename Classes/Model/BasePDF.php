<?php

namespace Bithost\Pdfviewhelpers\Model;

/***
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
 ***/

use setasign\Fpdi\Tcpdf\Fpdi;
use Closure;

/**
 * BasePDF
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
 */
class BasePDF extends Fpdi
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
    protected $pageHeaderScope = '';

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
    protected $pageFooterScope = '';

    /**
     * @var string
     */
    protected $currentPageFormat = '';

    /**
     * @var string
     */
    protected $currentTemplate = '';

    const SCOPE_THIS_PAGE = 'thisPage';
    const SCOPE_THIS_PAGE_INCLUDING_PAGE_BREAKS = 'thisPageIncludingPageBreaks';
    const SCOPE_DOCUMENT = 'document';

    /**
     * Storing the path of custom fonts to use them on setFont
     *
     * @var array
     */
    protected $customFontFilePaths = [];

    public function getCustomFontFilePaths()
    {
        return $this->customFontFilePaths;
    }

    public function setCustomFontFilePaths(array $customFontFilePaths)
    {
        $this->customFontFilePaths = $customFontFilePaths;
    }

    /**
     * @inheritdoc
     */
    protected function setHeader()
    {
        //disable TCPDF default behaviour in order to be able to overwrite header on page level
    }

    /**
     * @inheritdoc
     */
    public function endPage($tocpage = false)
    {
        parent::endPage($tocpage);

        if ($this->pageFooterScope === self::SCOPE_THIS_PAGE) {
            $this->pageFooterClosure = null; //remove page footer closure as it should only be used once
        }
    }

    /**
     * Custom method to call setHeader to render header after the children of a PageViewHelper are rendered.
     * This allows to overwrite the header within a PageViewHelper.
     *
     * @return void
     */
    public function renderHeader()
    {
        $graphicVars = $this->getGraphicVars();

        parent::setHeader();
        $this->setGraphicVars($graphicVars);
        $this->setPageMark();
    }

    /**
     * Use this method uf you extend BasePDF and want to render a header by accessing the TCPDF API directly
     *
     * @return void
     */
    protected function basePdfHeader()
    {
    }

    /**
     * Use this method uf you extend BasePDF and want to render a footer by accessing the TCPDF API directly
     *
     * @return void
     */
    protected function basePdfFooter()
    {
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function Footer() // phpcs:ignore
    {
        // See https://github.com/tecnickcom/TCPDF/pull/147
        $initialPageBreakTrigger = $this->PageBreakTrigger;
        $this->PageBreakTrigger = $this->h;

        if ($this->pageFooterClosure instanceof Closure) {
            $this->pageFooterClosure->__invoke();
        } else if ($this->documentFooterClosure instanceof Closure) {
            $this->documentFooterClosure->__invoke();
        }

        $this->basePdfFooter();
        $this->PageBreakTrigger = $initialPageBreakTrigger;
    }

    /**
     * @inheritdoc
     */
    public function AddPage($orientation = '', $format = '', $keepmargins = false, $tocpage = false) // phpcs:ignore
    {
        parent::AddPage($orientation, $format, $keepmargins, $tocpage);

        if ($this->isAutoPageBreak) {
            if ($this->pageHeaderScope === self::SCOPE_THIS_PAGE) {
                $this->pageHeaderClosure = null; //remove page header closure as it should only be used once
            }

            $this->renderHeader();
        }

        if ($this->importTemplateOnThisPage && $this->currentTemplate !== '') {
            $this->useTemplate($this->currentTemplate);
        }
    }

    /**
     * Overwrite useTemplate in order to save currentTemplate that can be used on automatic page breaks
     *
     * @inheritdoc
     */
    public function useTemplate($template, $x = 0, $y = 0, $width = null, $height = null, $adjustPageSize = false)
    {
        $this->currentTemplate = $template;

        parent::useTemplate($template, $x, $y, $width, $height, $adjustPageSize);
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

    /**
     * @inheritdoc
     */
    public function setPageFormat($format, $orientation = 'P')
    {
        $this->currentPageFormat = $format;

        parent::setPageFormat($format, $orientation);
    }

    /**
     * Overwrite SetFont in order to automatically provide paths to custom fonts
     *
     * @inheritdoc
     */
    public function SetFont($family, $style = '', $size = null, $fontfile = '', $subset = 'default', $out = true) // phpcs:ignore
    {
        if (empty($fontfile) && isset($this->customFontFilePaths[$family])) {
            $fontfile = $this->customFontFilePaths[$family];
        }

        parent::SetFont($family, $style, $size, $fontfile, $subset, $out);
    }

    /**
     * Overwrite AddFont in order to automatically provide paths to custom fonts
     *
     * @inheritdoc
     */
    public function AddFont($family, $style='', $fontfile='', $subset='default') // phpcs:ignore
    {
        if (empty($fontfile) && isset($this->customFontFilePaths[$family])) {
            $fontfile = $this->customFontFilePaths[$family];
        }

        return parent::AddFont($family, $style, $fontfile, $subset);
    }

    /**
     * @param $fontName
     * @param $fontFilePath
     */
    public function addCustomFontFilePath($fontName, $fontFilePath)
    {
        $this->fontlist[] = $fontName;
        $this->customFontFilePaths[$fontName] = $fontFilePath;
    }

    /**
     * @return integer
     */
    public function getInnerPageWidth()
    {
        $pageWidth = $this->getPageWidth();
        $pageMargins = $this->getMargins();

        return $pageWidth - $pageMargins['left'] - $pageMargins['right'];
    }

    /**
     * @return float
     */
    public function getScaledInnerPageHeight()
    {
        $margins = $this->getMargins();

        return $this->getScaledPageHeight() - $margins['top'] - $margins['bottom'];
    }

    /**
     * @param integer $page
     *
     * @return float;
     */
    public function getScaledPageHeight($page = null)
    {
        if ($page === null) {
            $page = $this->getPage();
        }

        return $this->getPageHeight($page) / $this->getScaleFactor();
    }

    /**
     * @return string
     */
    public function getCurrentPageOrientation()
    {
        return $this->CurOrientation;
    }

    /**
     * @return string
     */
    public function getCurrentPageFormat()
    {
        return $this->currentPageFormat;
    }

    /**
     * @return string
     */
    public function getPdfUnit()
    {
        return $this->pdfunit;
    }
}
