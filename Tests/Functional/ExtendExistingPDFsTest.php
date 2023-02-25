<?php

declare(strict_types=1);

namespace Bithost\Pdfviewhelpers\Tests\Functional;

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

use Bithost\Pdfviewhelpers\Exception\Exception;

/**
 * ExtendExistingPDFsTest
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
 */
class ExtendExistingPDFsTest extends AbstractFunctionalTest
{
    /**
     * @test
     */
    public function testDoImportOnAutomaticPageBreak(): void
    {
        $output = $this->renderFluidTemplate(
            $this->getFixtureExtPath('ExtendExistingPDFs/Template.html'),
            [
                'importPage' => 1,
                'importPageOnAutomaticPageBreak' => true,
                'text' => $this->getLongLoremIpsumText(5)
            ]
        );
        $pdf = $this->parseContent($output);
        $pages = $pdf->getPages();

        $this->assertCount(2, $pages);

        $this->assertStringContainsStringIgnoringCase('Mega GmbH', $pages[0]->getText());
        $this->assertStringContainsStringIgnoringCase('Ansprechpartner', $pages[0]->getText());
        $this->assertStringContainsStringIgnoringCase('www.bithost.ch', $pages[0]->getText());
        $this->assertStringContainsStringIgnoringCase('Lorem ipsum dolor sit amet', $pages[0]->getText());

        $this->assertStringContainsStringIgnoringCase('Mega GmbH', $pages[1]->getText());
        $this->assertStringContainsStringIgnoringCase('Ansprechpartner', $pages[1]->getText());
        $this->assertStringContainsStringIgnoringCase('www.bithost.ch', $pages[1]->getText());
        $this->assertStringContainsStringIgnoringCase('Lorem ipsum dolor sit amet', $pages[1]->getText());
    }

    /**
     * @test
     */
    public function testDoNotImportOnAutomaticPageBreak(): void
    {
        $output = $this->renderFluidTemplate(
            $this->getFixtureExtPath('ExtendExistingPDFs/Template.html'),
            [
                'importPage' => 1,
                'importPageOnAutomaticPageBreak' => false,
                'text' => $this->getLongLoremIpsumText(5)
            ]
        );
        $pdf = $this->parseContent($output);
        $pages = $pdf->getPages();

        $this->assertCount(2, $pages);

        $this->assertStringContainsStringIgnoringCase('Mega GmbH', $pages[0]->getText());
        $this->assertStringContainsStringIgnoringCase('Ansprechpartner', $pages[0]->getText());
        $this->assertStringContainsStringIgnoringCase('www.bithost.ch', $pages[0]->getText());
        $this->assertStringContainsStringIgnoringCase('Lorem ipsum dolor sit amet', $pages[0]->getText());

        $this->assertStringNotContainsStringIgnoringCase('Mega GmbH', $pages[1]->getText());
        $this->assertStringNotContainsStringIgnoringCase('Ansprechpartner', $pages[1]->getText());
        $this->assertStringNotContainsStringIgnoringCase('www.bithost.ch', $pages[1]->getText());
        $this->assertStringContainsStringIgnoringCase('Lorem ipsum dolor sit amet', $pages[1]->getText());
    }

    /**
     * @test
     */
    public function testImportWrongPage(): void
    {
        $this->expectException(Exception::class);

        $this->renderFluidTemplate(
            $this->getFixtureExtPath('ExtendExistingPDFs/Template.html'),
            [
                'importPage' => 2,
                'importPageOnAutomaticPageBreak' => true,
                'text' => $this->getLongLoremIpsumText(1)
            ]
        );
    }
}
