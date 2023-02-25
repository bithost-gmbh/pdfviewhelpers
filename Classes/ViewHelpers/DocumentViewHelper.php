<?php

declare(strict_types=1);

namespace Bithost\Pdfviewhelpers\ViewHelpers;

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
use Bithost\Pdfviewhelpers\Model\BasePDF;
use setasign\Fpdi\PdfParser\PdfParserException;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * DocumentViewHelper
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
 */
class DocumentViewHelper extends AbstractPDFViewHelper
{
    /**
     * TCPDF output destinations that send http headers and echo the pdf
     */
    protected array $tcpdfOutputContentDestinations = ['I', 'D', 'FI', 'FD'];

    /**
     * TCPDF output destinations that save the pdf to the filesystem
     */
    protected array $tcpdfSaveFileDestinations = ['F', 'FI', 'FD'];

    /**
     * TCPDF output destinations that return the pdf as string
     */
    protected array $tcpdfReturnContentDestinations = ['S', 'E'];

    /**
     * @inheritDoc
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
        $this->registerArgument('unicode', 'boolean', 'If true unicode is used.', false, (bool) $this->settings['document']['unicode']);
        $this->registerArgument('encoding', 'string', 'The encoding of the document.', false, $this->settings['document']['encoding']);
        $this->registerArgument('pdfa', 'boolean', 'If true PDF/A mode is enabled.', false, (bool) $this->settings['document']['pdfa']);
        $this->registerArgument('pdfua', 'boolean', 'If true PDF/UA-1 mode is enabled.', false, (bool) $this->settings['document']['pdfua']);
        $this->registerArgument('language', 'string', 'The language of the document.', false, $this->settings['document']['language']);
        $this->registerArgument('hyphenFile', 'string', 'The hyphen file to be used for the automatic hyphenation.', false, $this->settings['document']['hyphenFile']);
    }

    /**
     * @inheritDoc
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
        $this->loadPDFUASupport();

        $this->getPDF()->setSRGBmode($this->settings['config']['sRGBMode'] === '1');
        $this->getPDF()->setFontSubsetting($this->settings['config']['fonts']['subset'] === '1');
        $this->getPDF()->setJPEGQuality($this->settings['config']['jpgQuality']);
        $this->getPDF()->setTitle($this->arguments['title']);
        $this->getPDF()->setSubject($this->arguments['subject']);
        $this->getPDF()->setAuthor($this->arguments['author']);
        $this->getPDF()->setKeywords($this->arguments['keywords']);
        $this->getPDF()->setCreator($this->arguments['creator']);
        $this->getPDF()->disableTcpdfLink(); // Part for increasing accessibility (ua-1)

        //Disables cache if set so and in frontend mode
        if (isset($GLOBALS['TSFE']) && $GLOBALS['TSFE'] instanceof TypoScriptFrontendController && $this->settings['config']['disableCache']) {
            $GLOBALS['TSFE']->set_no_cache('EXT:pdfviewhelpers force disabled caching, see plugin.tx_pdfviewhelpers.settings.config.disableCache', true);
        }

        $this->viewHelperVariableContainer->add('DocumentViewHelper', 'hyphenFile', $this->arguments['hyphenFile']);
        $this->viewHelperVariableContainer->addOrUpdate('DocumentViewHelper', 'defaultHeaderFooterScope', BasePDF::SCOPE_DOCUMENT);
    }

    /**
     * @throws Exception
     */
    public function render(): string
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
     * @throws ValidationException
     */
    protected function loadTcpdfLanguageSettings(): void
    {
        $extPath = ExtensionManagementUtility::extPath('pdfviewhelpers');
        $languageFilePath = $extPath . 'Resources/Private/PHP/tcpdf/examples/lang/' . $this->arguments['language'] . '.php';

        if (!file_exists($languageFilePath) || !is_readable($languageFilePath)) {
            throw new ValidationException('The provided language file "' . $languageFilePath . '" does not exist or the file is not readable. ERROR: 1536487362', 1536487362);
        }

        require_once($languageFilePath);
    }

    /**
     * @throws Exception
     */
    protected function loadCustomFonts(): void
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
            $enc = isset($ttfFont['enc']) ? $ttfFont['enc'] : '';
            $flags = isset($ttfFont['flags']) ? (int) $ttfFont['flags'] : 32;
            $fontName = \TCPDF_FONTS::addTTFfont($path, $type, $enc, $flags, $outputPath);

            if ($fontName === false) {
                throw new ValidationException('Font "' . $ttfFontName . '" could not be added. ERROR: 1492808000', 1492808000);
            } else {
                $this->getPDF()->addCustomFontFilePath($fontName, $outputPath . $fontName . '.php');
            }
        }
    }

    /**
     * @throws Exception
     */
    protected function loadSourceFile(): void
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

    /**
     * Experimental PDF/UA support, also see https://github.com/bithost-gmbh/pdfviewhelpers/issues/204
     *
     * @throws Exception
     */
    public function loadPDFUASupport(): void
    {
        if ($this->arguments['pdfua']) {
            $viewerPreferences = [
                'DisplayDocTitle' => true,
            ];

            $this->getPDF()->setViewerPreferences($viewerPreferences);

            // See https://taggedpdf.com/508-pdf-help-center/pdfua-identifier-missing/
            $this->getPDF()->setExtraXMPRDF('
    <rdf:Description rdf:about=""
	    xmlns:pdfaExtension="http://www.aiim.org/pdfa/ns/extension/"
	    xmlns:pdfaSchema="http://www.aiim.org/pdfa/ns/schema#"
	    xmlns:pdfaProperty="http://www.aiim.org/pdfa/ns/property#"
	    xmlns:pdfuaid="http://www.aiim.org/pdfua/ns/id/">
		<pdfaExtension:schemas>
			<rdf:Bag>
				<rdf:li rdf:parseType="Resource">
					<pdfaSchema:schema>PDF/UA Universal Accessibility Schema</pdfaSchema:schema>
					<pdfaSchema:namespaceURI>http://www.aiim.org/pdfua/ns/id/</pdfaSchema:namespaceURI>
					<pdfaSchema:prefix>pdfuaid</pdfaSchema:prefix>
					<pdfaSchema:property>
						<rdf:Seq>
							<rdf:li rdf:parseType="Resource">
								<pdfaProperty:name>part</pdfaProperty:name>
								<pdfaProperty:valueType>Integer</pdfaProperty:valueType>
								<pdfaProperty:category>internal</pdfaProperty:category>
								<pdfaProperty:description>Indicates, which part of ISO 14289 standard is followed</pdfaProperty:description>
							</rdf:li>
						</rdf:Seq>
					</pdfaSchema:property>
				</rdf:li>
			</rdf:Bag>
		</pdfaExtension:schemas>
		<pdfuaid:part>1</pdfuaid:part>
	</rdf:Description>
	        ');
        }
    }
}
