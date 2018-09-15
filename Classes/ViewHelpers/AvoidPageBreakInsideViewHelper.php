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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * AvoidPageBreakInsideViewHelper
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Marin <esteban.marin@bithost.ch>
 */
class AvoidPageBreakInsideViewHelper extends AbstractPDFViewHelper
{
    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('breakIfImpossibleToAvoid', 'boolean', '', false, $this->settings['avoidPageBreakInside']['breakIfImpossibleToAvoid']);
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    public function render()
    {
        $realPDF = $this->getPDF();
        $shadowPDF = $this->prepareShadowPdf($realPDF);
        $startPage = $shadowPDF->getPage();

        $shadowPDF->SetY($realPDF->GetY());
        $this->setPDF($shadowPDF);
        $this->renderChildren();
        $this->setPDF($realPDF);

        $endPage = $shadowPDF->getPage();
        $pageStep = $endPage - $startPage;

        if ($pageStep > 0) {
            if ($this->arguments['breakIfImpossibleToAvoid']) {
                //we always break, even if a page break is unavoidable because the content is too long
                $realPDF->AddPage();
            } else if ($pageStep === 1) {
                //only break if the content fits on the next page
                $firstPageChildrenHeight = $realPDF->getScaledPageHeight($startPage) - $realPDF->GetY() - $realPDF->getBreakMargin($startPage);
                $secondPageChildrenHeight = $shadowPDF->GetY() - $realPDF->getMargins()['top'];
                $totalChildrenHeight = $firstPageChildrenHeight + $secondPageChildrenHeight;
                $innerPageHeight = $realPDF->getScaledInnerPageHeight();

                if ($totalChildrenHeight < $innerPageHeight) {
                    $realPDF->AddPage();
                }
            } else {
                //Content is longer than one page, do not add page break
            }
        }

        $this->renderChildren();
    }

    /**
     * @param BasePDF $realPdf
     *
     * @return BasePDF
     */
    protected function prepareShadowPdf(BasePDF $realPdf)
    {
        /** @var BasePDF $shadowPDF */
        $shadowPDF = GeneralUtility::makeInstance(
            $this->settings['config']['class'],
            $realPdf->getCurrentPageOrientation(),
            $realPdf->getPdfUnit(),
            $realPdf->getCurrentPageFormat()
        );
        $realPdfMargins = $realPdf->getMargins();

        $shadowPDF->SetMargins($realPdfMargins['left'], $realPdfMargins['top'], $realPdfMargins['right']);
        $shadowPDF->SetAutoPageBreak(true, $realPdfMargins['bottom']);
        $shadowPDF->AddPage();

        return $shadowPDF;
    }
}
