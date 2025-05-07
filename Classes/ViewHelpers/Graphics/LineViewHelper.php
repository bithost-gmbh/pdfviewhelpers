<?php

declare(strict_types=1);

namespace Bithost\Pdfviewhelpers\ViewHelpers\Graphics;

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
use Bithost\Pdfviewhelpers\Exception\ValidationException;
use Bithost\Pdfviewhelpers\ViewHelpers\AbstractContentElementViewHelper;

/**
 * LineViewHelper
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
 */
class LineViewHelper extends AbstractContentElementViewHelper
{
    /**
     * @inheritDoc
     */
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('style', 'array', '', false, []);
        $this->registerArgument('fromX', 'integer', '', false, null);
        $this->registerArgument('fromY', 'integer', '', false, null);
        $this->registerArgument('toX', 'integer', '', false, null);
        $this->registerArgument('toY', 'integer', '', false, null);
        $this->registerArgument('padding', 'array', '', false, []);
    }

    /**
     * @inheritDoc
     *
     * @throws Exception
     */
    public function initialize()
    {
        parent::initialize();

        $pageMargins = $this->getPDF()->getMargins();
        $this->arguments['style'] = array_merge($this->settings['graphics']['line']['style'] ?? [], $this->arguments['style']);
        $this->arguments['padding'] = array_merge($this->settings['graphics']['line']['padding'] ?? [], $this->arguments['padding']);

        $this->validationService->validatePadding($this->arguments['padding']);

        if ($this->validationService->validateColor($this->arguments['style']['color'])) {
            $this->arguments['style']['color'] = $this->conversionService->convertHexToRGB($this->arguments['style']['color']);
        }

        if (is_numeric($this->arguments['style']['width'])) {
            $this->arguments['style']['width'] = (float)$this->arguments['style']['width'];
        } else {
            throw new ValidationException('Invalid Line width "' . $this->arguments['style']['width'] . '" provided, must be numeric. ERROR: 1536157900', 1536157900);
        }

        // render horizontal line by default
        if ($this->arguments['fromX'] === null) {
            $this->arguments['fromX'] = $pageMargins['left'];
        }
        if ($this->arguments['fromY'] === null) {
            $this->arguments['fromY'] = $this->getPDF()->GetY();
        }
        if ($this->arguments['toX'] === null) {
            $this->arguments['toX'] =  $this->getPDF()->getPageWidth() - $pageMargins['right'];
        }
        if ($this->arguments['toY'] === null) {
            $this->arguments['toY'] = $this->getPDF()->GetY();
        }
    }

    /**
     * @throws Exception
     */
    public function render(): void
    {
        $this->getPDF()->Line(
            $this->arguments['fromX'] + $this->arguments['padding']['left'],
            $this->arguments['fromY'] + $this->arguments['padding']['top'],
            $this->arguments['toX'] - $this->arguments['padding']['right'],
            $this->arguments['toY'] + $this->arguments['padding']['top'],
            $this->arguments['style']
        );

        if ($this->arguments['padding']['bottom'] > 0) {
            $this->getPDF()->setAbsY($this->getPDF()->GetY() + $this->arguments['padding']['top'] + $this->arguments['padding']['bottom']);
        }
    }
}
