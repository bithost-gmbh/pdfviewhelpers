<?php

declare(strict_types=1);

namespace Bithost\Pdfviewhelpers\Tests\Functional\Examples;

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

use Bithost\Pdfviewhelpers\Tests\Functional\AbstractFunctionalTestCase;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * ExtendExistingPDFsTest
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
 */
class ExtendExistingPDFsTest extends AbstractFunctionalTestCase
{
    protected array $typoScriptFiles = [
        'EXT:pdfviewhelpers/Tests/Functional/Fixtures/Examples/ExtendExistingPDFs.typoscript',
    ];

    #[Test]
    public function testExtendExistingPDFs(): void
    {
        $outputPath = GeneralUtility::getFileAbsFileName('EXT:pdfviewhelpers/Tests/Output/ExtendExistingPDFs.pdf');
        $output = $this->renderFluidTemplate($this->getFixtureExtPath('Examples/ExtendExistingPDFs.html'));
        $pdf = $this->parseContent($output);
        $text = $pdf->getText();

        file_put_contents($outputPath, $output);

        self::assertEquals(1, count($pdf->getPages()));
        self::assertStringContainsStringIgnoringCase('Mega GmbH', $text);
        self::assertStringContainsStringIgnoringCase('Ansprechpartner', $text);
        self::assertStringContainsStringIgnoringCase('www.bithost.ch', $text);
        self::assertStringContainsStringIgnoringCase('Here is your header', $text);
        self::assertStringContainsStringIgnoringCase('Here is the HTML header', $text);
        self::assertStringContainsStringIgnoringCase('Lorem ipsum dolor sit amet', $text);

        $this->validatePDF($output);

        $expectedHash = sha1(file_get_contents($this->getFixtureAbsolutePath('Examples/ExtendExistingPDFs.pdf')));
        $actualHash = sha1($output);
        self::assertEquals($expectedHash, $actualHash);
    }
}
