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
 * FullFeatureShowCaseTest
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
 */
class FullFeatureShowCaseTest extends AbstractFunctionalTest
{
    protected array $typoScriptFiles = [
        'EXT:pdfviewhelpers/Tests/Functional/Fixtures/Examples/FullFeatureShowCase.txt',
    ];

    /**
     * @test
     */
    public function testFullFeatureShowCase(): void
    {
        $output = $this->renderFluidTemplate($this->getFixtureExtPath('Examples/FullFeatureShowCase.html'));
        $pdf = $this->parseContent($output);
        $pages = $pdf->getPages();

        $this->assertEquals(5, count($pages));

        $this->assertStringContainsStringIgnoringCase('hallo@bithost.ch - www.bithost.ch', $pages[0]->getText());
        $this->assertStringContainsStringIgnoringCase('Full Feature Show Case', $pages[0]->getText());

        $this->assertStringContainsStringIgnoringCase('hallo@bithost.ch - www.bithost.ch', $pages[1]->getText());
        $this->assertStringContainsStringIgnoringCase('Application Development', $pages[1]->getText());
        $this->assertStringContainsStringIgnoringCase('This is a h1 headline', $pages[1]->getText());

        $this->assertStringContainsStringIgnoringCase('Only this page will have a different header', $pages[2]->getText());

        $this->assertStringContainsStringIgnoringCase('hallo@bithost.ch - www.bithost.ch', $pages[3]->getText());
        $this->assertStringContainsStringIgnoringCase('HTML content being styled externally', $pages[3]->getText());
        $this->assertStringContainsStringIgnoringCase('A Link to click', $pages[3]->getText());
        $this->assertStringContainsStringIgnoringCase('We are on page 4 of 5 pages.', $pages[3]->getText());

        $this->assertStringContainsStringIgnoringCase('Avoid page break inside', $pages[4]->getText());

        $this->validatePDF($output);

        //do not assert file equality, because the files generated on the server are not equal to the local files
        //comparing hashes of two locally generated files works however
        //$expectedHash = sha1(file_get_contents($this->getFixturePath('Examples/FullFeatureShowCase.pdf')));
        //$actualHash = sha1($output);
        //$this->assertEquals($expectedHash, $actualHash);
    }
}
