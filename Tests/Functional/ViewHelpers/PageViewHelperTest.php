<?php

declare(strict_types=1);

namespace Bithost\Pdfviewhelpers\Tests\Functional\ViewHelpers;

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

/**
 * PageViewHelperTest
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
 */
class PageViewHelperTest extends AbstractFunctionalTestCase
{
    /**
     * @test
     */
    public function testAddPages(): void
    {
        $output = $this->renderFluidTemplate($this->getFixtureExtPath('PageViewHelper/AddPages.html'));
        $pdf = $this->parseContent($output);
        $pages = $pdf->getPages();

        $this->assertEquals(3, count($pages));
        $this->assertStringContainsStringIgnoringCase('Page 1', $pages[0]->getText());
        $this->assertStringNotContainsStringIgnoringCase('Page 2', $pages[0]->getText());
        $this->assertStringNotContainsStringIgnoringCase('Page 3', $pages[0]->getText());
        $this->assertStringContainsStringIgnoringCase('Page 2', $pages[1]->getText());
        $this->assertStringNotContainsStringIgnoringCase('Page 1', $pages[1]->getText());
        $this->assertStringNotContainsStringIgnoringCase('Page 3', $pages[1]->getText());
        $this->assertStringContainsStringIgnoringCase('Page 3', $pages[2]->getText());
        $this->assertStringNotContainsStringIgnoringCase('Page 1', $pages[2]->getText());
        $this->assertStringNotContainsStringIgnoringCase('Page 2', $pages[2]->getText());
    }

    /**
     * @test
     */
    public function testAutoPageBreakOn(): void
    {
        $longLoremIpsumText = $this->getLongLoremIpsumText(10);
        $output = $this->renderFluidTemplate(
            $this->getFixtureExtPath('PageViewHelper/AutoPageBreak.html'),
            ['autoPageBreak' => 1, 'text' => $longLoremIpsumText]
        );
        $pdf = $this->parseContent($output);

        $this->assertEquals(2, count($pdf->getPages()));
    }

    /**
     * @test
     */
    public function testAutoPageBreakOff(): void
    {
        $longLoremIpsumText = $this->getLongLoremIpsumText(10);
        $output = $this->renderFluidTemplate(
            $this->getFixtureExtPath('PageViewHelper/AutoPageBreak.html'),
            ['autoPageBreak' => 0, 'text' => $longLoremIpsumText]
        );
        $pdf = $this->parseContent($output);

        $this->assertEquals(1, count($pdf->getPages()));
    }
}
