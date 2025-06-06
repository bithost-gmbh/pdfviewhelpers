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

use Bithost\Pdfviewhelpers\Exception\ValidationException;
use Bithost\Pdfviewhelpers\Tests\Functional\AbstractFunctionalTestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * TextViewHelperTest
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
 */
class TextViewHelperTest extends AbstractFunctionalTestCase
{
    /**
     * @var string
     */
    protected $untrimmedText = "\n\n\t\t     Some text containing        double whitespaces    \t\n\t\n";

    #[Test]
    public function testTrimAndRemoveDoubleWhitespace(): void
    {
        $output = $this->renderFluidTemplate(
            $this->getFixtureExtPath('TextViewHelper/Text.html'),
            ['text' => $this->untrimmedText]
        );
        $pdf = $this->parseContent($output);

        $this->assertStringContainsStringIgnoringCase('Some text containing double whitespaces', $pdf->getText());
    }

    #[Test]
    public function testNotRemoveDoubleWhitespace(): void
    {
        $output = $this->renderFluidTemplate(
            $this->getFixtureExtPath('TextViewHelper/TextOverwrite.html'),
            ['text' => $this->untrimmedText, 'trim' => 1, 'removeDoubleWhitespace' => 0]
        );
        $pdf = $this->parseContent($output);

        $this->assertStringContainsStringIgnoringCase(trim($this->untrimmedText), $pdf->getText());
    }

    #[Test]
    public function testColorShort(): void
    {
        $output = $this->renderFluidTemplate(
            $this->getFixtureExtPath('TextViewHelper/TextColor.html'),
            ['color' => '#333']
        );
        $pdf = $this->parseContent($output);

        $this->assertStringContainsStringIgnoringCase('Text', $pdf->getText());
    }

    #[Test]
    public function testColorLong(): void
    {
        $output = $this->renderFluidTemplate(
            $this->getFixtureExtPath('TextViewHelper/TextColor.html'),
            ['color' => '#123456']
        );
        $pdf = $this->parseContent($output);

        $this->assertStringContainsStringIgnoringCase('Text', $pdf->getText());
    }

    #[Test]
    public function testInvalidColor(): void
    {
        $this->expectException(\Bithost\Pdfviewhelpers\Exception\ValidationException::class);

        $this->renderFluidTemplate(
            $this->getFixtureExtPath('TextViewHelper/TextColor.html'),
            ['color' => '#1']
        );
    }

    #[Test]
    public function testPartialPaddingOverwrite(): void
    {
        $output = $this->renderFluidTemplate(
            $this->getFixtureExtPath('TextViewHelper/TextPadding.html'),
            ['padding' => ['top' => 2]]
        );
        $pdf = $this->parseContent($output);

        $this->assertStringContainsStringIgnoringCase('Text', $pdf->getText());
    }

    #[Test]
    public function testInvalidPAddingKey(): void
    {
        $this->expectException(ValidationException::class);

        $this->renderFluidTemplate(
            $this->getFixtureExtPath('TextViewHelper/TextPadding.html'),
            ['padding' => ['tops' => 2]]
        );
    }

    #[Test]
    public function testInvalidPAdding(): void
    {
        $this->expectException(ValidationException::class);

        $this->renderFluidTemplate(
            $this->getFixtureExtPath('TextViewHelper/TextPadding.html'),
            ['padding' => ['bottom' => 'lorem']]
        );
    }
}
