<?php

declare(strict_types=1);

namespace Bithost\Pdfviewhelpers\Tests\Unit\Service;

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
use Bithost\Pdfviewhelpers\Service\ConversionService;
use Bithost\Pdfviewhelpers\Tests\Unit\AbstractUnitTestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * SettingsConversionServiceTest
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
 */
class SettingsConversionServiceTest extends AbstractUnitTestCase
{
    protected ConversionService $settingsConversionService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->settingsConversionService = new ConversionService();
    }

    #[Test]
    public function testHexToRGB(): void
    {
        $hexShort = '#05B';
        $hexLong = '#12ABCD';

        $this->assertEquals(['R' => 0, 'G' => 85, 'B' => 187], $this->settingsConversionService->convertHexToRGB($hexShort));
        $this->assertEquals(['R' => 18, 'G' => 171, 'B' => 205], $this->settingsConversionService->convertHexToRGB($hexLong));
    }

    #[Test]
    public function testOrientation(): void
    {
        $expectedConversions = [
            'portrait' => 'P',
            'P' => 'P',
            'landscape' => 'L',
            'L' => 'L',
        ];

        foreach ($expectedConversions as $input => $expectedOutput) {
            $this->assertEquals($expectedOutput, $this->settingsConversionService->convertSpeakingOrientationToTcpdfOrientation($input));
        }
    }

    #[Test]
    public function testInvalidOrientation(): void
    {
        $this->expectException(ValidationException::class);

        $this->settingsConversionService->convertSpeakingOrientationToTcpdfOrientation('foobar');
    }

    #[Test]
    public function testOutputDestination(): void
    {
        $expectedConversions = [
            'inline' => 'I',
            'I' => 'I',
            'download' => 'D',
            'D' => 'D',
            'file' => 'F',
            'F' => 'F',
            'file-inline' => 'FI',
            'FI' => 'FI',
            'file-download' => 'FD',
            'FD' => 'FD',
            'email' => 'E',
            'E' => 'E',
            'string' => 'S',
            'S' => 'S',
        ];

        foreach ($expectedConversions as $input => $expectedOutput) {
            $this->assertEquals($expectedOutput, $this->settingsConversionService->convertSpeakingOutputDestinationToTcpdfOutputDestination($input));
        }
    }

    #[Test]
    public function testInvalidOutputDestination(): void
    {
        $this->expectException(ValidationException::class);

        $this->settingsConversionService->convertSpeakingOutputDestinationToTcpdfOutputDestination('foobar');
    }

    #[Test]
    public function testFontStyle(): void
    {
        $expectedConversions = [
            'bold' => 'B',
            'B' => 'B',
            'italic' => 'I',
            'I' => 'I',
            'underline' => 'U',
            'U' => 'U',
            'regular' => '',
            'R' => '',
        ];

        foreach ($expectedConversions as $input => $expectedOutput) {
            $this->assertEquals($expectedOutput, $this->settingsConversionService->convertSpeakingFontStyleToTcpdfFontStyle($input));
        }
    }

    #[Test]
    public function testInvalidFontStyle(): void
    {
        $this->expectException(ValidationException::class);

        $this->settingsConversionService->convertSpeakingFontStyleToTcpdfFontStyle('foobar');
    }

    #[Test]
    public function testAlignment(): void
    {
        $expectedConversions = [
            'left' => 'L',
            'L' => 'L',
            'center' => 'C',
            'C' => 'C',
            'right' => 'R',
            'R' => 'R',
            'justify' => 'J',
            'J' => 'J',
        ];

        foreach ($expectedConversions as $input => $expectedOutput) {
            $this->assertEquals($expectedOutput, $this->settingsConversionService->convertSpeakingAlignmentToTcpdfAlignment($input));
        }
    }

    #[Test]
    public function testInvalidAlignment(): void
    {
        $this->expectException(ValidationException::class);

        $this->settingsConversionService->convertSpeakingAlignmentToTcpdfAlignment('foobar');
    }

    #[Test]
    public function testImageTypes(): void
    {
        $settings = [
            'config' => [
                'allowedImageTypes' => [
                    'image' => 'png,JPG',
                    'imageEPS' => 'ai,eps',
                    'imageSVG' => 'svG',
                ]
            ],
        ];

        $expectedConversions = [
            'png' => 'image',
            'PNG' => 'image',
            'jpg' => 'image',
            'JpG' => 'image',
            'ai' => 'imageEPS',
            'eps' => 'imageEPS',
            'svg' => 'imageSVG',
        ];


        $this->settingsConversionService->setSettings($settings);

        foreach ($expectedConversions as $input => $expectedOutput) {
            $this->assertEquals($expectedOutput, $this->settingsConversionService->convertImageExtensionToRenderMode($input));
        }
    }
}
