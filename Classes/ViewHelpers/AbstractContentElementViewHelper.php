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

use Bithost\Pdfviewhelpers\Exception\Exception;

/**
 * AbstractContentElementViewHelper
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
 */
abstract class AbstractContentElementViewHelper extends AbstractPDFViewHelper
{
    /**
     * @inheritDoc
     */
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('posX', 'float', 'Absolute posX of the element on the current page.', false, null);
        $this->registerArgument('posY', 'float', 'Absolute posY of the element on the current page.', false, null);
        $this->registerArgument('width', 'string', 'Width of the current element in the set unit.', false, null);
        $this->registerArgument('height', 'float', 'Height of the current element in the set unit.', false, null);
    }

    /**
     * @inheritDoc
     *
     * @throws Exception
     */
    public function initialize()
    {
        parent::initialize();

        if (!is_null($this->arguments['width'])) {
            $this->validationService->validateWidth($this->arguments['width']);
        }

        if (!is_null($this->arguments['height'])) {
            $this->validationService->validateHeight($this->arguments['height']);
        }

        if (is_null($this->arguments['posX'])) {
            $this->arguments['posX'] = $this->getPDF()->GetX();
        }

        if (is_null($this->arguments['posY'])) {
            $this->arguments['posY'] = $this->getPDF()->GetY();
        }

        $this->initializeHeaderAndFooter();
    }

    /**
     * @throws Exception
     */
    protected function initializeHeaderAndFooter(): void
    {
        if ($this->viewHelperVariableContainer->get('DocumentViewHelper', 'pageNeedsHeader')) {
            $this->viewHelperVariableContainer->addOrUpdate('DocumentViewHelper', 'pageNeedsHeader', false);

            $this->getPDF()->renderHeader();
        }
    }

    /**
     * @throws Exception
     */
    protected function initializeMultiColumnSupport(): void
    {
        $multiColumnContext = $this->getCurrentMultiColumnContext();

        if (is_array($multiColumnContext) && ($multiColumnContext['isInAColumn'] ?? false)) {
            $columnWidth = $multiColumnContext['columnWidth'] ?? 0;

            if ($this->arguments['width'] === null) {
                $this->arguments['width'] = $columnWidth;
            } else {
                $convertedWidth = $this->conversionService->convertSpeakingWidthToTcpdfWidth($this->arguments['width'], $columnWidth);
                $this->arguments['width'] = min($convertedWidth, $columnWidth);
            }

            $this->arguments['posX'] = $multiColumnContext['currentPosX'];
        } elseif ($this->arguments['width'] !== null) {
            $this->arguments['width'] = $this->conversionService->convertSpeakingWidthToTcpdfWidth($this->arguments['width'], $this->getPDF()->getInnerPageWidth());
        }
    }
}
