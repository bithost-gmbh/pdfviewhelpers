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

/**
 * ColumnViewHelper
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Marin <esteban.marin@bithost.ch>
 */
class ColumnViewHelper extends AbstractPDFViewHelper
{
    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('width', 'string', 'The width of this column in the current unit or percentage.', false, null);
        $this->registerArgument('padding', 'array', 'The padding of this column given as array.', false, []);
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    public function initialize()
    {
        parent::initialize();

        $multiColumnContext = $this->getCurrentMultiColumnContext();
        $this->arguments['padding'] = array_merge(['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0], $this->arguments['padding']);

        $this->validationService->validatePadding($this->arguments['padding']);

        if (strlen($this->arguments['width'])) {
            if ($this->validationService->validateWidth($this->arguments['width'])) {
                $multiColumnContext['columnWidth'] = $this->conversionService->convertSpeakingWidthToTcpdfWidth($this->arguments['width'], $multiColumnContext['pageWidthWithoutMargins']);
            }
        } else {
            $multiColumnContext['columnWidth'] = $multiColumnContext['defaultColumnWidth'];
        }

        $multiColumnContext['columnWidth'] = $multiColumnContext['columnWidth'] - $this->arguments['padding']['left'] - $this->arguments['padding']['right'];
        $multiColumnContext['currentPosX'] = $multiColumnContext['currentPosX'] + $this->arguments['padding']['left'];
        $multiColumnContext['columnPadding'] = $this->arguments['padding'];

        $this->setCurrentMultiColumnContext($multiColumnContext);
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    public function render()
    {
        $this->getPDF()->SetY($this->getPDF()->GetY() + $this->arguments['padding']['top']);
        $this->renderChildren();
        $this->getPDF()->SetY($this->getPDF()->GetY() + $this->arguments['padding']['bottom']);
    }
}
