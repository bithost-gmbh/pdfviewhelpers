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

use Bithost\Pdfviewhelpers\Tests\Functional\AbstractFunctionalTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use PHPUnit\Framework\Attributes\Test;

/**
 * DocumentViewHelperTest
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
 */
class DocumentViewHelperTest extends AbstractFunctionalTestCase
{
    #[Test]
    public function testMetaInformation(): void
    {
        $output = $this->renderFluidTemplate($this->getFixtureExtPath('DocumentViewHelper/MetaInformation.html'));
        $pdf = $this->parseContent($output);
        $details = $pdf->getDetails();

        $this->assertEquals('TYPO3 EXT:pdfviewhelpers', $details['Creator']);
    }

    #[Test]
    public function testMetaInformationOverwrite(): void
    {
        $output = $this->renderFluidTemplate(
            $this->getFixtureExtPath('DocumentViewHelper/MetaInformationOverwrite.html'),
            ['creator' => 'Some other creator']
        );
        $pdf = $this->parseContent($output);
        $details = $pdf->getDetails();

        $this->assertEquals('Some other creator', $details['Creator']);
    }

    #[Test]
    public function testOutputDestinationF(): void
    {
        $output = $this->renderFluidTemplate(
            $this->getFixtureExtPath('DocumentViewHelper/OutputDestination.html'),
            ['outputDestination' => 'F']
        );
        $savePath = GeneralUtility::getFileAbsFileName('DocumentOutputDestination.pdf');

        $this->assertEmpty(trim($output));
        $this->assertFileExists($savePath);
        $this->assertNotEmpty(file_get_contents($savePath));
        $this->assertGreaterThan(5000, filesize($savePath));
    }

    #[Test]
    public function testOutputDestinationS(): void
    {
        $output = $this->renderFluidTemplate(
            $this->getFixtureExtPath('DocumentViewHelper/OutputDestination.html'),
            ['outputDestination' => 'S']
        );

        $this->assertNotEmpty(trim($output));
    }

    #[Test]
    public function testOutputDestinationE(): void
    {
        $output = $this->renderFluidTemplate(
            $this->getFixtureExtPath('DocumentViewHelper/OutputDestination.html'),
            ['outputDestination' => 'E']
        );

        $this->assertNotEmpty(trim($output));
    }
}
