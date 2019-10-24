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

/**
 * HtmlViewHelperTest
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
 */
class HtmlViewHelperTest extends AbstractFunctionalTest
{
    /**
     * @test
     */
    public function testRenderRichText()
    {
        $html = '<h1>Headline</h1><p>Text</p>';
        $styleSheet = 'EXT:pdfviewhelpers/Tests/Functional/Fixtures/HtmlViewHelper/styles.css';

        $output = $this->renderFluidTemplate(
            $this->getFixturePath('HtmlViewHelper/Html.html'),
            ['html' => $html, 'styleSheet' => $styleSheet]
        );
        $pdf = $this->parseContent($output);

        $this->assertContains('Headline', $pdf->getText());
        $this->assertContains('Text', $pdf->getText());
        $this->assertNotContains('<h1>', $pdf->getText());
        $this->assertNotContains('<p>', $pdf->getText());
    }

    /**
     * @test
     *
     * @expectedException \Bithost\Pdfviewhelpers\Exception\ValidationException
     */
    public function testInvalidStylesheet()
    {
        $this->renderFluidTemplate(
            $this->getFixturePath('HtmlViewHelper/Html.html'),
            ['html' => '', 'styleSheet' => 'EXT:pdfviewhelpers/Tests/Functional/Fixtures/HtmlViewHelper/NonExistingPath.css']
        );
    }
}
