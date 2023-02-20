<?php

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
 * TableOfContentTest
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
 */
class TableOfContentTest extends AbstractFunctionalTest
{
    protected $typoScriptFiles = [
        'EXT:pdfviewhelpers/Tests/Functional/Fixtures/Examples/TableOfContent.txt',
    ];

    /**
     * @test
     */
    public function testTableOfContent()
    {
        $output = $this->renderFluidTemplate($this->getFixtureExtPath('Examples/TableOfContent.html'));
        $pdf = $this->parseContent($output);
        $pages = $pdf->getPages();

        $this->assertCount(5, $pages);

        $this->assertStringContainsStringIgnoringCase('HTML Table of content', $pages[0]->getText());
        $this->assertStringContainsStringIgnoringCase('html-table-of-content-header', $pages[0]->getText());
        $this->assertStringContainsStringIgnoringCase('html-table-of-content-footer', $pages[0]->getText());
        $this->assertStringContainsStringIgnoringCase('LEVEL 0: Headline page 1', $pages[0]->getText());
        $this->assertStringContainsStringIgnoringCase('LEVEL 0: Headline page 2', $pages[0]->getText());
        $this->assertStringContainsStringIgnoringCase('LEVEL 1: Headline page 2, level 1', $pages[0]->getText());
        $this->assertStringContainsStringIgnoringCase('LEVEL 0: Adding custom styled bookmark for a text', $pages[0]->getText());
        $this->assertStringContainsStringIgnoringCase('LEVEL 0: Adding custom styled ADVANCED bookmark for a text', $pages[0]->getText());
        $this->assertStringNotContainsStringIgnoringCase('Headline not in table of content', $pages[0]->getText());

        $this->assertStringContainsStringIgnoringCase('Regular table of content', $pages[1]->getText());
        $this->assertStringContainsStringIgnoringCase('regular-table-of-content-header', $pages[1]->getText());
        $this->assertStringContainsStringIgnoringCase('regular-table-of-content-footer', $pages[1]->getText());
        $this->assertStringContainsStringIgnoringCase('Headline page 1', $pages[1]->getText());
        $this->assertStringContainsStringIgnoringCase('Headline page 2', $pages[1]->getText());
        $this->assertStringContainsStringIgnoringCase('Headline page 2, level 1', $pages[1]->getText());
        $this->assertStringContainsStringIgnoringCase('Adding custom styled bookmark for a text', $pages[1]->getText());
        $this->assertStringContainsStringIgnoringCase('Adding custom styled ADVANCED bookmark for a text', $pages[1]->getText());
        $this->assertStringNotContainsStringIgnoringCase('LEVEL 0', $pages[1]->getText());
        $this->assertStringNotContainsStringIgnoringCase('Headline not in table of content', $pages[1]->getText());

        $this->assertStringContainsStringIgnoringCase('Headline page 1', $pages[2]->getText());
        $this->assertStringContainsStringIgnoringCase('General Header', $pages[2]->getText());
        $this->assertStringContainsStringIgnoringCase('General Footer', $pages[2]->getText());

        $this->assertStringContainsStringIgnoringCase('Headline page 2', $pages[3]->getText());
        $this->assertStringContainsStringIgnoringCase('Headline page 2, level 1', $pages[3]->getText());
        $this->assertStringContainsStringIgnoringCase('Headline page 2, level 2', $pages[3]->getText());

        $this->assertStringContainsStringIgnoringCase('Headline not in table of content', $pages[4]->getText());
        $this->assertStringContainsStringIgnoringCase('Here is some text', $pages[4]->getText());
        $this->assertStringContainsStringIgnoringCase('Headline not in table of content', $pages[4]->getText());

        //Do not validate as TCPDF does not produce valid documents having bookmarks
        //$this->validatePDF($output);
    }
}
