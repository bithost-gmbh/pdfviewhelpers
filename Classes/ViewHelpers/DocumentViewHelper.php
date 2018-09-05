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
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use FPDI;
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
        $this->registerArgument('title', 'string', '', false, $this->settings['document']['title']);
        $this->registerArgument('subject', 'string', '', false, $this->settings['document']['subject']);
        $this->registerArgument('author', 'string', '', false, $this->settings['document']['author']);
        $this->registerArgument('keywords', 'string', '', false, $this->settings['document']['keywords']);
        $this->registerArgument('creator', 'string', '', false, $this->settings['document']['creator']);
        $this->registerArgument('outputDestination', 'string', '', false, $this->settings['document']['outputDestination']);
        $this->registerArgument('outputPath', 'string', '', false, $this->settings['document']['outputPath']);
        $this->registerArgument('sourceFile', 'string', '', false, $this->settings['document']['sourceFile']);
    }

    /**
     * @return void
     */
    public function initialize()
    {
        if (isset($GLOBALS['TSFE']->applicationData) && in_array($this->arguments['outputDestination'], $this->tcpdfOutputContentDestinations)) {
            $GLOBALS['TSFE']->applicationData['tx_pdfviewhelpers']['pdfOutput'] = true;
        }

        $extPath = ExtensionManagementUtility::extPath('pdfviewhelpers');
        $pdfClassName = empty($this->settings['config']['class']) ? 'TCPDF' : $this->settings['config']['class'];

        //Autoload class TCPDF in order for fpdi_bridge to be able to correctly determine its parent class
        class_exists('TCPDF', true);

        //Load TCPDF language settings
        require_once($extPath . 'Resources/Private/PHP/tcpdf/examples/lang/' . $this->settings['config']['language'] . '.php');

        //Set PDF and document properties
        $this->setPDF(GeneralUtility::makeInstance($pdfClassName));

        $this->getPDF()->setSRGBmode($this->settings['config']['sRGBMode'] === '1');
        $this->getPDF()->setFontSubsetting($this->settings['config']['fonts']['subset'] === '1');
        $this->getPDF()->setJPEGQuality($this->settings['config']['jpgQuality']);
        $this->getPDF()->SetTitle($this->arguments['title']);
        $this->getPDF()->SetSubject($this->arguments['subject']);
        $this->getPDF()->SetAuthor($this->arguments['author']);
        $this->getPDF()->SetKeywords($this->arguments['keywords']);
        $this->getPDF()->SetCreator($this->arguments['creator']);

        //Add custom fonts
        foreach ($this->settings['config']['fonts']['addTTFFont'] as $ttfFontName => $ttfFont) {
            $path = GeneralUtility::getFileAbsFileName($ttfFont['path']);
            $type = isset($ttfFont['type']) ? $ttfFont['type'] : '';

            $fontName = \TCPDF_FONTS::addTTFfont($path, $type);

            if ($fontName === false) {
                throw new Exception('Font "' . $ttfFontName . '" could not be added. ERROR: 1492808000', 1492808000);
            }
        }

        //Add FPDI sourceFile if given
        if (!empty($this->arguments['sourceFile'])) {
            $sourceFilePath = GeneralUtility::getFileAbsFileName($this->arguments['sourceFile']);

            if (!file_exists($sourceFilePath) || !is_readable($sourceFilePath)) {
                throw new ValidationException('The provided source file "' . $sourceFilePath . '" does not exist or the file is not readable. ERROR: 1525452207', 1525452207);
            }

            if ($this->getPDF() instanceof FPDI) {
                $this->getPDF()->setSourceFile($sourceFilePath);
            } else {
                throw new Exception('PDF object must be instance of FPDI to support option "sourceFile". ERROR: 1474144733', 1474144733);
            }
        }

        //Disables cache if set so and in frontend mode
        if ($GLOBALS['TSFE'] instanceof TypoScriptFrontendController && $this->settings['config']['disableCache']) {
            $GLOBALS['TSFE']->set_no_cache();
        }

        $this->viewHelperVariableContainer->addOrUpdate('DocumentViewHelper', 'defaultHeaderFooterScope', BasePDF::SCOPE_DOCUMENT);
    }

    /**
     * @return string
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
        }

        return in_array($this->arguments['outputDestination'], $this->tcpdfReturnContentDestinations) ? $output : '';
    }
}
