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

use Bithost\Pdfviewhelpers\Exception\Exception;
use Bithost\Pdfviewhelpers\Exception\ValidationException;
use Bithost\Pdfviewhelpers\Service\ValidationService;
use Bithost\Pdfviewhelpers\Tests\Unit\AbstractUnitTest;

/**
 * ValidationServiceTest
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
 */
class ValidationServiceTest extends AbstractUnitTest
{
    protected ValidationService $validationService;

    protected function setUp(): void
    {
        $this->validationService = new ValidationService();
    }

    /**
     * @test
     */
    public function testValidPadding(): void
    {
        $padding = ['top' => 10, 'right' => 0, 'bottom' => 20, 'left' => 10];

        $this->assertTrue($this->validationService->validatePadding($padding));
    }

    /**
     * @test
     */
    public function testInvalidPaddingMissingAttribute(): void
    {
        $padding = ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0];

        foreach ($padding as $key => $value) {
            $localPadding = $padding;

            unset($localPadding[$key]);

            try {
                $this->validationService->validatePadding($localPadding);
                $this->fail();
            } catch (Exception $e) {
                $this->assertInstanceOf(ValidationException::class, $e);
            }
        }
    }

    /**
     * @test
     */
    public function testInvalidPaddingNonNumeric(): void
    {
        $padding = ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0];

        foreach ($padding as $key => $value) {
            $localPadding = $padding;

            $localPadding[$key] = 'non numeric';

            try {
                $this->validationService->validatePadding($localPadding);
                $this->fail();
            } catch (Exception $e) {
                $this->assertInstanceOf(ValidationException::class, $e);
            }
        }
    }

    /**
     * @test
     */
    public function testInvalidPaddingTooManyElements(): void
    {
        $padding = ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0, 'bla' => 0];

        $this->expectException(\Bithost\Pdfviewhelpers\Exception\ValidationException::class);
        $this->validationService->validatePadding($padding);
    }

    /**
     * @test
     */
    public function testColor(): void
    {
        $colorShort = '#1B3';
        $colorLong = '#8ABC99';

        $this->assertTrue($this->validationService->validateColor($colorShort));
        $this->assertTrue($this->validationService->validateColor($colorLong));
    }

    /**
     * @test
     */
    public function testInvalidColor(): void
    {
        $invalidColors = ['#1Y3', '#8ABC99B', '123', '#12'];

        foreach ($invalidColors as $color) {
            try {
                $this->validationService->validateColor($color);
                $this->fail();
            } catch (Exception $e) {
                $this->assertInstanceOf(ValidationException::class, $e);
            }
        }
    }

    /**
     * @test
     */
    public function testWidth(): void
    {
        $validWidths = ['10', '0', '1', '100.5'];

        foreach ($validWidths as $width) {
            $this->assertTrue($this->validationService->validateWidth($width));
        }
    }

    /**
     * @test
     */
    public function testInvalidWidth(): void
    {
        $invalidWidths = ['-1', '-0.1', 'a10', '10b', 'abc', ''];

        foreach ($invalidWidths as $width) {
            try {
                $this->validationService->validateWidth($width);
                $this->fail();
            } catch (Exception $e) {
                $this->assertInstanceOf(ValidationException::class, $e);
            }
        }
    }

    /**
     * @test
     */
    public function testFontSize(): void
    {
        $validFontSizes = ['10', '0', '1', '100.5'];

        foreach ($validFontSizes as $fontSize) {
            $this->assertTrue($this->validationService->validateFontSize($fontSize));
        }
    }

    /**
     * @test
     */
    public function testInvalidFontSize(): void
    {
        $invalidFontSizes = ['-1', '-0.1', 'a10', '10b', 'abc', ''];

        foreach ($invalidFontSizes as $fontSize) {
            try {
                $this->validationService->validateFontSize($fontSize);
                $this->fail();
            } catch (Exception $e) {
                $this->assertInstanceOf(ValidationException::class, $e);
            }
        }
    }

    /**
     * @test
     */
    public function testFontFamily(): void
    {
        $fontFamilies = ['robotob', 'courieri', 'timesb'];

        foreach ($fontFamilies as $fontFamily) {
            $this->assertTrue($this->validationService->validateFontFamily($fontFamily));
        }
    }

    /**
     * @test
     */
    public function testInvalidFontFamily(): void
    {
        $invalidFontFamilies = ['robotobold', 'courieritalic', 'times-b', ''];

        foreach ($invalidFontFamilies as $fontFamily) {
            try {
                $this->validationService->validateFontFamily($fontFamily);
                $this->fail();
            } catch (Exception $e) {
                $this->assertInstanceOf(ValidationException::class, $e);
            }
        }
    }

    /**
     * @test
     */
    public function testParaghraphSpacing(): void
    {
        $validParagraphSpacing = ['10', '0', '1', '100.5'];

        foreach ($validParagraphSpacing as $paragraphSpacing) {
            $this->assertTrue($this->validationService->validateParagraphSpacing($paragraphSpacing));
        }
    }

    /**
     * @test
     */
    public function testInvalidParaghraphSpacing(): void
    {
        $invalidParagraphSpacing = ['-1', '-0.1', 'a10', '10b', 'abc', ''];

        foreach ($invalidParagraphSpacing as $paragraphSpacing) {
            try {
                $this->validationService->validateParagraphSpacing($paragraphSpacing);
                $this->fail();
            } catch (Exception $e) {
                $this->assertInstanceOf(ValidationException::class, $e);
            }
        }
    }

    /**
     * @test
     */
    public function testHeight(): void
    {
        $validHeight = ['10', '0', '1', '100.5'];

        foreach ($validHeight as $height) {
            $this->assertTrue($this->validationService->validateHeight($height));
        }
    }

    /**
     * @test
     */
    public function testInvalidHeight(): void
    {
        $invalidHeight = ['-1', '-0.1', 'a10', '10b', 'abc', ''];

        foreach ($invalidHeight as $height) {
            try {
                $this->validationService->validateHeight($height);
                $this->fail();
            } catch (Exception $e) {
                $this->assertInstanceOf(ValidationException::class, $e);
            }
        }
    }
}
