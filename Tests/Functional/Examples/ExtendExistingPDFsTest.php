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

use Bithost\Pdfviewhelpers\Tests\Functional\AbstractFunctionalTest;

/**
 * ExtendExistingPDFsTest
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
 */
class ExtendExistingPDFsTest extends AbstractFunctionalTest
{
    protected array $typoScriptFiles = [
        'EXT:pdfviewhelpers/Tests/Functional/Fixtures/Examples/ExtendExistingPDFs.txt',
    ];

    /**
     * @test
     */
    public function testExtendExistingPDFs(): void
    {
        $output = $this->renderFluidTemplate($this->getFixtureExtPath('Examples/ExtendExistingPDFs.html'));
        $pdf = $this->parseContent($output);
        $text = $pdf->getText();

        $this->assertEquals(1, count($pdf->getPages()));
        $this->assertStringContainsStringIgnoringCase('Mega GmbH', $text);
        $this->assertStringContainsStringIgnoringCase('Ansprechpartner', $text);
        $this->assertStringContainsStringIgnoringCase('www.bithost.ch', $text);
        $this->assertStringContainsStringIgnoringCase('Here is your header', $text);
        $this->assertStringContainsStringIgnoringCase('Here is the HTML header', $text);
        $this->assertStringContainsStringIgnoringCase('Lorem ipsum dolor sit amet', $text);

        $this->validatePDF($output);

        //$expectedHash = sha1(file_get_contents($this->getFixturePath('Examples/ExtendExistingPDFs.pdf')));
        //$actualHash = sha1($output);
        //$this->assertEquals($expectedHash, $actualHash);
    }
}
