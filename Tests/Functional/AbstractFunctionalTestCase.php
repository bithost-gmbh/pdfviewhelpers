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

use Bithost\Pdfviewhelpers\Tests\Functional\Traits\SetUpFrontendSiteTrait;
use Smalot\PdfParser\Document;
use Smalot\PdfParser\Parser;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\TypoScript\AST\Node\RootNode;
use TYPO3\CMS\Core\TypoScript\FrontendTypoScript;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * BaseFunctionalTest
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
 */
abstract class AbstractFunctionalTestCase extends FunctionalTestCase
{
    use SetUpFrontendSiteTrait;

    protected array $testExtensionsToLoad = [
        'bithost-gmbh/pdfviewhelpers',
    ];

    /**
     * 150 words 890 characters
     */
    protected string $loremIpsumText = 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.';

    private array $baseTypoScript = [
        'plugin.' => [
            'tx_pdfviewhelpers.' => [
                'settings.' => [
                    'staticTypoScriptSetupIncluded' => 1,
                    'config.' => [
                        'class' => 'Bithost\\Pdfviewhelpers\\Tests\\Functional\\TestBasePDF',
                        'disableCache' => 1,
                        'exitAfterPdfContentOutput' => 0,
                        'jpgQuality' => 100,
                        'sRGBMode' => 0,
                        'allowedImageTypes.' => [
                            'image' => 'jpg,jpeg,png,gif',
                            'imageEPS' => 'ai,eps',
                            'imageSVG' => 'svg',
                        ],
                        'fonts.' => [
                            'subset' => 1,
                            'outputPath' => 'typo3temp/pdfviewhelpers/fonts/',
                            'addTTFFont.' => [
                                // Example fonts can be added here
                                // 'roboto.' => [
                                //     'path' => 'EXT:pdfviewhelpers/Resources/Public/Examples/FullFeatureShowCase/Roboto.ttf',
                                // ],
                                // 'opensans.' => [
                                //     'path' => 'EXT:pdfviewhelpers/Resources/Public/Examples/FullFeatureShowCase/OpenSans.ttf',
                                // ],
                            ],
                        ],
                    ],
                    'document.' => [
                        'title' => '',
                        'subject' => '',
                        'author' => '',
                        'keywords' => '',
                        'creator' => 'TYPO3 EXT:pdfviewhelpers',
                        'outputDestination' => 'inline',
                        'outputPath' => 'document.pdf',
                        'sourceFile' => '',
                        'unit' => 'mm',
                        'unicode' => 1,
                        'encoding' => 'UTF-8',
                        'pdfa' => 0,
                        'pdfua' => 0,
                        'language' => 'ger',
                        'hyphenFile' => 'hyph-de-ch-1901.tex',
                    ],
                    'page.' => [
                        'autoPageBreak' => 1,
                        'margin.' => [
                            'top' => 15,
                            'right' => 15,
                            'bottom' => 15,
                            'left' => 15,
                        ],
                        'importPage' => '',
                        'importPageOnAutomaticPageBreak' => 1,
                        'orientation' => 'portrait',
                        'format' => 'A4',
                        'keepMargins' => 0,
                        'tableOfContentPage' => 0,
                    ],
                    'header.' => [
                        'posY' => 5,
                    ],
                    'footer.' => [
                        'posY' => -10,
                    ],
                    'avoidPageBreakInside.' => [
                        'breakIfImpossibleToAvoid' => 0,
                    ],
                    'generalText.' => [
                        'trim' => 1,
                        'removeDoubleWhitespace' => 1,
                        'color' => '#000',
                        'fontFamily' => 'helvetica',
                        'fontSize' => 11,
                        'fontStyle' => 'regular',
                        'lineHeight' => 1.25,
                        'characterSpacing' => 0,
                        'alignment' => 'left',
                        'paragraphSpacing' => 2,
                        'paragraphLineFeed' => 0,
                        'autoHyphenation' => 0,
                        'padding.' => [
                            'top' => 0,
                            'right' => 0,
                            'bottom' => 0,
                            'left' => 0,
                        ],
                        'types.' => [],
                    ],
                    'text.' => [
                        'trim' => '',
                        'removeDoubleWhitespace' => '',
                        'color' => '',
                        'fontFamily' => '',
                        'fontSize' => '',
                        'fontStyle' => '',
                        'lineHeight' => '',
                        'characterSpacing' => '',
                        'alignment' => '',
                        'paragraphSpacing' => '',
                        'paragraphLineFeed' => '',
                        'autoHyphenation' => '',
                        'padding.' => [],
                        'types.' => [],
                    ],
                    'headline.' => [
                        'trim' => '',
                        'removeDoubleWhitespace' => '',
                        'color' => '',
                        'fontFamily' => '',
                        'fontSize' => 16,
                        'fontStyle' => '',
                        'lineHeight' => '',
                        'characterSpacing' => '',
                        'alignment' => '',
                        'paragraphSpacing' => 0,
                        'paragraphLineFeed' => '',
                        'autoHyphenation' => '',
                        'addToTableOfContent' => 0,
                        'tableOfContentLevel' => 0,
                        'padding.' => [
                            'top' => 6,
                            'bottom' => 3,
                        ],
                        'types.' => [],
                    ],
                    'list.' => [
                        'trim' => '',
                        'removeDoubleWhitespace' => '',
                        'color' => '',
                        'fontFamily' => '',
                        'fontSize' => '',
                        'fontStyle' => '',
                        'lineHeight' => '',
                        'characterSpacing' => '',
                        'paragraphLineFeed' => '',
                        'alignment' => 'left',
                        'autoHyphenation' => '',
                        'padding.' => [
                            'bottom' => 2,
                            'left' => 1.5,
                        ],
                        'bulletColor' => '',
                        'bulletImageSrc' => '',
                        'bulletSize' => 1.5,
                        'types.' => [],
                    ],
                    'image.' => [
                        'alignment' => 'left',
                        'fitOnPage' => 1,
                        'padding.' => [
                            'top' => 0,
                            'right' => 0,
                            'bottom' => 2,
                            'left' => 0,
                        ],
                        'processingInstructions.' => [
                            // Example crop instructions can be implemented here
                            // 'width' => '',
                            // 'height' => '',
                            // 'crop.' => [
                            //     'custom_crop.' => [
                            //         'cropArea.' => [
                            //             'width' => 0.5,
                            //             'height' => 0.5,
                            //             'x' => 0,
                            //             'y' => 0,
                            //         ],
                            //     ],
                            // ],
                        ],
                    ],
                    'html.' => [
                        'autoHyphenation' => '',
                        'styleSheet' => '',
                        'listIndentWidth' => '',
                        'padding.' => [
                            'top' => 0,
                            'right' => 0,
                            'bottom' => 2,
                            'left' => 0,
                        ],
                    ],
                    'graphics.' => [
                        'line.' => [
                            'padding.' => [
                                'top' => 4,
                                'right' => 0,
                                'bottom' => 5,
                                'left' => 0,
                            ],
                            'style.' => [
                                'width' => 0.25,
                                'color' => '#000',
                            ],
                        ],
                    ],
                    'tableOfContent.' => [
                        'page' => 1,
                        'numbersFont' => '',
                        'filter' => '.',
                        'name' => '',
                        'htmlMode' => 0,
                        'fontFamily' => '',
                        'fontSize' => '',
                        'lineHeight' => '',
                        'characterSpacing' => '',
                        'padding.' => [
                            'top' => 0,
                            'right' => 0,
                            'bottom' => 2,
                            'left' => 0,
                        ],
                    ],
                    'htmlBookmarkTemplate.' => [
                        'level' => 0,
                        'sanitizeWhitespace' => 1,
                    ],
                    'bookmark.' => [
                        'level' => 0,
                        'fontStyle' => '',
                        'color' => '',
                    ],
                ],
            ],
        ],
        'module.' => [
            'tx_pdfviewhelpers' => '< plugin.tx_pdfviewhelpers',
        ],
    ];

