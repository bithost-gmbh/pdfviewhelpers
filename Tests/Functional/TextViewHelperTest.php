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
 * TextViewHelperTest
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Marin <esteban.marin@bithost.ch>
 */
class TextViewHelperTest extends AbstractFunctionalTest
{
    /**
     * @var string
     */
    protected $untrimmedText = "\n\n\t\t     Some text containing        double whitespaces    \t\n\t\n";

    /**
     * @test
     */
    public function testTrimAndRemoveDoubleWhitespace()
    {
        $output = $this->renderFluidTemplate(
            $this->getFixturePath('TextViewHelper/Text.html'),
            ['text' => $this->untrimmedText]
        );
        $pdf = $this->parseContent($output);

        $this->assertContains('Some text containing double whitespaces', $pdf->getText());
    }

    /**
     * @test
     */
    public function testNotRemoveDoubleWhitespace()
    {
        $output = $this->renderFluidTemplate(
            $this->getFixturePath('TextViewHelper/TextOverwrite.html'),
            ['text' => $this->untrimmedText, 'trim' => 1, 'removeDoubleWhitespace' => 0]
        );
        $pdf = $this->parseContent($output);

        $this->assertContains(trim($this->untrimmedText), $pdf->getText());
    }

    /**
     * @test
     */
    public function testColorShort()
    {
        $output = $this->renderFluidTemplate(
            $this->getFixturePath('TextViewHelper/TextColor.html'),
            ['color' => '#333']
        );
        $pdf = $this->parseContent($output);

        $this->assertContains('Text', $pdf->getText());
    }

    /**
     * @test
     */
    public function testColorLong()
    {
        $output = $this->renderFluidTemplate(
            $this->getFixturePath('TextViewHelper/TextColor.html'),
            ['color' => '#123456']
        );
        $pdf = $this->parseContent($output);

        $this->assertContains('Text', $pdf->getText());
    }

    /**
     * @test
     *
     * @expectedException \Bithost\Pdfviewhelpers\Exception\ValidationException
     */
    public function testInvalidColor()
    {
        $this->renderFluidTemplate(
            $this->getFixturePath('TextViewHelper/TextColor.html'),
            ['color' => '#1']
        );
    }
}
