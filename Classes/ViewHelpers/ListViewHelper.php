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

use Bithost\Pdfviewhelpers\Exception\ValidationException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use UnexpectedValueException;

/**
 * ListViewHelper
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
 */
class ListViewHelper extends AbstractTextViewHelper
{
    /**
     * @inheritDoc
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();

        $this->registerArgument('listElements', 'array', 'A one dimensional array of list elements.', true, null);
        $this->registerArgument('bulletColor', 'string', 'The color of the bullet.', false, $this->settings['list']['bulletColor']);
        $this->registerArgument('bulletImageSrc', 'string', 'An image to be used alternatively to the square bullet.', false, $this->settings['list']['bulletImageSrc']);
        $this->registerArgument('bulletSize', 'float', 'The size of the bullet.', false, $this->settings['list']['bulletSize']);
    }

    /**
     * @inheritDoc
     *
     * @throws ValidationException
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->validationService->validateListElements($this->arguments['listElements']);

        if (!empty($this->arguments['bulletImageSrc'])) {
            $bulletImageFile = $this->conversionService->convertFileSrcToFileObject($this->arguments['bulletImageSrc']);
            $fileExtension = $bulletImageFile ? $bulletImageFile->getExtension() : pathinfo($this->arguments['bulletImageSrc'], PATHINFO_EXTENSION);

            if ($this->conversionService->convertImageExtensionToRenderMode($fileExtension) !== 'image') {
                throw new ValidationException('Image type not supported for list. ERROR: 1363771014', 1363771014);
            }
        }

        if (empty($this->arguments['bulletColor'])) {
            $this->arguments['bulletColor'] = $this->settings['generalText']['color'];
        }

        $this->validationService->validateColor($this->arguments['bulletColor']);

        $this->getPDF()->setCellPadding(0, 0);
    }

    /**
     * @inheritDoc
     *
     * @throws ValidationException
     */
    public function render(): void
    {
        $this->initializeMultiColumnSupport();

        $pdf = $this->getPDF();
        $bulletSize = (float) $this->arguments['bulletSize'];
        //indent of the bullet from the left page border
        $bulletPosX = $this->arguments['posX'] + $this->arguments['padding']['left'];
        //helps to center the bullet vertically
        $relativeBulletPosY = ($pdf->getLineHeight() - $bulletSize) / 2;
        //indent of the Text from the left page border
        $textPosX = $this->arguments['padding']['left'] * 2 + $bulletSize + $this->arguments['posX'];
        //width of the entire element minus the indent for the bullet, null extends it to the right margin
        $textWidth = $this->arguments['width'] === null ? null : $this->arguments['width'] - $this->arguments['padding']['left'] - 2 * $bulletSize;
        $bulletImage = $this->arguments['bulletImageSrc'] ? $this->getBulletImage() : '';

        $pdf->setY($this->arguments['posY'] + $this->arguments['padding']['top']);
        $pdf->setHyphenation($this->arguments['autoHyphenation'] ? $this->getHyphenFilePath() : '');

        foreach ($this->arguments['listElements'] as $listElement) {
            if ($this->arguments['paragraphLineFeed']) {
                $listElement .= "\n";
            }

            $firstLine = $pdf->writeText((string) $listElement, $textPosX, $pdf->getY(), $textWidth, $this->conversionService->convertSpeakingAlignmentToTcpdfAlignment($this->arguments['alignment']), (float) $this->arguments['height']);
            $elementEndPage = $pdf->getPageId();
            $elementEndY = $pdf->getY();

            // the bullet is drawn next to the first line, which might have been moved to the next page
            $pdf->setPageId($firstLine['page']);

            if ($bulletImage === '') {
                $pdf->drawRect($bulletPosX, $firstLine['y'] + $relativeBulletPosY, $bulletSize, $bulletSize, $this->arguments['bulletColor']);
            } else {
                $pdf->drawImage($bulletImage, 'image', $bulletPosX, $firstLine['y'] + $relativeBulletPosY, $bulletSize, null, '', 0, 0, false);
            }

            $pdf->setPageId($elementEndPage);
            $pdf->setY($elementEndY);
        }

        $pdf->setHyphenation('');
        $pdf->setY($pdf->getY() + $this->arguments['padding']['bottom']);
    }

    /**
     * @throws ValidationException
     */
    protected function getBulletImage(): string
    {
        $bulletImageFile = $this->conversionService->convertFileSrcToFileObject($this->arguments['bulletImageSrc']);

        if ($bulletImageFile) {
            return $bulletImageFile->getContents();
        }

        $bulletImageFileName = GeneralUtility::getFileAbsFileName($this->arguments['bulletImageSrc']);

        if ($bulletImageFileName && file_exists($bulletImageFileName)) {
            return file_get_contents($bulletImageFileName);
        }

        throw new UnexpectedValueException('Provided bullet image source can\'t be loaded: ' . $this->arguments['bulletImageSrc'], 1729853326);
    }

    protected function getSettingsKey(): string
    {
        return 'list';
    }
}
