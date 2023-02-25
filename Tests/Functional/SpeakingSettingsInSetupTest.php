<?php

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

/**
 * SpeakingSettingsInSetupTest
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
 */
class SpeakingSettingsInSetupTest extends AbstractFunctionalTest
{
    protected $typoScriptFiles = [
        'EXT:pdfviewhelpers/Tests/Functional/Fixtures/SpeakingSettings/setup_speaking.txt',
    ];

    /**
     * @test
     */
    public function testSpeakingSetupSettings()
    {
        $this->markTestSkipped('Changing settings in TS not working because of AbstractViewHelper::$argumentDefinitionCache.');

        $output = $this->renderFluidTemplate($this->getFixtureExtPath('SpeakingSettings/TypoScript.html'));
        $pdf = $this->parseContent($output);
        $text = $pdf->getText();

        $this->assertStringContainsStringIgnoringCase('Test', $text);
    }
}