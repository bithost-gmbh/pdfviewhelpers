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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use Smalot\PdfParser\Document;
use Smalot\PdfParser\Parser;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * BaseFunctionalTest
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
 */
abstract class AbstractFunctionalTest extends FunctionalTestCase
{
    /**
     * @var array
     */
    protected $testExtensionsToLoad = ['typo3conf/ext/pdfviewhelpers'];

    /**
     * 150 words 890 characters
     *
     * @var string
     */
    protected $loremIpsumText = 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.';

    /**
     * @var array
     */
    protected $typoScriptFiles = [];

    /**
     * Setup TYPO3 environment
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $baseTypoScripts = [
            'EXT:pdfviewhelpers/Configuration/TypoScript/setup.txt',
        ];

        $this->importDataSet($this->getFixtureExtPath('pages.xml'));
        $this->setUpFrontendRootPage(
            1,
            array_merge($baseTypoScripts, $this->typoScriptFiles)
        );
        $this->setUpBackendUserFromFixture(1);
    }

    /**
     * @param string $templatePath
     * @param array $variables
     *
     * @return mixed
     */
    protected function renderFluidTemplate($templatePath, $variables = [])
    {
        /** @var StandaloneView $standaloneView */
        $standaloneView = GeneralUtility::makeInstance(StandaloneView::class);

        $standaloneView->setFormat('html');
        $standaloneView->setTemplatePathAndFilename($templatePath);
        $standaloneView->assignMultiple($variables);

        return $standaloneView->render();
    }

    /**
     * @param string $path
     *
     * @return string
     */
    protected function getFixtureExtPath($path)
    {
        return 'EXT:pdfviewhelpers/Tests/Functional/Fixtures/' . $path;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    protected function getFixtureAbsolutePath($path)
    {
        return GeneralUtility::getFileAbsFileName($this->getFixtureExtPath($path));
    }

    /**
     * @param string $filename
     *
     * @return Document
     */
    public function parseFile($filename)
    {
        $parser = new Parser();

        return $parser->parseFile($filename);
    }

    /**
     * @param string $content
     *
     * @return Document
     */
    public function parseContent($content)
    {
        $parser = new Parser();

        return $parser->parseContent($content);
    }

    /**
     * @param integer $duplicates
     *
     * @return string
     */
    protected function getLongLoremIpsumText($duplicates)
    {
        $longLoremIpsumText = '';
        $duplicates = max($duplicates, 0);

        for ($i=0; $i < $duplicates; $i++) {
            $longLoremIpsumText .= $this->loremIpsumText;
        }

        return $longLoremIpsumText;
    }

    /**
     * @param string $pdf
     */
    protected function validatePDF($pdf)
    {
        $tempFilePath = GeneralUtility::tempnam('pdfviewhelpers-pdf-validation-temp-file', '.pdf');
        $resultCode = null;
        $output = [];

        file_put_contents($tempFilePath, $pdf);
        // Redirect stderr to stdout and silent regular stdout
        exec('qpdf --check ' . escapeshellarg($tempFilePath) . ' 2>&1 >/dev/null', $output, $resultCode);

        $this->assertEquals(0, $resultCode, implode(PHP_EOL, $output));
        $this->assertEmpty($output, implode(PHP_EOL, $output));
    }
}
