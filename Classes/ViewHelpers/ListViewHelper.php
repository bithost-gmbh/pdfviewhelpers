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

        $this->registerArgument('listElements', 'array', '', true, null);
        $this->registerArgument('bulletColor', 'string', '', false, $this->settings['list']['bulletColor']);
        $this->registerArgument('bulletImageSrc', 'string', '', false, $this->settings['list']['bulletImageSrc']);
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

        $this->validationService->validateListElements($this->arguments['listElements']);

        if (!empty($this->arguments['bulletImageSrc'])) {
            $bulletImageFile = $this->conversionService->convertFileSrcToFileObject($this->arguments['bulletImageSrc']);

            if (!($this->conversionService->convertImageExtensionToRenderMode($bulletImageFile->getExtension()) === 'image')) {
                throw new ValidationException('Image type not supported for list. ERROR: 1363771014', 1363771014);
            }
        }

        if (empty($this->arguments['bulletColor'])) {
            $this->arguments['bulletColor'] = $this->settings['generalText']['color'];
        }

        if ($this->validationService->validateColor($this->arguments['bulletColor'])) {
            $this->arguments['bulletColor'] = $this->conversionService->convertHexToRGB($this->arguments['bulletColor']);
        }

        $this->getPDF()->setCellPaddings(0, 0, 0, 0);
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
        $relativBulletPosY = ($this->getPDF()->getCellHeight($this->arguments['fontSize']) / $this->getPDF()->getScaleFactor() - $this->arguments['bulletSize']) / 2;
        //indent of the Text from the left page border
        $textPosX = $this->arguments['padding']['left'] * 2 + $this->arguments['bulletSize'] + $this->arguments['posX'];
        //width of the entire element minus the indent for the bullet
        $textWidth = $this->arguments['width'] - $this->arguments['padding']['left'] - 2 * $this->arguments['bulletSize'];

        //Update y respecting padding
        $this->getPDF()->SetY($this->arguments['posY'] + $this->arguments['padding']['top']);

        if (!empty($this->arguments['bulletImageSrc'])) {
            $bulletImageFile = $this->conversionService->convertFileSrcToFileObject($this->arguments['bulletImageSrc']);
            $bulletImageFileContent = '@' . $bulletImageFile->getContents();
        }

        //The height of a single text line
        $oneLineTextHeight = $this->getPDF()->getStringHeight($textWidth, '.');

        foreach ($this->arguments['listElements'] as $listElement) {
            if ($this->arguments['autoHyphenation']) {
                $listElement = $this->hyphenationService->hyphenateText(
                    $listElement,
                    $this->hyphenationService->getHyphenFilePath($this->settings['config']['hyphenFile'])
                );
            }

            $elementStartPage = $this->getPDF()->getPage();
            $elementStartY = $this->getPDF()->getY();

            $this->getPDF()->MultiCell($textWidth, $this->arguments['height'], $listElement, 0, $this->conversionService->convertSpeakingAlignmentToTcpdfAlignment($this->arguments['alignment']), false, 1, $textPosX, null, true, 0, false, true, 0, 'T', false);

            $elementEndPage = $this->getPDF()->getPage();
            $elementEndY = $this->getPDF()->getY();

            $scaledPageHeight = $this->getPDF()->getScaledPageHeight();
            $breakMargin = $this->getPDF()->getBreakMargin($elementStartPage);

            if ($elementStartY + $oneLineTextHeight >= $scaledPageHeight  - $breakMargin) {
                //A page break occurred on the first line
                $elementStartY = $this->getPDF()->getMargins()['top'];
            } else {
                //No page on first line break, reset to start page
                $this->getPDF()->setPage($elementStartPage);
            }

            if (empty($this->arguments['bulletImageSrc'])) {
                $this->getPDF()->Rect($bulletPosX, $elementStartY + $relativBulletPosY, $this->arguments['bulletSize'], $this->arguments['bulletSize'], 'F', null, [$this->arguments['bulletColor']['R'], $this->arguments['bulletColor']['G'], $this->arguments['bulletColor']['B']]);
            } else {
                $this->getPDF()->Image($bulletImageFileContent, $bulletPosX, $elementStartY + $relativBulletPosY, $this->arguments['bulletSize'], null, '', '', '', false, 300, '', false, false, 0, false, false, true, false);
            }

            $this->getPDF()->setPage($elementEndPage);
            $this->getPDF()->setY($elementEndY);
        }

        $this->getPDF()->SetY($elementEndY + $this->arguments['padding']['bottom']);
    }

    /**
     * @return string
     */
    protected function getSettingsKey()
    {
        return 'list';
    }
}
