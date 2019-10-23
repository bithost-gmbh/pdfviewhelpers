<?php

namespace Bithost\Pdfviewhelpers\ViewHelpers;

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

use Bithost\Pdfviewhelpers\Exception\Exception;
use Bithost\Pdfviewhelpers\Exception\ValidationException;
use Bithost\Pdfviewhelpers\Model\BasePDF;
use setasign\Fpdi\PdfParser\PdfParserException;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * DocumentViewHelper
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Marin <esteban.marin@bithost.ch>
 */
class DocumentViewHelper extends AbstractPDFViewHelper
{
    /**
     * TCPDF output destinations that send http headers and echo the pdf
     *
     * @var array
     */
    protected $tcpdfOutputContentDestinations = ['I', 'D', 'FI', 'FD'];

    /**
     * TCPDF output destinations that save the pdf to the filesystem
     *
     * @var array
     */
    protected $tcpdfSaveFileDestinations = ['F', 'FI', 'FD'];

    /**
     * TCPDF output destinations that return the pdf as string
     *
     * @var array
     */
    protected $tcpdfReturnContentDestinations = ['S', 'E'];

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('title', 'string', 'The title of the document.', false, $this->settings['document']['title']);
        $this->registerArgument('subject', 'string', 'The subject of the document.', false, $this->settings['document']['subject']);
        $this->registerArgument('author', 'string', 'The author of the document.', false, $this->settings['document']['author']);
        $this->registerArgument('keywords', 'string', 'Keywords describing the document.', false, $this->settings['document']['keywords']);
        $this->registerArgument('creator', 'string', 'The creator of the document.', false, $this->settings['document']['creator']);
        $this->registerArgument('outputDestination', 'string', 'The output destination of the document: inline, download, file, file-inline, file-download, email or string', false, $this->settings['document']['outputDestination']);
        $this->registerArgument('outputPath', 'string', 'The name or path of the saved document.', false, $this->settings['document']['outputPath']);
        $this->registerArgument('sourceFile', 'string', 'The path to the source file for templates to be applied to this document.', false, $this->settings['document']['sourceFile']);
        $this->registerArgument('unit', 'string', 'The default unit of measure.', false, $this->settings['document']['unit']);
        $this->registerArgument('unicode', 'boolean', 'If true unicode is used.', false, (boolean) $this->settings['document']['unicode']);
        $this->registerArgument('encoding', 'string', 'The encoding of the document.', false, $this->settings['document']['encoding']);
        $this->registerArgument('pdfa', 'boolean', 'If true PDF/A mode is enabled.', false, (boolean) $this->settings['document']['pdfa']);
        $this->registerArgument('language', 'string', 'The language of the document.', false, $this->settings['document']['language']);
        $this->registerArgument('hyphenFile', 'string', 'The hyphen file to be used for the automatic hyphenation.', false, $this->settings['document']['hyphenFile']);
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    public function initialize()
    {
        parent::initialize();

        $this->arguments['outputDestination'] = $this->conversionService->convertSpeakingOutputDestinationToTcpdfOutputDestination($this->arguments['outputDestination']);

        if (isset($GLOBALS['TSFE']->applicationData) && in_array($this->arguments['outputDestination'], $this->tcpdfOutputContentDestinations)) {
            $GLOBALS['TSFE']->applicationData['tx_pdfviewhelpers']['pdfOutput'] = true;
        }

        if (!empty($this->settings['config']['class'])) {
            $this->setPDF(GeneralUtility::makeInstance(
                $this->settings['config']['class'],
                $this->conversionService->convertSpeakingOrientationToTcpdfOrientation($this->settings['page']['orientation']),
                $this->arguments['unit'],
                $this->settings['page']['format'],
                $this->arguments['unicode'],
                $this->arguments['encoding'],
                false, //deprecated feature
                $this->arguments['pdfa']
            ));
        } else {
            throw new ValidationException('TypoScript value "settings.config.class" must be set! ERROR: 1536837206', 1536837206);
        }

        $this->loadTcpdfLanguageSettings();
        $this->loadCustomFonts();
        $this->loadSourceFile();

        $this->getPDF()->setSRGBmode($this->settings['config']['sRGBMode'] === '1');
        $this->getPDF()->setFontSubsetting($this->settings['config']['fonts']['subset'] === '1');
        $this->getPDF()->setJPEGQuality($this->settings['config']['jpgQuality']);
        $this->getPDF()->SetTitle($this->arguments['title']);
        $this->getPDF()->SetSubject($this->arguments['subject']);
        $this->getPDF()->SetAuthor($this->arguments['author']);
        $this->getPDF()->SetKeywords($this->arguments['keywords']);
        $this->getPDF()->SetCreator($this->arguments['creator']);

        //Disables cache if set so and in frontend mode
        if ($GLOBALS['TSFE'] instanceof TypoScriptFrontendController && $this->settings['config']['disableCache']) {
            $GLOBALS['TSFE']->set_no_cache();
        }

        $this->viewHelperVariableContainer->add('DocumentViewHelper', 'hyphenFile', $this->arguments['hyphenFile']);
        $this->viewHelperVariableContainer->addOrUpdate('DocumentViewHelper', 'defaultHeaderFooterScope', BasePDF::SCOPE_DOCUMENT);
    }

