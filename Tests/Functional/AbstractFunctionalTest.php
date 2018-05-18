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

use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use Smalot\PdfParser\Parser;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * BaseFunctionalTest
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Marin <esteban.marin@bithost.ch>
 */
abstract class AbstractFunctionalTest extends FunctionalTestCase
{
    /**
     * @var array
     */
    protected $testExtensionsToLoad = ['typo3conf/ext/pdfviewhelpers'];

    /**
     * @var array
     */
    protected $coreExtensionsToLoad = ['extbase', 'fluid'];

    /**
     * @var Parser
     */
    protected $parser;

    /**
     * Setup TYPO3 environment
     */
    public function setUp()
    {
        parent::setUp();

        $this->parser = new Parser();

        $this->importDataSet($this->getFixturePath('pages.xml'));
        $this->setUpPage();
    }

    /**
     * Load TypoScript files
     */
    public function setUpPage($typoScriptFiles = [])
    {
        $baseTypoScripts = [
            __DIR__ . '/../../Configuration/TypoScript/setup.txt',
        ];

        $this->setUpFrontendRootPage(
            1,
            array_merge($baseTypoScripts, $typoScriptFiles)
        );
    }

    /**
     * @param string $templatePath
     * @param array $variables
     *
     * @return mixed
     */
    protected function renderFluidTemplate($templatePath, $variables = [])
    {
        /** @var ObjectManager $objectManager */
        $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
        /** @var StandaloneView $standaloneView */
        $standaloneView = $objectManager->get(\TYPO3\CMS\Fluid\View\StandaloneView::class);

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
    protected function getFixturePath($path)
    {
        return __DIR__ . '/Fixtures/' . $path;
    }
}
