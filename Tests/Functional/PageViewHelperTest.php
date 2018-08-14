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

/**
 * PageViewHelperTest
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Marin <esteban.marin@bithost.ch>
 */
class PageViewHelperTest extends AbstractFunctionalTest
{
    /**
     * 150 words 890 characters
     *
     * @var string
     */
    protected $loremIpsumText = 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.';

    /**
     * @test
     */
    public function testAddPages()
    {
        $output = $this->renderFluidTemplate($this->getFixturePath('PageViewHelper/AddPages.html'));
        $pdf = $this->parseContent($output);
        $pages = $pdf->getPages();

        $this->assertEquals(3, count($pages));
        $this->assertContains('Page 1', $pages[0]->getText());
        $this->assertNotContains('Page 2', $pages[0]->getText());
        $this->assertNotContains('Page 3', $pages[0]->getText());
        $this->assertContains('Page 2', $pages[1]->getText());
        $this->assertNotContains('Page 1', $pages[1]->getText());
        $this->assertNotContains('Page 3', $pages[1]->getText());
        $this->assertContains('Page 3', $pages[2]->getText());
        $this->assertNotContains('Page 1', $pages[2]->getText());
        $this->assertNotContains('Page 2', $pages[2]->getText());
    }

    /**
     * @test
     */
    public function testAutoPageBreakOn()
    {
        $longLoremIpsumText = $this->getLongLoremIpsumText(10);
        $output = $this->renderFluidTemplate(
            $this->getFixturePath('PageViewHelper/AutoPageBreak.html'),
            ['autoPageBreak' => 1, 'text' => $longLoremIpsumText]
        );
        $pdf = $this->parseContent($output);

        $this->assertEquals(2, count($pdf->getPages()));
    }

    /**
     * @test
     */
    public function testAutoPageBreakOff()
    {
        $longLoremIpsumText = $this->getLongLoremIpsumText(10);
        $output = $this->renderFluidTemplate(
            $this->getFixturePath('PageViewHelper/AutoPageBreak.html'),
            ['autoPageBreak' => 0, 'text' => $longLoremIpsumText]
        );
        $pdf = $this->parseContent($output);

        $this->assertEquals(1, count($pdf->getPages()));
    }

    /**
     * @param integer $duplicates
     *
     * @return string
     */
    protected function getLongLoremIpsumText($duplicates)
    {
        $longLoremIpsumText = '';
        $duplicates = max($duplicates, 0);

        for ($i=0; $i < $duplicates; $i++) {
            $longLoremIpsumText .= $this->loremIpsumText;
        }

        return $longLoremIpsumText;
    }
}