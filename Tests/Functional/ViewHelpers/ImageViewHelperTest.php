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
use TYPO3\CMS\Core\Resource\FileInterface;

/**
 * ImageViewHelperTest
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
 */
class ImageViewHelperTest extends AbstractFunctionalTest
{
    /**
     * @test
     */
    public function testStringSource()
    {
        $output = $this->renderFluidTemplate(
            $this->getFixturePath('ImageViewHelper/Image.html'),
            ['src' => 'EXT:pdfviewhelpers/Tests/Functional/Fixtures/ImageViewHelper/Bithost.jpg']
        );

        $pdf = $this->parseContent($output);
        $images = $pdf->getObjectsByType('XObject', 'Image');

        $this->assertCount(1, $images);
    }

    /**
     * @test
     */
    public function testFileSource()
    {
        $fileInterfaceMock = $this->createMock(FileInterface::class);
        $fileInterfaceMock->method('getContents')->willReturn(file_get_contents($this->getFixturePath('ImageViewHelper/Bithost.jpg')));
        $fileInterfaceMock->method('getExtension')->willReturn('jpg');

        $output = $this->renderFluidTemplate(
            $this->getFixturePath('ImageViewHelper/Image.html'),
            ['src' => $fileInterfaceMock]
        );

        $pdf = $this->parseContent($output);
        $images = $pdf->getObjectsByType('XObject', 'Image');

        $this->assertCount(1, $images);
    }

    /**
     * @test
     */
    public function testInvalidStringSource()
    {
        $this->expectException(\Bithost\Pdfviewhelpers\Exception\ValidationException::class);

        $output = $this->renderFluidTemplate(
            $this->getFixturePath('ImageViewHelper/Image.html'),
            ['src' => $this->getFixturePath('ImageViewHelper/NoFileHere.jpg')]
        );
    }
}
