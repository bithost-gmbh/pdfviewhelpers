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
use Bithost\Pdfviewhelpers\Model\BasePDF;

/**
 * AbstractContentElementViewHelper
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Marin <esteban.marin@bithost.ch>
 */
abstract class AbstractContentElementViewHelper extends AbstractPDFViewHelper
{
    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('posX', 'integer', '', false, null);
        $this->registerArgument('posY', 'integer', '', false, null);
        $this->registerArgument('width', 'integer', '', false, null);
        $this->registerArgument('height', 'integer', '', false, null);
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    public function initialize()
    {
        if (!is_null($this->arguments['width'])) {
            $this->isValidWidth($this->arguments['width']);
        }

        if (!is_null($this->arguments['height'])) {
            $this->isValidHeight($this->arguments['height']);
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
     * @return void
     *
     * @throws Exception
     */
    public function initializeHeaderAndFooter()
    {
        if (!($this->getPDF() instanceof BasePDF)) {
            return;
        }

        if ($this->viewHelperVariableContainer->get('DocumentViewHelper', 'pageNeedsHeader')) {
            $this->viewHelperVariableContainer->addOrUpdate('DocumentViewHelper', 'pageNeedsHeader', false);

            $this->getPDF()->renderHeader();
            $this->getPDF()->renderFooter();
        }
    }

    /**
     * @param string $colorHex
     *
     * @return array
     */
    protected function convertHexToRGB($colorHex)
    {
        $colorHex = str_replace("#", "", $colorHex);

        if (strlen($colorHex) == 3) {
            $r = hexdec(substr($colorHex, 0, 1) . substr($colorHex, 0, 1));
            $g = hexdec(substr($colorHex, 1, 1) . substr($colorHex, 1, 1));
            $b = hexdec(substr($colorHex, 2, 1) . substr($colorHex, 2, 1));
        } else {
            $r = hexdec(substr($colorHex, 0, 2));
            $g = hexdec(substr($colorHex, 2, 2));
            $b = hexdec(substr($colorHex, 4, 2));
        }

        return ['R' => $r, 'G' => $g, 'B' => $b];
    }

    /**
     * @param string $imageTypes
     *
     * @return array
     */
    protected function convertImageTypeStringToImageTypeArray($imageTypes)
    {
        return explode(',', str_replace(' ', '', strtolower($imageTypes)));
    }

    /**
     * @return void
     */
    protected function initializeMultiColumnSupport()
    {
        $multiColumnContext = $this->getMultiColumnContext();

        if ($multiColumnContext['isInAColumn']) {
            $this->arguments['width'] = $multiColumnContext['columnWidth'];
            $this->arguments['posX'] = $multiColumnContext['currentPosX'];
        }
    }

    /**
     * @param string $extension
     *
     * @return string
     *
     * @throws ValidationException
     */
    protected function getImageRenderMode($extension)
    {
        $extension = strtolower($extension);

        if (in_array($extension, $this->convertImageTypeStringToImageTypeArray($this->settings['config']['allowedImageTypes']['image']))) {
            return 'image';
        } elseif (in_array($extension, $this->convertImageTypeStringToImageTypeArray($this->settings['config']['allowedImageTypes']['imageEPS']))) {
            return 'imageEPS';
        } elseif (in_array($extension, $this->convertImageTypeStringToImageTypeArray($this->settings['config']['allowedImageTypes']['imageSVG']))) {
            return 'imageSVG';
        } else {
            throw new ValidationException('Imagetype is not supported. ERROR: 1363778014', 1363778014);
        }
    }

    /**
     * @return array|bool $multiColumnContext
     */
    protected function getMultiColumnContext()
    {
        if ($this->viewHelperVariableContainer->exists('MultiColumnViewHelper', 'multiColumnContext')) {
            return $this->viewHelperVariableContainer->get('MultiColumnViewHelper', 'multiColumnContext');
        } else {
            return false;
        }
    }

    /**
     * @param string $colorHex
     *
     * @return boolean
     *
     * @throws ValidationException
     */
    protected function isValidColor($colorHex)
    {
        if (preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $colorHex)) {
            return true;
        } else {
            throw new ValidationException('Your Color is invalid. Use #000 or #000000.', 1363765272);
        }
    }

    /**
     * @param string $width
     *
     * @return boolean
     *
     * @throws ValidationException
     */
    protected function isValidWidth($width)
    {
        if (is_numeric($width)) {
            return true;
        } else {
            throw new ValidationException('Width must be an integer. ERROR: 1363765672', 1363765372);
        }
    }

    /**
     * @param string $height
     *
     * @return boolean
     *
     * @throws ValidationException
     */
    protected function isValidHeight($height)
    {
        if (is_numeric($height)) {
            return true;
        } else {
            throw new ValidationException('Height must be an integer. ERROR: 1363766372', 1363765372);
        }
    }

    /**
     * @param array $padding
     *
     * @return boolean
     *
     * @throws ValidationException
     */
    protected function isValidPadding($padding)
    {
        if (count($padding) === 4
            && isset($padding['top'], $padding['right'], $padding['bottom'], $padding['left'])
            && is_numeric($padding['top']) && is_numeric($padding['right'])
            && is_numeric($padding['bottom']) && is_numeric($padding['left'])
        ) {
            return true;
        } else {
            throw new ValidationException('Padding must be an Array with Elements: top:[int],right:[int],bottom:[int],left:[int] ERROR: 1363769351', 1363769351);
        }
    }

    /**
     * Converts pdfviewhelper fontStyle syntax to TCPDF syntax. This function is necessary because TCPDF uses an empty
     * string to represent "regular", but we can not do this because of the settings inheritance (empty means inherit).
     *
     * @param string $fontStyle
     *
     * @return string
     *
     * @throws Exception
     */
    public function convertToTcpdfFontStyle($fontStyle)
    {
        switch ($fontStyle) {
            case 'bold':
            case 'B':
                return 'B';
            case 'italic':
            case 'I':
                return 'I';
            case 'underline':
            case 'U':
                return 'U';
            case 'regular':
            case 'R':
                return '';
            default:
                throw new ValidationException('Invalid font style "' . $fontStyle . '" provided. ERROR: 1536238089', 1536238089);
        }
    }
}
