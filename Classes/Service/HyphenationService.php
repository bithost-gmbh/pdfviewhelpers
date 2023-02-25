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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TCPDF;
use TCPDF_STATIC;

/**
 * HyphenationService
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
 */
class HyphenationService implements SingletonInterface
{
    protected TCPDF $pdf;

    /**
     * Used to cache loading of hyphen patterns
     */
    protected array $hyphenPatterns = [];

    /**
     * @throws ValidationException
     */
    public function hyphenateText(string $text, string $hyphenFilePath): string
    {
        if (!file_exists($hyphenFilePath) || !is_readable($hyphenFilePath)) {
            throw new ValidationException('Path to hyphen file "' . $hyphenFilePath . '" does not exist or file is not readable. ERROR: 1525410458', 1525410458);
        }

        if (!isset($this->hyphenPatterns[$hyphenFilePath])) {
            $this->hyphenPatterns[$hyphenFilePath] = TCPDF_STATIC::getHyphenPatternsFromTEX($hyphenFilePath);
        }

        return $this->pdf->hyphenateText($text, $this->hyphenPatterns[$hyphenFilePath]);
    }

    public function getHyphenFilePath(string $hyphenFile): string
    {
        return GeneralUtility::getFileAbsFileName('EXT:pdfviewhelpers/Resources/Private/Hyphenation/' . $hyphenFile);
    }

    public function setPDF(TCPDF $pdf): void
    {
        $this->pdf = $pdf;
    }
}