    /**
     * @return string
     *
     * @throws Exception
     */
    public function render()
    {
        $this->renderChildren();

        $outputPath = $this->arguments['outputPath'];

        if (in_array($this->arguments['outputDestination'], $this->tcpdfSaveFileDestinations)) {
            $outputPath = GeneralUtility::getFileAbsFileName($outputPath);
        }

        $output = $this->getPDF()->Output($outputPath, $this->arguments['outputDestination']);
        $this->removePDF(); //allow creation of multiple PDFs per request

        if (in_array($this->arguments['outputDestination'], $this->tcpdfOutputContentDestinations)) {
            //flush and close all outputs in order to prevent TYPO3 from sending other contents and let it finish gracefully
            ob_end_flush();
            ob_flush();
            flush();

            if ($this->settings['config']['exitAfterPdfContentOutput'] === '1') {
                exit;
            }
        }

        return in_array($this->arguments['outputDestination'], $this->tcpdfReturnContentDestinations) ? $output : '';
    }

    /**
     * @return void
     *
     * @throws ValidationException
     */
    protected function loadTcpdfLanguageSettings()
    {
        $extPath = ExtensionManagementUtility::extPath('pdfviewhelpers');
        $languageFilePath = $extPath . 'Resources/Private/PHP/tcpdf/examples/lang/' . $this->arguments['language'] . '.php';

        if (!file_exists($languageFilePath) || !is_readable($languageFilePath)) {
            throw new ValidationException('The provided language file "' . $languageFilePath . '" does not exist or the file is not readable. ERROR: 1536487362', 1536487362);
        }

        require_once($languageFilePath);
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    protected function loadCustomFonts()
    {
        if (empty($this->settings['config']['fonts']['addTTFFont'])) {
            return;
        }

        $outputPath = GeneralUtility::getFileAbsFileName($this->settings['config']['fonts']['outputPath']);
        $outputPath = rtrim($outputPath, '/') . '/';

        if (!is_dir($outputPath)) {
            GeneralUtility::mkdir_deep($outputPath);
        }

        foreach ($this->settings['config']['fonts']['addTTFFont'] as $ttfFontName => $ttfFont) {
            $path = GeneralUtility::getFileAbsFileName($ttfFont['path']);
            $type = isset($ttfFont['type']) ? $ttfFont['type'] : '';
            $fontName = \TCPDF_FONTS::addTTFfont($path, $type, '', 32, $outputPath);

            if ($fontName === false) {
                throw new ValidationException('Font "' . $ttfFontName . '" could not be added. ERROR: 1492808000', 1492808000);
            } else {
                $this->getPDF()->addCustomFontFilePath($fontName, $outputPath . $fontName . '.php');
            }
        }
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    protected function loadSourceFile()
    {
        if (!empty($this->arguments['sourceFile'])) {
            $sourceFilePath = GeneralUtility::getFileAbsFileName($this->arguments['sourceFile']);

            if (!file_exists($sourceFilePath) || !is_readable($sourceFilePath)) {
                throw new ValidationException('The provided source file "' . $sourceFilePath . '" does not exist or the file is not readable. ERROR: 1525452207', 1525452207);
            }

            try {
                $this->getPDF()->setSourceFile($sourceFilePath);
            } catch (PdfParserException $e) {
                throw new Exception('Could not set source file. ' . $e->getMessage() . ' ERROR: 1538067316', 1538067316, $e);
            }
        }
    }
}
