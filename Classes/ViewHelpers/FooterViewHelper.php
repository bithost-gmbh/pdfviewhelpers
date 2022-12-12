<?php

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

/**
 * FooterViewHelper
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
 */
class FooterViewHelper extends AbstractPDFViewHelper
{
    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('posY', 'integer', 'Absolute posY of the element on the current page. A negative value means it is measured from the bottom of the page.', false, $this->settings['footer']['posY']);
        $this->registerArgument('scope', 'string', 'The scope the footer is applied to: document, thisPage or thisPageIncludingPageBreaks.', false, null);
    }

    /**
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        if ($this->arguments['scope'] === null) {
            $this->arguments['scope'] = $this->viewHelperVariableContainer->get('DocumentViewHelper', 'defaultHeaderFooterScope');
        }
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    public function render()
    {
        $pdf = $this->getPDF();
        $arguments = $this->arguments;
        $renderChildrenClosure = $this->buildRenderChildrenClosure();
        $footerViewHelper = $this;
        $footerClosure = function () use ($pdf, $arguments, $renderChildrenClosure, $footerViewHelper) {
            $footerViewHelper->pushMultiColumnContext([]); // avoid interference of page and footer multi column context

            if ($arguments['posY']) {
                $pdf->setY($arguments['posY']);
            }

            $renderChildrenClosure();
            $footerViewHelper->popMultiColumnContext();
        };

        $pdf->setFooterClosure($footerClosure, $arguments['scope']);
    }
}
