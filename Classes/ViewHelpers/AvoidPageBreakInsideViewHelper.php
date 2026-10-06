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


/**
 * AvoidPageBreakInsideViewHelper
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
 */
class AvoidPageBreakInsideViewHelper extends AbstractPDFViewHelper
{
    /**
     * @inheritDoc
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();

        $this->registerArgument('breakIfImpossibleToAvoid', 'boolean', 'If true a line break is inserted even if a page break is not avoidable.', false, $this->settings['avoidPageBreakInside']['breakIfImpossibleToAvoid']);
    }

    /**
     * @inheritDoc
     */
    public function render(): void
    {
        $realPDF = $this->getPDF();
        $startPage = $realPDF->getPageId();
        $startY = $realPDF->getY();

        // render the children into a shadow document in order to find out whether they break across pages
        $shadowPDF = $realPDF->createShadow();
        $pageNeedsHeader = $this->viewHelperVariableContainer->get('DocumentViewHelper', 'pageNeedsHeader');
        $this->setPDF($shadowPDF);
        $this->renderChildren();
        $this->setPDF($realPDF);
        $this->viewHelperVariableContainer->addOrUpdate('DocumentViewHelper', 'pageNeedsHeader', $pageNeedsHeader);

        $pageStep = $shadowPDF->getPageId();

        if ($pageStep > 0) {
            if ($this->arguments['breakIfImpossibleToAvoid']) {
                //we always break, even if a page break is unavoidable because the content is too long
                $realPDF->nextPage();
            } elseif ($pageStep === 1) {
                //only break if the content fits on the next page
                $firstPageChildrenHeight = $realPDF->getPageBreakY() - $startY;
                $secondPageChildrenHeight = $shadowPDF->getY() - $realPDF->getMargins()['top'];

                if ($firstPageChildrenHeight + $secondPageChildrenHeight <= $realPDF->getInnerPageHeight()) {
                    $realPDF->nextPage();
                }
            }
            //else the content is longer than one page, do not add page break
        }

        $this->renderChildren();
    }
}
