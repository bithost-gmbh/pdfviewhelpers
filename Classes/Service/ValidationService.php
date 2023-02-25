<?php

declare(strict_types=1);

namespace Bithost\Pdfviewhelpers\Service;

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
use TYPO3\CMS\Core\SingletonInterface;

/**
 * ValidationService
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
 */
class ValidationService implements SingletonInterface
{
    /**
     * @throws ValidationException
     */
    public function validatePadding(array $padding): bool
    {
        if (
            count($padding) === 4
            && isset($padding['top'], $padding['right'], $padding['bottom'], $padding['left'])
            && is_numeric($padding['top']) && is_numeric($padding['right'])
            && is_numeric($padding['bottom']) && is_numeric($padding['left'])
        ) {
            return true;
        } else {
            throw new ValidationException('Invalid padding "' . var_export($padding, true) . '", must be an array with elements: top:[int],right:[int],bottom:[int],left:[int] ERROR: 1363769351', 1363769351);
        }
    }

    /**
     * @throws ValidationException
     */
    public function validateColor(string $colorHex): bool
    {
        if (preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $colorHex)) {
            return true;
        } else {
            throw new ValidationException('Invalid color syntax "' . $colorHex . '", must follow syntax #000 or #000000.', 1363765272);
        }
    }

    /**
     * @param string|int|float $width
     *
     * @throws ValidationException
     */
    public function validateWidth($width): bool
    {
        if (is_numeric($width) && $width >= 0 || mb_substr($width, -1) === '%') {
            return true;
        } else {
            throw new ValidationException('Invalid width "' . $width . '" provided, must be a floating point or percentage value. ERROR: 1363765672', 1363765372);
        }
    }

    /**
     * @param string|int|float $fontSize
     *
     * @throws ValidationException
     */
    public function validateFontSize($fontSize): bool
    {
        if (is_numeric($fontSize) && $fontSize >= 0) {
            return true;
        } else {
            throw new ValidationException('Invalid fontSize "' . $fontSize . '" provided, must be a floating point value. ERROR: 1363765372', 1363765372);
        }
    }

    /**
     * Check fontFamily for compatibility with TCPDF naming conventions
     *
     * @throws ValidationException
     */
    public function validateFontFamily(string $fontFamily): bool
    {
        //TCPDF transformation START
        $tcpdfFontFamilyName = strtolower($fontFamily);
        $tcpdfFontFamilyName = preg_replace('/[^a-z0-9_]/', '', $tcpdfFontFamilyName);
        $search = ['bold', 'oblique', 'italic', 'regular'];
        $replace = ['b', 'i', 'i', ''];
        $tcpdfFontFamilyName = str_replace($search, $replace, $tcpdfFontFamilyName);

        if (empty($tcpdfFontFamilyName)) {
            // set generic name
            $tcpdfFontFamilyName = 'tcpdffont';
        }
        //TCPDF transformation END

        if ($fontFamily === $tcpdfFontFamilyName) {
            return true;
        } else {
            throw new ValidationException('Invalid fontFamily name "' . $fontFamily . '". Name must only contain letters "a-z0-9_" and none of the words "bold", "oblique", "italic" and "regular". ERROR: 1492809393', 1492809393);
        }
    }

    /**
     * @param string|int|float $paragraphSpacing
     *
     * @throws ValidationException
     */
    public function validateParagraphSpacing($paragraphSpacing): bool
    {
        if (is_numeric($paragraphSpacing) && $paragraphSpacing >= 0) {
            return true;
        } else {
            throw new ValidationException('Invalid paragraphSpacing "' . $paragraphSpacing . '" provided, must be a float value. ERROR: 1363765379', 1363765379);
        }
    }

    /**
     * @param string|int|float $height
     *
     * @throws ValidationException
     */
    public function validateHeight($height): bool
    {
        if (is_numeric($height) && $height >= 0) {
            return true;
        } else {
            throw new ValidationException('Invalid height "' . $height . '" provided, must be a positive float value. ERROR: 1363766372', 1363765372);
        }
    }

    /**
     * @throws ValidationException
     */
    public function validateListElements(array $listElements): bool
    {
        if (count($listElements) == count($listElements, COUNT_RECURSIVE)) {
            return true;
        } else {
            throw new ValidationException('Only one dimensional arrays are allowed for the ListViewHelper. ERROR: 1363779014', 1363779014);
        }
    }

    /**
     * @param string|int|float $lineHeight
     *
     * @throws ValidationException
     */
    public function validateLineHeight($lineHeight): bool
    {
        if (is_numeric($lineHeight) && $lineHeight >= 0) {
            return true;
        } else {
            throw new ValidationException('Invalid lineHeight "' . $lineHeight . '" provided, must be a positive float value. ERROR: 1536704503', 1536704503);
        }
    }

    /**
     * @param string|int|float $characterSpacing
     *
     * @throws ValidationException
     */
    public function validateCharacterSpacing($characterSpacing): bool
    {
        if (is_numeric($characterSpacing) && $characterSpacing >= 0) {
            return true;
        } else {
            throw new ValidationException('Invalid characterSpacing "' . $characterSpacing . '" provided, must be a positive float value. ERROR: 1536704547', 1536704547);
        }
    }
}
