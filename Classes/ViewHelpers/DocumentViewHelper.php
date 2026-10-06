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
use Bithost\Pdfviewhelpers\Service\OutputService;
use Com\Tecnick\Pdf\Font\Import;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * DocumentViewHelper
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
 */
class DocumentViewHelper extends AbstractPDFViewHelper
{
    /**
     * Output destinations that send http headers and echo the pdf
     */
    protected array $outputContentDestinations = ['I', 'D', 'FI', 'FD'];

    /**
     * Output destinations that save the pdf to the filesystem
     */
    protected array $saveFileDestinations = ['F', 'FI', 'FD'];

    protected OutputService $outputService;

    public function injectOutputService(OutputService $outputService): void
    {
        $this->outputService = $outputService;
    }

    /**
     * @inheritDoc
     */
    public function initializeArguments(): void
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
        $this->registerArgument('unit', 'string', 'The default unit of measure: pt, mm, cm or in.', false, $this->settings['document']['unit']);
        $this->registerArgument('pdfa', 'boolean', 'If true PDF/A-1b mode is enabled.', false, (bool) $this->settings['document']['pdfa']);
        $this->registerArgument('pdfua', 'boolean', 'If true PDF/UA-1 mode is enabled.', false, (bool) $this->settings['document']['pdfua']);
        $this->registerArgument('language', 'string', 'The language of the document.', false, $this->settings['document']['language']);
        $this->registerArgument('hyphenFile', 'string', 'The hyphen file to be used for the automatic hyphenation.', false, $this->settings['document']['hyphenFile']);
    }

    /**
     * @inheritDoc
     *
     * @throws Exception
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->arguments['outputDestination'] = $this->conversionService->convertSpeakingOutputDestinationToTcpdfOutputDestination($this->arguments['outputDestination']);

        if (in_array($this->arguments['outputDestination'], $this->outputContentDestinations)) {
            $this->outputService->setIsOutputDestinationOut(true);
        }

        if (empty($this->settings['config']['class'])) {
            throw new ValidationException('TypoScript value "settings.config.class" must be set! ERROR: 1536837206', 1536837206);
        }

        if ($this->arguments['pdfa'] && $this->arguments['pdfua']) {
            throw new ValidationException('PDF/A and PDF/UA mode can not be enabled at the same time. ERROR: 1759737604', 1759737604);
        }

        if ($this->arguments['pdfua'] && empty($this->arguments['title'])) {
            throw new ValidationException('PDF/UA mode requires a document title. ERROR: 1759737606', 1759737606);
        }

        $this->setPDF(GeneralUtility::makeInstance(
            $this->settings['config']['class'],
            $this->arguments['unit'],
            $this->settings['config']['fonts']['subset'] === '1',
            $this->arguments['pdfa'] ? 'pdfa1b' : ($this->arguments['pdfua'] ? 'pdfua1' : ''),
        ));

        $this->loadLanguageSettings();
        $this->loadCustomFonts();
        $this->loadSourceFile();

        $this->getPDF()->setSRGB($this->settings['config']['sRGBMode'] === '1');
        $this->getPDF()->setJpegQuality((int) $this->settings['config']['jpgQuality']);
        $this->getPDF()->setTitle($this->arguments['title']);
        $this->getPDF()->setSubject($this->arguments['subject']);
        $this->getPDF()->setAuthor($this->arguments['author']);
        $this->getPDF()->setKeywords($this->arguments['keywords']);
        $this->getPDF()->setCreator($this->arguments['creator']);

        $this->outputService->setDisableCache($this->settings['config']['disableCache'] === '1');

        $this->viewHelperVariableContainer->add('DocumentViewHelper', 'hyphenFile', $this->arguments['hyphenFile']);
        $this->viewHelperVariableContainer->addOrUpdate('DocumentViewHelper', 'defaultHeaderFooterScope', BasePDF::SCOPE_DOCUMENT);
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $this->renderChildren();

        $pdf = $this->getPDF();
        $output = $pdf->getOutPDFString();
        $destination = $this->arguments['outputDestination'];
        $this->removePDF(); //allow creation of multiple PDFs per request

        $pdf->setPDFFilename(basename($this->arguments['outputPath']));

        if (in_array($destination, $this->saveFileDestinations)) {
            $outputPath = GeneralUtility::getFileAbsFileName($this->arguments['outputPath']);

            if ($outputPath === '' || file_put_contents($outputPath, $output) === false) {
                throw new Exception('The PDF could not be saved to "' . $this->arguments['outputPath'] . '". ERROR: 1759737605', 1759737605);
            }
        }

        if (in_array($destination, ['I', 'FI'])) {
            $pdf->renderPDF($output);
        } elseif (in_array($destination, ['D', 'FD'])) {
            $pdf->downloadPDF($output);
        }

        if (in_array($destination, $this->outputContentDestinations)) {
            //flush and close all outputs in order to prevent TYPO3 from sending other contents and let it finish gracefully
            ob_end_flush();
            ob_flush();
            flush();

            if ($this->settings['config']['exitAfterPdfContentOutput'] === '1') {
                exit;
            }
        }

        return match ($destination) {
            'S' => $output,
            'E' => $pdf->getMIMEAttachmentPDF($output),
            default => '',
        };
    }

    /**
     * @throws ValidationException
     */
    protected function loadLanguageSettings(): void
    {
        $languageFilePath = GeneralUtility::getFileAbsFileName('EXT:pdfviewhelpers/Resources/Private/Language/' . $this->arguments['language'] . '.php');

        if (!file_exists($languageFilePath) || !is_readable($languageFilePath)) {
            throw new ValidationException('The provided language file "' . $languageFilePath . '" does not exist or the file is not readable. ERROR: 1536487362', 1536487362);
        }

        $this->getPDF()->setLanguageArray(require($languageFilePath));
    }

    /**
     * Converts the configured TTF fonts once into the output path and registers them
     *
     * @throws ValidationException
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
            // The font name is derived from the file name, the same way tc-lib-pdf does it
            $fontName = str_replace(['bold', 'oblique', 'italic', 'regular'], ['b', 'i', 'i', ''], preg_replace('/[^a-z0-9_]/', '', strtolower(pathinfo($path, PATHINFO_FILENAME))));
            $fontFile = $outputPath . $fontName . '.json';

            if (!is_file($fontFile)) {
                try {
                    new Import($path, $outputPath, $ttfFont['type'] ?? '', $ttfFont['enc'] ?? '', (int) ($ttfFont['flags'] ?? 32));
                } catch (\Exception $e) {
                    // The font might have been imported by a concurrent request in the meantime
                    if (!is_file($fontFile)) {
                        throw new ValidationException('Font "' . $ttfFontName . '" could not be added. ' . $e->getMessage() . ' ERROR: 1492808000', 1492808000, $e);
                    }
                }
            }

            $this->getPDF()->addCustomFontFile($fontName, $fontFile);
        }
    }

    /**
     * @throws ValidationException
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
            } catch (\Exception $e) {
                throw new Exception('Could not set source file. ' . $e->getMessage() . ' ERROR: 1538067316', 1538067316, $e);
            }
        }
    }
}
