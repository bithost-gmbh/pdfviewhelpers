<?php

declare(strict_types=1);

namespace Bithost\Pdfviewhelpers\Model;

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

use Closure;
use Com\Tecnick\Pdf\Font\Stack;

/**
 * FontStack, resolves the font definition files of fonts requested by name, e.g. by HTML using bold text
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
 */
class FontStack extends Stack
{
    /**
     * Function(string $family, string $style): string returning the font definition file or an empty string if unknown
     */
    protected ?Closure $fontFileResolver = null;

    public function setFontFileResolver(Closure $fontFileResolver): void
    {
        $this->fontFileResolver = $fontFileResolver;
    }

    /**
     * @inheritDoc
     */
    public function addFont(int &$objnum, string $font, string $style = '', string $ifile = '', ?bool $subset = null): array
    {
        if ($ifile === '' && $this->fontFileResolver !== null) {
            // tc-lib-pdf normalizes e.g. "helvetica-bold" to "helveticaB"
            preg_match('/^(.*?)(B?I?)$/', $font, $matches);
            $ifile = ($this->fontFileResolver)($matches[1], $style . $matches[2]);
        }

        return parent::addFont($objnum, $font, $style, $ifile, $subset);
    }
}
