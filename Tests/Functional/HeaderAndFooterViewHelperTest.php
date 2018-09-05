<?php

namespace Bithost\Pdfviewhelpers\Tests\Functional;

/* * *
 *
 * This file is part of the "PDF ViewHelpers" Extension for TYPO3 CMS.
 *
 *  (c) 2016 Markus Mächler <markus.maechler@bithost.ch>, Bithost GmbH
 *           Esteban Marin <esteban.marin@bithost.ch>, Bithost GmbH
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

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * PageViewHelperTest
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Marin <esteban.marin@bithost.ch>
 */
class HeaderAndFooterViewHelperTest extends AbstractFunctionalTest
{
    /**
     * @test
     */
    public function testDocumentScope()
    {
        $output = $this->renderFluidTemplate(
            $this->getFixturePath('HeaderAndFooterViewHelper/DocumentScope.html'),
            ['autoPageBreak' => false, 'text' => $this->getLongLoremIpsumText(10)]
        );

        $pdf = $this->parseContent($output);
        $text = $pdf->getText();

        $this->assertEquals(1, mb_substr_count($text, 'Header'));
        $this->assertEquals(1, mb_substr_count($text, 'Footer'));
    }

    /**
     * @test
     */
    public function testDocumentScopeWithAutoPageBreak()
    {
        $output = $this->renderFluidTemplate(
            $this->getFixturePath('HeaderAndFooterViewHelper/DocumentScope.html'),
            ['autoPageBreak' => true, 'text' => $this->getLongLoremIpsumText(10)]
        );

        $pdf = $this->parseContent($output);
        $text = $pdf->getText();

        $this->assertEquals(2, mb_substr_count($text, 'Header'));
        $this->assertEquals(2, mb_substr_count($text, 'Footer'));
    }

    /**
     * @test
     */
    public function testDocumentScopeEmpty()
    {
        $output = $this->renderFluidTemplate($this->getFixturePath('HeaderAndFooterViewHelper/DocumentScopeEmpty.html'));

        $pdf = $this->parseContent($output);
        $text = $pdf->getText();

        $this->assertEquals(1, mb_substr_count($text, 'Header'));
        $this->assertEquals(1, mb_substr_count($text, 'Footer'));
    }

    /**
     * @test
     */
    public function testPageScope()
    {
        $output = $this->renderFluidTemplate(
            $this->getFixturePath('HeaderAndFooterViewHelper/PageScope.html'),
            ['autoPageBreak' => false, 'text' => $this->getLongLoremIpsumText(10)]
        );

        $pdf = $this->parseContent($output);
        $text = $pdf->getText();

        $this->assertEquals(1, mb_substr_count($text, 'Header'));
        $this->assertEquals(1, mb_substr_count($text, 'Footer'));
    }

    /**
     * @test
     */
    public function testPageScopeEmpty()
    {
        $output = $this->renderFluidTemplate($this->getFixturePath('HeaderAndFooterViewHelper/PageScopeEmpty.html'));

        $pdf = $this->parseContent($output);
        $text = $pdf->getText();

        $this->assertEquals(1, mb_substr_count($text, 'Header'));
        $this->assertEquals(1, mb_substr_count($text, 'Footer'));
    }

    /**
     * @test
     */
    public function testPageScopeWithAutoPageBreak()
    {
        $output = $this->renderFluidTemplate(
            $this->getFixturePath('HeaderAndFooterViewHelper/PageScope.html'),
            ['autoPageBreak' => true, 'text' => $this->getLongLoremIpsumText(10)]
        );

        $pdf = $this->parseContent($output);
        $text = $pdf->getText();

        $this->assertEquals(2, mb_substr_count($text, 'Header'));
        $this->assertEquals(2, mb_substr_count($text, 'Footer'));
    }

    /**
     * @test
     */
    public function testDocumentAndPageScope()
    {
        $output = $this->renderFluidTemplate(
            $this->getFixturePath('HeaderAndFooterViewHelper/DocumentAndPageScope.html'),
            ['autoPageBreak' => true, 'text' => $this->getLongLoremIpsumText(10)]
        );

        $pdf = $this->parseContent($output);
        $pages = $pdf->getPages();

        $this->assertEquals(1, mb_substr_count($pages[0]->getText(), 'FirstPageHeader'));
        $this->assertEquals(1, mb_substr_count($pages[0]->getText(), 'FirstPageFooter'));
        $this->assertEquals(1, mb_substr_count($pages[1]->getText(), 'DocumentHeader'));
        $this->assertEquals(1, mb_substr_count($pages[1]->getText(), 'FirstPageFooter'));
        $this->assertEquals(1, mb_substr_count($pages[2]->getText(), 'DocumentHeader'));
        $this->assertEquals(1, mb_substr_count($pages[2]->getText(), 'DocumentFooter'));
    }
}