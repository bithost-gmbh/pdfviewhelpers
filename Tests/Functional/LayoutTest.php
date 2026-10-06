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

use PHPUnit\Framework\Attributes\Test;

/**
 * LayoutTest
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
 */
class LayoutTest extends AbstractFunctionalTestCase
{
    protected array $typoScriptFiles = [
        'EXT:pdfviewhelpers/Tests/Functional/Fixtures/Layout/setup.typoscript',
    ];

    #[Test]
    public function testPageBreakInColumnContinuesOnExistingPage(): void
    {
        $output = $this->renderFluidTemplate($this->getFixtureExtPath('Layout/ColumnPageBreak.html'));
        $pages = $this->parseContent($output)->getPages();

        $this->validatePDF($output);
        $this->assertCount(2, $pages);
        $this->assertStringContainsString('Column two before break', $pages[0]->getText());
        $this->assertStringContainsString('Column two after break', $pages[1]->getText());
        $this->assertStringContainsString('After columns', $pages[1]->getText());
    }

    #[Test]
    public function testPageNumberAliasesWithAllFonts(): void
    {
        $output = $this->renderFluidTemplate($this->getFixtureExtPath('Layout/PageNumberAlias.html'));
        $pages = $this->parseContent($output)->getPages();

        $this->validatePDF($output);
        $this->assertCount(2, $pages);
        $this->assertStringContainsString('Roboto body 1 of 2', $pages[0]->getText());
        $this->assertStringContainsString('Helvetica body 2 of 2', $pages[1]->getText());

        foreach ($pages as $index => $page) {
            $this->assertStringContainsString('HTML footer ' . ($index + 1), $page->getText());
            $this->assertStringContainsString('Roboto footer ' . ($index + 1) . ' of 2', $page->getText());
            $this->assertStringNotContainsString('~#', $page->getText());
        }
    }

    #[Test]
    public function testTextIsNotRepeatedWithoutAutoPageBreak(): void
    {
        $output = $this->renderFluidTemplate($this->getFixtureExtPath('Layout/NoAutoPageBreak.html'));
        $pages = $this->parseContent($output)->getPages();

        $this->validatePDF($output);
        $this->assertCount(1, $pages);
        $this->assertStringContainsString('Paragraph 1', $pages[0]->getText());
        $this->assertStringNotContainsString('Paragraph 5', $pages[0]->getText());
    }

    #[Test]
    public function testAttachPdfWithoutPage(): void
    {
        $output = $this->renderFluidTemplate($this->getFixtureExtPath('Layout/AttachPdfOnly.html'));

        $this->validatePDF($output);
        $this->assertCount(1, $this->parseContent($output)->getPages());
    }

    #[Test]
    public function testKeepMarginsUsesBottomMarginOfPage(): void
    {
        $output = $this->renderFluidTemplate($this->getFixtureExtPath('Layout/KeepMargins.html'));
        $pages = $this->parseContent($output)->getPages();

        $this->validatePDF($output);
        $this->assertGreaterThan(2, count($pages));

        // text positions are measured from the bottom of the page in points, 30mm are 85pt
        foreach ($pages[1]->getDataTm() as [$matrix]) {
            $this->assertGreaterThan(85, (float) $matrix[5]);
        }
    }

    #[Test]
    public function testHtmlHeaderAndFooterWithHtmlContentBreakingPages(): void
    {
        $output = $this->renderFluidTemplate($this->getFixtureExtPath('Layout/HtmlFooterWithHtmlContent.html'));
        $pages = $this->parseContent($output)->getPages();

        $this->validatePDF($output);
        $this->assertGreaterThan(1, count($pages));
        $this->assertStringContainsString('After the HTML', $pages[count($pages) - 1]->getText());

        foreach ($pages as $page) {
            $this->assertSame(1, substr_count($page->getText(), 'HTML header'));
            $this->assertSame(1, substr_count($page->getText(), 'HTML footer'));
        }
    }

    #[Test]
    public function testHtmlCenteredWithTrailingWhitespace(): void
    {
        $output = $this->renderFluidTemplate($this->getFixtureExtPath('Layout/HtmlCenteredTrailingWhitespace.html'));
        $positions = [];

        foreach ($this->parseContent($output)->getPages()[0]->getDataTm() as [$matrix, $text]) {
            if (trim($text) === 'Centered') {
                $positions[] = round((float) $matrix[4]);
            }
        }

        $this->assertCount(2, $positions);
        $this->assertSame($positions[0], $positions[1]);
    }
}
