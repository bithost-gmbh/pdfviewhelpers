<?php

namespace Bithost\Pdfviewhelpers\Utility;

/***
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
 ***/

use Bithost\Pdfviewhelpers\Exception\ValidationException;

/**
 * ValidationUtility
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Marin <esteban.marin@bithost.ch>
 */
class ValidationUtility
{
    /**
     * @param array $padding
     *
     * @return boolean
     *
     * @throws ValidationException
     */
    public static function validatePadding($padding)
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
}
