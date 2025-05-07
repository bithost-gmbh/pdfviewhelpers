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

use Closure;
use setasign\Fpdi\PdfParser\StreamReader;
use setasign\Fpdi\Tcpdf\Fpdi;

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
     */
    protected bool $importTemplateOnThisPage = false;

    /**
     * Document wide HeaderViewHelper
     */
    protected ?\Closure $documentHeaderClosure = null;

    /**
     * Page wide HeaderViewHelper, overwriting Document wide HeaderViewHelper
     */
    protected ?\Closure $pageHeaderClosure = null;

    protected string $pageHeaderScope = '';

    /**
     * Document wide FooterViewHelper
     */
    protected ?\Closure $documentFooterClosure = null;

    /**
     * Page wide FooterViewHelper, overwriting Document wide FooterViewHelper
     */
    protected ?\Closure $pageFooterClosure = null;

    protected string $pageFooterScope = '';

    /**
     * Indicating whether page break is triggered by PageViewHelper or an auto page break
     */
    protected bool $isAutoPageBreak = false;

    /**
     * @var string|array
     */
    protected $currentPageFormat = '';
    protected string $currentTemplate = '';

    public const SCOPE_THIS_PAGE = 'thisPage';
    public const SCOPE_THIS_PAGE_INCLUDING_PAGE_BREAKS = 'thisPageIncludingPageBreaks';
    public const SCOPE_DOCUMENT = 'document';

    /**
     * Storing the path of custom fonts to use them on setFont
     */
    protected array $customFontFilePaths = [];

    /**
     * @var string|resource|StreamReader|null $currentSourceFile
     */
    protected $currentSourceFile;

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
     */
    public function renderHeader(): void
    {
        $graphicVars = $this->getGraphicVars();

        parent::setHeader();
        $this->setGraphicVars($graphicVars);
    }

    /**
     * Use this method uf you extend BasePDF and want to render a header by accessing the TCPDF API directly
     */
    protected function basePdfHeader(): void {}

    /**
     * Use this method uf you extend BasePDF and want to render a footer by accessing the TCPDF API directly
     */
    protected function basePdfFooter(): void {}

    /**
     * @inheritdoc
     */
    public function Header() // phpcs:ignore
    {
        if ($this->pageHeaderClosure instanceof \Closure) {
            $this->pageHeaderClosure->__invoke();
        } elseif ($this->documentHeaderClosure instanceof \Closure) {
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

        if ($this->pageFooterClosure instanceof \Closure) {
            $this->pageFooterClosure->__invoke();
        } elseif ($this->documentFooterClosure instanceof \Closure) {
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
            $this->setPageMark();
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

    public function setImportTemplateOnThisPage(bool $importTemplateOnThisPage): void
    {
        $this->importTemplateOnThisPage = $importTemplateOnThisPage;
    }

    public function setHeaderClosure(?\Closure $closure = null, string $scope = self::SCOPE_DOCUMENT): void
    {
        if ($scope === self::SCOPE_DOCUMENT) {
            $this->documentHeaderClosure = $closure;
        } elseif (in_array($scope, [self::SCOPE_THIS_PAGE, self::SCOPE_THIS_PAGE_INCLUDING_PAGE_BREAKS])) {
            $this->pageHeaderScope = $scope;
            $this->pageHeaderClosure = $closure;
        }
    }

    public function setFooterClosure(?\Closure $closure = null, string $scope = self::SCOPE_DOCUMENT): void
    {
        if ($scope === self::SCOPE_DOCUMENT) {
            $this->documentFooterClosure = $closure;
        } elseif (in_array($scope, [self::SCOPE_THIS_PAGE, self::SCOPE_THIS_PAGE_INCLUDING_PAGE_BREAKS])) {
            $this->pageFooterScope = $scope;
            $this->pageFooterClosure = $closure;
        }
    }

    public function setIsAutoPageBreak(bool $isAutoPageBreak)
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
        $familyWithStyle = $family . strtolower($style);

        if (empty($fontfile) && isset($this->customFontFilePaths[$familyWithStyle])) {
            $fontfile = $this->customFontFilePaths[$familyWithStyle];
        } elseif (empty($fontfile) && isset($this->customFontFilePaths[$family])) {
            $fontfile = $this->customFontFilePaths[$family];
        }

        parent::SetFont($family, $style, $size, $fontfile, $subset, $out);
    }

    /**
     * Overwrite AddFont in order to automatically provide paths to custom fonts
     *
     * @inheritdoc
     */
    public function AddFont($family, $style = '', $fontfile = '', $subset = 'default') // phpcs:ignore
    {
        $familyWithStyle = $family . strtolower($style);

        if (empty($fontfile) && isset($this->customFontFilePaths[$familyWithStyle])) {
            $fontfile = $this->customFontFilePaths[$familyWithStyle];
        } elseif (empty($fontfile) && isset($this->customFontFilePaths[$family])) {
            $fontfile = $this->customFontFilePaths[$family];
        }

        return parent::AddFont($family, $style, $fontfile, $subset);
    }

    public function addCustomFontFilePath(string $fontName, string $fontFilePath): void
    {
        $this->fontlist[] = $fontName;
        $this->customFontFilePaths[$fontName] = $fontFilePath;
    }

    public function setCustomFontFilePaths(array $customFontFilePaths): void
    {
        $this->customFontFilePaths = $customFontFilePaths;
    }

    /**
     * @inheritdoc
     */
    public function setSourceFile($file)
    {
        $this->currentSourceFile = $file;

        return parent::setSourceFile($file);
    }

    public function getSourceFile()
    {
        return $this->currentSourceFile;
    }

    public function getCustomFontFilePaths(): array
    {
        return $this->customFontFilePaths;
    }

    public function getInnerPageWidth(): float
    {
        $pageWidth = $this->getPageWidth();
        $pageMargins = $this->getMargins();

        return (float)$pageWidth - $pageMargins['left'] - $pageMargins['right'];
    }

    public function getScaledInnerPageHeight(): float
    {
        $margins = $this->getMargins();

        return (float)$this->getScaledPageHeight() - $margins['top'] - $margins['bottom'];
    }

    public function getScaledPageHeight(?int $page = null): float
    {
        if ($page === null) {
            $page = $this->getPage();
        }

        return (float)$this->getPageHeight($page) / $this->getScaleFactor();
    }

    public function getCurrentPageOrientation(): string
    {
        return $this->CurOrientation;
    }

    public function getCurrentPageFormat(): string
    {
        return $this->currentPageFormat;
    }

    public function getPdfUnit(): string
    {
        return $this->pdfunit;
    }

    /**
     * Disable the ad-link in the bottom left corner
     */
    public function disableTcpdfLink(): void
    {
        $this->tcpdflink = false;
    }
}