    protected array $overrideTypoScript = [];

    /**
     * Setup TYPO3 environment
     *
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $frontendTypoScript = new FrontendTypoScript(new RootNode(), [], [], []);
        $frontendTypoScript->setSetupArray(array_merge($this->baseTypoScript, $this->overrideTypoScript));

        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest())
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_FE)
            ->withAttribute('frontend.typoscript', $frontendTypoScript);
    }

    protected function renderFluidTemplate(string $templatePath, array $variables = []): string
    {
        /** @var ViewFactoryInterface $viewFactory */
        $viewFactory = GeneralUtility::makeInstance(ViewFactoryInterface::class);
        $viewFactoryData = new ViewFactoryData(
            templateRootPaths: [$this->getFixtureExtPath('')],
            templatePathAndFilename: $templatePath,
            request: $GLOBALS['TYPO3_REQUEST'],
        );
        $view = $viewFactory->create($viewFactoryData);

        $view->assignMultiple($variables);

        return $view->render();
    }

    protected function getFixtureExtPath(string $path): string
    {
        return 'EXT:pdfviewhelpers/Tests/Functional/Fixtures/' . $path;
    }

    protected function getFixtureAbsolutePath(string $path): string
    {
        return GeneralUtility::getFileAbsFileName($this->getFixtureExtPath($path));
    }

    public function parseFile(string $filename): Document
    {
        $parser = new Parser();

        return $parser->parseFile($filename);
    }

    public function parseContent(string $content): Document
    {
        $parser = new Parser();

        return $parser->parseContent($content);
    }

    protected function getLongLoremIpsumText(int $duplicates): string
    {
        $longLoremIpsumText = '';
        $duplicates = max($duplicates, 0);

        for ($i = 0; $i < $duplicates; $i++) {
            $longLoremIpsumText .= $this->loremIpsumText;
        }

        return $longLoremIpsumText;
    }

    protected function validatePDF(string $pdf): void
    {
        $tempFilePath = GeneralUtility::tempnam('pdfviewhelpers-pdf-validation-temp-file', '.pdf');
        $resultCode = null;
        $output = [];

        file_put_contents($tempFilePath, $pdf);
        // Redirect stderr to stdout and silent regular stdout
        exec('qpdf --check ' . escapeshellarg($tempFilePath) . ' 2>&1 >/dev/null', $output, $resultCode);

        self::assertEquals(0, $resultCode, implode(PHP_EOL, $output));
        self::assertEmpty($output, implode(PHP_EOL, $output));
    }
}
