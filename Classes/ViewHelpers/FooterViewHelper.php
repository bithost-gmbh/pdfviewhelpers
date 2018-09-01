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
 * FooterViewHelper
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Marin <esteban.marin@bithost.ch>
 */
class FooterViewHelper extends AbstractContentElementViewHelper
{
    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->overrideArgument('posY', 'integer', '', false, $this->settings['footer']['posY']);
    }

    /**
     * @throws Exception
     *
     * @return void
     */
    public function render()
    {
        if (!($this->getPDF() instanceof BasePDF)) {
            throw new  Exception("Your PDF class must be an instance of Bithost\\Pdfviewhelpers\\Model\\BasePDF in order to support Header and Footer ViewHelper.");
        }

        $arguments = $this->arguments;
        $renderChildrenClosure = $this->buildRenderChildrenClosure();
        $pdf = $this->getPDF();
        $footerClosure = function() use ($pdf, $arguments, $renderChildrenClosure) {
            if ($arguments['posY']) {
                $pdf->SetY($arguments['posY']);
            }

            $renderChildrenClosure();
        };

        $this->getPDF()->setFooterClosure($footerClosure);
    }
}
