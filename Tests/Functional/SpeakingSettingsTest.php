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

use Bithost\Pdfviewhelpers\Exception\ValidationException;
use PHPUnit\Framework\Attributes\Test;

/**
 * SpeakingSettingsTest
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
 */
class SpeakingSettingsTest extends AbstractFunctionalTestCase
{
    protected array $typoScriptFiles = [
        'EXT:pdfviewhelpers/Tests/Functional/Fixtures/SpeakingSettings/setup.typoscript',
    ];

    #[Test]
    public function testSetupSettings(): void
    {
        $this->markTestSkipped('Changing settings in TS not working because of AbstractViewHelper::$argumentDefinitionCache.');

        $output = $this->renderFluidTemplate($this->getFixtureExtPath('SpeakingSettings/TypoScript.html'));
        $pdf = $this->parseContent($output);
        $text = $pdf->getText();

        $this->assertStringContainsStringIgnoringCase('Test', $text);
    }

    #[Test]
    public function testFluid(): void
    {
        $outputDestinations = ['string', 'S'];
        $orientations = ['portrait', 'P', 'landscape', 'L'];
        $fontStyles = ['bold', 'B', 'italic', 'I', 'regular', 'R', 'underline', 'U'];
        $alignments = ['left', 'L', 'center', 'C', 'right', 'R', 'justify', 'J'];

        $outputDestinationsCount = count($outputDestinations);
        $orientationsCount = count($orientations);
        $fontStylesCount = count($fontStyles);
        $alignmentsCount = count($alignments);

        $maxElements = max($outputDestinationsCount, $orientationsCount, $fontStylesCount, $alignmentsCount);

        for ($i=0; $i<$maxElements; $i++) {
            $output = $this->renderFluidTemplate(
                $this->getFixtureExtPath('SpeakingSettings/FluidAttributes.html'),
                [
                    'outputDestination' => $outputDestinations[$i % $outputDestinationsCount],
                    'orientation' => $orientations[$i % $orientationsCount],
                    'fontStyle' => $fontStyles[$i % $fontStylesCount],
                    'alignment' => $alignments[$i % $alignmentsCount],
                ]
            );
            $pdf = $this->parseContent($output);
            $text = $pdf->getText();

            $this->assertStringContainsStringIgnoringCase('Test', $text);
        }
    }

    #[Test]
    public function testInvalidOutputDestination(): void
    {
        $this->expectException(ValidationException::class);

        $this->renderFluidTemplate(
            $this->getFixtureExtPath('SpeakingSettings/FluidAttributes.html'),
            [
                'outputDestination' => 'invalid',
                'orientation' => 'L',
                'fontStyle' => 'B',
                'alignment' => 'L',
            ]
        );
    }

    #[Test]
    public function testInvalidOrientation(): void
    {
        $this->expectException(ValidationException::class);

        $this->renderFluidTemplate(
            $this->getFixtureExtPath('SpeakingSettings/FluidAttributes.html'),
            [
                'outputDestination' => 'S',
                'orientation' => 'XYZ',
                'fontStyle' => 'B',
                'alignment' => 'L',
            ]
        );
    }

    #[Test]
    public function testInvalidFontStyle(): void
    {
        $this->expectException(ValidationException::class);

        $this->renderFluidTemplate(
            $this->getFixtureExtPath('SpeakingSettings/FluidAttributes.html'),
            [
                'outputDestination' => 'S',
                'orientation' => 'P',
                'fontStyle' => 'nothing',
                'alignment' => 'L',
            ]
        );
    }

    #[Test]
    public function testInvalidAlignment(): void
    {
        $this->expectException(ValidationException::class);

        $this->renderFluidTemplate(
            $this->getFixtureExtPath('SpeakingSettings/FluidAttributes.html'),
            [
                'outputDestination' => 'S',
                'orientation' => 'P',
                'fontStyle' => 'B',
                'alignment' => 'rectify',
            ]
        );
    }
}
