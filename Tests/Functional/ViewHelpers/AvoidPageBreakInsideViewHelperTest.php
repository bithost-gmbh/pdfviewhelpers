<?php

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

use Bithost\Pdfviewhelpers\Tests\Functional\AbstractFunctionalTest;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * AvoidPageBreakInsideViewHelperTest
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
 */
class AvoidPageBreakInsideViewHelperTest extends AbstractFunctionalTest
{
    /**
     * @test
     */
    public function testPageBreakPossible1()
    {
        $output = $this->renderFluidTemplate(
            $this->getFixturePath('AvoidPageBreakInsideViewHelper/Template.html'),
            [
                'text1' => 'text1' . $this->getLongLoremIpsumText(5), //6 duplicates fit on one page
                'text2' => 'text2' . $this->getLongLoremIpsumText(5),
                'breakIfImpossibleToAvoid' => false
            ]
        );

        $pdf = $this->parseContent($output);
        $pages = $pdf->getPages();

        $this->assertCount(2, $pages);
        $this->assertContains('text1', $pages[0]->getText());
        $this->assertContains('text2', $pages[1]->getText());
    }

    /**
     * @test
     */
    public function testPageBreakPossible2()
    {
        $output = $this->renderFluidTemplate(
            $this->getFixturePath('AvoidPageBreakInsideViewHelper/Template.html'),
            [
                'text1' => 'text1' . $this->getLongLoremIpsumText(5), //6 duplicates fit on one page
                'text2' => 'text2' . $this->getLongLoremIpsumText(5),
                'breakIfImpossibleToAvoid' => true
            ]
        );

        $pdf = $this->parseContent($output);
        $pages = $pdf->getPages();

        $this->assertCount(2, $pages);
        $this->assertContains('text1', $pages[0]->getText());
        $this->assertContains('text2', $pages[1]->getText());
    }

    /**
     * @test
     */
    public function testImpossibleToAvoid1()
    {
        $output = $this->renderFluidTemplate(
            $this->getFixturePath('AvoidPageBreakInsideViewHelper/Template.html'),
            [
                'text1' => 'text1' . $this->getLongLoremIpsumText(5), //6 duplicates fit on one page
                'text2' => 'text2' . $this->getLongLoremIpsumText(7),
                'breakIfImpossibleToAvoid' => false
            ]
        );

        $pdf = $this->parseContent($output);
        $pages = $pdf->getPages();

        $this->assertCount(2, $pages);
        $this->assertContains('text1', $pages[0]->getText());
        $this->assertContains('text2', $pages[0]->getText());
    }

    /**
     * @test
     */
    public function testImpossibleToAvoid2()
    {
        $output = $this->renderFluidTemplate(
            $this->getFixturePath('AvoidPageBreakInsideViewHelper/Template.html'),
            [
                'text1' => 'text1' . $this->getLongLoremIpsumText(5), //6 duplicates fit on one page
                'text2' => 'text2' . $this->getLongLoremIpsumText(7),
                'breakIfImpossibleToAvoid' => true
            ]
        );

        $pdf = $this->parseContent($output);
        $pages = $pdf->getPages();

        $this->assertCount(3, $pages);
        $this->assertContains('text1', $pages[0]->getText());
        $this->assertContains('text2', $pages[1]->getText());
    }

    /**
     * @test
     */
    public function testCustomFont()
    {
        $this->setUpPage([$this->getFixturePath('AvoidPageBreakInsideViewHelper/CustomFont.txt')]);

        $output = $this->renderFluidTemplate(
            $this->getFixturePath('AvoidPageBreakInsideViewHelper/CustomFont.html'),
            [
                'text1' => 'text1',
                'text2' => 'text2',
            ]
        );
    }
}
