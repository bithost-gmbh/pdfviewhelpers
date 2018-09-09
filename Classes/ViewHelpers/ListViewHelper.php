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
use Bithost\Pdfviewhelpers\Exception\ValidationException;

/**
 * ListViewHelper
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Marin <esteban.marin@bithost.ch>
 */
class ListViewHelper extends AbstractTextViewHelper
{
    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();

        if (strlen($this->settings['list']['trim'])) {
            $this->overrideArgument('trim', 'boolean', '', false, (boolean)$this->settings['list']['trim']);
        }
        if (strlen($this->settings['list']['removeDoubleWhitespace'])) {
            $this->overrideArgument('removeDoubleWhitespace', 'boolean', '', false, (boolean)$this->settings['list']['removeDoubleWhitespace']);
        }
        if (!empty($this->settings['list']['color'])) {
            $this->overrideArgument('color', 'string', '', false, $this->settings['list']['color']);
        }
        if (!empty($this->settings['list']['fontFamily'])) {
            $this->overrideArgument('fontFamily', 'string', '', false, $this->settings['list']['fontFamily']);
        }
        if (!empty($this->settings['list']['fontSize'])) {
            $this->overrideArgument('fontSize', 'integer', '', false, $this->settings['list']['fontSize']);
        }
        if (!empty($this->settings['list']['fontStyle'])) {
            $this->overrideArgument('fontStyle', 'string', '', false, $this->settings['list']['fontStyle']);
        }
        if (!empty($this->settings['list']['alignment'])) {
            $this->overrideArgument('alignment', 'string', '', false, $this->settings['list']['alignment']);
        }
        if (!empty($this->settings['list']['autoHyphenation'])) {
            $this->overrideArgument('autoHyphenation', 'boolean', '', false, $this->settings['list']['autoHyphenation']);
        }

        $this->registerArgument('listElements', 'array', '', true, null);
        $this->registerArgument('bulletColor', 'string', '', false, $this->settings['list']['bulletColor']);
        $this->registerArgument('bulletImageSrc', 'string', '', false, $this->settings['list']['bulletImage']);
        $this->registerArgument('bulletSize', 'integer', '', false, $this->settings['list']['bulletSize']);
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    public function initialize()
    {
        parent::initialize();

        $this->arguments['padding'] = array_merge($this->settings['generalText']['padding'], $this->settings['list']['padding'], $this->arguments['padding']);

        if ($this->validationService->validatePadding($this->arguments['padding'])) {
            $this->getPDF()->setCellPaddings(0, 0, $this->arguments['padding']['right'], 0);
        }

        $this->validationService->validateListElements($this->arguments['listElements']);

        if (!empty($this->arguments['bulletImageSrc'])) {
            if (!($this->settingsConversionService->convertImageExtensionToRenderMode($this->arguments['bulletImageSrc']) === 'image')) {
                throw new ValidationException('Image type not supported for list. ERROR: 1363771014', 1363771014);
            }
        }

        if (empty($this->arguments['bulletColor'])) {
            $this->arguments['bulletColor'] = $this->settings['generalText']['color'];
        }

        if ($this->validationService->validateColor($this->arguments['bulletColor'])) {
            $this->arguments['bulletColor'] = $this->settingsConversionService->convertHexToRGB($this->arguments['bulletColor']);
        }
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    public function render()
    {
        $this->initializeMultiColumnSupport();

        //indent of the bullet from the left page border
        $bulletPosX = $this->arguments['posX'] + $this->arguments['padding']['left'];
        //helps to center the bullet vertically
        $relativBulletPosY = $this->getPDF()->getFontSize() / 1.6 - $this->arguments['bulletSize'] / 2;
        $pageMargins = $this->getPDF()->getMargins();
        //indent of the Text from the left page border
        $textPosX = $this->arguments['padding']['left'] + 2 * $this->arguments['bulletSize'] + $this->arguments['posX'];
        //width of the entire element minus the indent for the bullet
        $textWidth = $this->arguments['width'] - $this->arguments['padding']['left'] - 2 * $this->arguments['bulletSize'];
        //posY of the line that's being printed
        $currentPosY = $this->arguments['posY'] + $this->arguments['padding']['top'];

        foreach ($this->arguments['listElements'] as $listElement) {
            if (empty($this->arguments['bulletImageSrc'])) {
                $this->getPDF()->Rect($bulletPosX, $currentPosY + $relativBulletPosY, $this->arguments['bulletSize'], $this->arguments['bulletSize'], 'F', null, [$this->arguments['bulletColor']['R'], $this->arguments['bulletColor']['G'], $this->arguments['bulletColor']['B']]);
            } else {
                $this->getPDF()->Image($this->arguments['bulletImageSrc'], $bulletPosX, $currentPosY + $relativBulletPosY, $this->arguments['bulletSize'], null, '', '', '', false, 300, '', false, false, 0, false, false, true, false);
            }

            if ($this->arguments['autoHyphenation']) {
                $listElement = $this->hyphenationService->hyphenateText(
                    $listElement,
                    $this->hyphenationService->getHyphenFilePath($this->settings['config']['hyphenFile'])
                );
            }

            $this->getPDF()->MultiCell($textWidth, $this->arguments['height'], $listElement, 0, $this->settingsConversionService->convertAlignment($this->arguments['alignment']), false, 1, $textPosX, $currentPosY, true, 0, false, true, 0, 'T', false);

            $currentPosY += $this->getPDF()->getStringHeight($textWidth, $listElement);
        }

        $this->getPDF()->SetY($currentPosY + $this->arguments['padding']['bottom']);
    }
}
