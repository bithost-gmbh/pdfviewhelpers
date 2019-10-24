<?php

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
    public function testDoImportOnAutomaticPageBreak()
    {
        $output = $this->renderFluidTemplate(
            $this->getFixturePath('ExtendExistingPDFs/Template.html'),
            [
                'importPage' => 1,
                'importPageOnAutomaticPageBreak' => true,
                'text' => $this->getLongLoremIpsumText(5)
            ]
        );
        $pdf = $this->parseContent($output);
        $pages = $pdf->getPages();

        $this->assertCount(2, $pages);

        $this->assertContains('Mega GmbH', $pages[0]->getText());
        $this->assertContains('Ansprechpartner', $pages[0]->getText());
        $this->assertContains('www.bithost.ch', $pages[0]->getText());
        $this->assertContains('Lorem ipsum dolor sit amet', $pages[0]->getText());

        $this->assertContains('Mega GmbH', $pages[1]->getText());
        $this->assertContains('Ansprechpartner', $pages[1]->getText());
        $this->assertContains('www.bithost.ch', $pages[1]->getText());
        $this->assertContains('Lorem ipsum dolor sit amet', $pages[1]->getText());
    }

    /**
     * @test
     */
    public function testDoNotImportOnAutomaticPageBreak()
    {
        $output = $this->renderFluidTemplate(
            $this->getFixturePath('ExtendExistingPDFs/Template.html'),
            [
                'importPage' => 1,
                'importPageOnAutomaticPageBreak' => false,
                'text' => $this->getLongLoremIpsumText(5)
            ]
        );
        $pdf = $this->parseContent($output);
        $pages = $pdf->getPages();

        $this->assertCount(2, $pages);

        $this->assertContains('Mega GmbH', $pages[0]->getText());
        $this->assertContains('Ansprechpartner', $pages[0]->getText());
        $this->assertContains('www.bithost.ch', $pages[0]->getText());
        $this->assertContains('Lorem ipsum dolor sit amet', $pages[0]->getText());

        $this->assertNotContains('Mega GmbH', $pages[1]->getText());
        $this->assertNotContains('Ansprechpartner', $pages[1]->getText());
        $this->assertNotContains('www.bithost.ch', $pages[1]->getText());
        $this->assertContains('Lorem ipsum dolor sit amet', $pages[1]->getText());
    }

    /**
     * @test
     *
     * @expectedException \Bithost\Pdfviewhelpers\Exception\Exception
     */
    public function testImportWrongPage()
    {
        $this->renderFluidTemplate(
            $this->getFixturePath('ExtendExistingPDFs/Template.html'),
            [
                'importPage' => 2,
                'importPageOnAutomaticPageBreak' => true,
                'text' => $this->getLongLoremIpsumText(1)
            ]
        );
    }
}
