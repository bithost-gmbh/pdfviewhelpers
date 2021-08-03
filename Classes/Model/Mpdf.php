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

/**
 * Extending Mpdf to integrate with EXT:pdfviewhelpers and TCPDF
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
 */
class Mpdf extends \Mpdf\Mpdf
{
    /**
     * @var BasePDF
     */
    protected $basePdf = null;

    /**
     * @param BasePDF $basePDF
     */
    public function setBasePdf(BasePDF $basePDF)
    {
        $this->basePdf = $basePDF;
    }

    /**
     * @inheritDoc
     */
    public function AddPage($orientation = '', $condition = '', $resetpagenum = '', $pagenumstyle = '', $suppress = '', $mgl = '', $mgr = '', $mgt = '', $mgb = '', $mgh = '', $mgf = '', $ohname = '', $ehname = '', $ofname = '', $efname = '', $ohvalue = 0, $ehvalue = 0, $ofvalue = 0, $efvalue = 0, $pagesel = '', $newformat = '')
    {
        $margins = $this->basePdf->getMargins();

        if (count($this->pages) === 0) {
            // First page, use current PosY as top margin
            return parent::AddPage($orientation, $condition, $resetpagenum, $pagenumstyle, $suppress, $margins['left'], $margins['right'], $this->basePdf->GetY(), $margins['bottom'], $mgh, $mgf, $ohname, $ehname, $ofname, $efname, $ohvalue, $ehvalue, $ofvalue, $efvalue, $pagesel, $newformat);
        } else {
            // All subsequent pages, use regular top margin
            return parent::AddPage($orientation, $condition, $resetpagenum, $pagenumstyle, $suppress, $margins['left'], $margins['right'], $margins['top'], $margins['bottom'], $mgh, $mgf, $ohname, $ehname, $ofname, $efname, $ohvalue, $ehvalue, $ofvalue, $efvalue, $pagesel, $newformat);
        }
    }
}
