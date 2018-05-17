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

/**
 * EmptyFPDI, Needed because TCPDF adds a line to the header by default
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Marin <esteban.marin@bithost.ch>
 */
class EmptyFPDI extends FPDI
{
    /**
     * Indicating whether the current page is using a template.
     * This is needed to automatically add the template on an automatic page break.
     *
     * @var bool
     */
    protected $importTemplateOnThisPage = false;

    /**
     * @return void
     */
    public function Header() // phpcs:ignore
    {
    }

    /**
     * @return void
     */
    public function Footer() // phpcs:ignore
    {
    }

    /**
     * Fixes importPage not working with autoPageBreak=1, see https://github.com/bithost-gmbh/pdfviewhelpers/issues/41
     *
     * @inheritdoc
     */
    public function AddPage($orientation = '', $format = '', $rotationOrKeepmargins = false, $tocpage = false) // phpcs:ignore
    {
        parent::AddPage($orientation, $format, $rotationOrKeepmargins, $tocpage);

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
}
