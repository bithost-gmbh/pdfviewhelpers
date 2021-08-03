<?php

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
use Bithost\Pdfviewhelpers\Model\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Output\Destination;
use None\Sense;
use setasign\Fpdi\PdfParser\StreamReader;
use setasign\Fpdi\PdfReader\PageBoundaries;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * HtmlViewHelper
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
 */
class HtmlViewHelper extends AbstractContentElementViewHelper
{
    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('autoHyphenation', 'boolean', 'If true the text will be hyphenated automatically.', false, (boolean) $this->settings['generalText']['autoHyphenation']);
        $this->registerArgument('styleSheet', 'string', 'The path to an external style sheet being used to style this HTML content.', false, $this->settings['html']['styleSheet']);
        $this->registerArgument('padding', 'array', 'The padding of the HTML element as array.', false, null);
        $this->registerArgument('renderer', 'string', 'The renderer used to render HTML (tcpdf, mpdf or chrome).', false, $this->settings['html']['renderer']);
        $this->registerArgument('rendererOptions', 'array', 'Individual settings for the chosen renderer, see the documentation for more information.', false, null);

        if (strlen($this->settings['html']['autoHyphenation'])) {
            $this->overrideArgument('autoHyphenation', 'boolean', '', false, (boolean) $this->settings['html']['autoHyphenation']);
        }
    }

    /**
     * @throws Exception
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        if (is_array($this->arguments['padding'])) {
            $this->arguments['padding'] = array_merge($this->settings['html']['padding'], $this->arguments['padding']);
        } else {
            $this->arguments['padding'] = $this->settings['html']['padding'];
        }

        $this->validationService->validatePadding($this->arguments['padding']);

        if (is_array($this->arguments['rendererOptions'])) {
            $this->arguments['rendererOptions'] = array_merge($this->settings['html']['rendererOptions'], $this->arguments['rendererOptions']);
        } else {
            $this->arguments['rendererOptions'] = $this->settings['html']['rendererOptions'];
        }
    }

    /**
     * @return void
     *
     * @throws Exception if an invalid style sheet path is provided
     */
    public function render()
    {
        $html = $this->renderChildren();
        $htmlStyle = '';
        $color = $this->conversionService->convertHexToRGB($this->settings['generalText']['color']);

        $this->initializeMultiColumnSupport();

        $initialMargins = $this->getPDF()->getMargins();
        $marginLeft = $this->arguments['posX'] + $this->arguments['padding']['left'];

        if (is_null($this->arguments['width'])) {
            $marginRight = $initialMargins['right'] + $this->arguments['padding']['right'];
        } else {
            $marginRight = $this->getPDF()->getPageWidth() - $marginLeft - $this->arguments['width'] + $this->arguments['padding']['right'];
        }

        $this->getPDF()->SetMargins($marginLeft, $initialMargins['top'], $marginRight);

        if (!empty($this->arguments['styleSheet'])) {
            $styleSheetFile = $this->conversionService->convertFileSrcToFileObject($this->arguments['styleSheet']);

            $htmlStyle = '<style>' . $styleSheetFile->getContents() . '</style>';
        }

        if ($this->arguments['autoHyphenation']) {
            $html = $this->hyphenationService->hyphenateText(
                $html,
                $this->hyphenationService->getHyphenFilePath($this->getHyphenFileName())
            );
        }

        //reset settings to generalText
        $this->getPDF()->SetTextColor($color['R'], $color['G'], $color['B']);
        $this->getPDF()->SetFontSize($this->settings['generalText']['fontSize']);
        $this->getPDF()->SetFont($this->settings['generalText']['fontFamily'], $this->conversionService->convertSpeakingFontStyleToTcpdfFontStyle($this->settings['generalText']['fontStyle']));
        $this->getPDF()->setCellPaddings(0, 0, 0, 0); //reset padding to avoid errors on nested tags
        $this->getPDF()->setCellHeightRatio($this->settings['generalText']['lineHeight']);
        $this->getPDF()->setFontSpacing($this->settings['generalText']['characterSpacing']);

        $this->getPDF()->SetY($this->arguments['posY'] + $this->arguments['padding']['top']);

        $this->renderHtml($this->arguments['renderer'], $this->arguments['rendererOptions'], $htmlStyle . $html);

        $this->getPDF()->SetY($this->getPDF()->GetY() + $this->arguments['padding']['bottom']);
        $this->getPDF()->SetMargins($initialMargins['left'], $initialMargins['top'], $initialMargins['right']);
    }

    /**
     * @param $renderer
     * @param array $rendererOptions
     * @param $html
     *
     * @throws Exception
     */
    protected function renderHtml($renderer, array $rendererOptions, $html)
    {
        switch (strtolower($renderer)) {
            case 'mpdf':
                $this->renderHtmlMpdf($rendererOptions, $html);
                break;
            case 'chrome':
                $this->renderHtmlChrome($rendererOptions, $html);
                break;
            case 'tcpdf':
            default:
                $this->renderHtmlTcpdf($rendererOptions, $html);
        }
    }

    /**
     * @param array $rendererOptions
     * @param $html
     *
     * @throws Exception
     */
    protected function renderHtmlTcpdf(array $rendererOptions, $html)
    {
        $this->getPDF()->writeHTML($html, true, false, true, false, '');
    }

    /**
     * @param array $rendererOptions
     * @param $html
     *
     * @throws Exception
     */
    protected function renderHtmlMpdf(array $rendererOptions, $html)
    {
        if (!class_exists('\Mpdf\Mpdf')) {
            throw new Exception('The mPDF HTML renderer requires mPDF to be installed in the system. Install EXT:mpdf or just the mPDF library using "composer require mpdf/mpdf". ERROR: 1599545679 ', 1599545679);
        }

        $defaultConfiguration = [
            'format' => $this->getPDF()->getCurrentPageFormat(),
            'orientation' => $this->getPDF()->getCurrentPageOrientation(),
            'default_font_size' => $this->getPDF()->getFontSizePt(),
            'default_font' => $this->getPDF()->getFontFamily(),
            'normalLineheight' => $this->getPDF()->getCellHeightRatio(),
            'PDFA' => (boolean) $this->settings['document']['pdfa'],
            'tempDir' => $this->getTempPath('mpdf/tmp'),
        ];

        //$rendererOptions = array_merge($defaultConfiguration, $rendererOptions);

        if (isset($rendererOptions['fontDir'])) {
            // Add default fonts from mPDF, see https://mpdf.github.io/fonts-languages/fonts-in-mpdf-7-x.html
            $defaultFontDirs = (new ConfigVariables())->getDefaults()['fontDir'];

            foreach ($rendererOptions['fontDir'] as $key => $path) {
                $rendererOptions['fontDir'][$key] =  GeneralUtility::getFileAbsFileName($path);
            }

            $rendererOptions['fontDir'] = array_merge($defaultFontDirs, $rendererOptions['fontDir']);
        }

        if (isset($rendererOptions['fontdata'])) {
            // Add default fonts from mPDF, see https://mpdf.github.io/fonts-languages/fonts-in-mpdf-7-x.html
            $defaultFontData = (new FontVariables())->getDefaults()['fontdata'];
            $rendererOptions['fontdata'] = $defaultFontData + $rendererOptions['fontdata'];
        }

        try {
            $mPDF = new Mpdf($rendererOptions);

            $mPDF->setBasePdf($this->getPDF());
            $mPDF->WriteHTML($html);
            $pdf = $mPDF->Output('', Destination::STRING_RETURN);
        } catch (\Exception $e) {
            throw new Exception('Could not write HTML or output to PDF in mPDF ViewHelper. ' . $e->getMessage() . ' ERROR: 1593934910 ', 1593934910, $e);
        }

        $pdfStreamReader = StreamReader::createByString($pdf);
        $numberOfPages = $this->getPDF()->setSourceFile($pdfStreamReader);

        try {
            for ($i=0; $i<$numberOfPages; $i++) {
                $pageNumber = $i + 1;
                $pageTemplate = $this->getPDF()->importPage($pageNumber);
                $this->getPDF()->useTemplate($pageTemplate);

                if ($i + 1 < $numberOfPages) {
                    $this->getPDF()->AddPage();
                }
            }
        } catch (\Exception $e) {
            throw new Exception('Could not import page in mPDF ViewHelper. ' . $e->getMessage() . ' ERROR: 1593934801 ', 1593934801, $e);
        }

        $this->getPDF()->SetY($mPDF->y);
    }

    /**
     * @param array $rendererOptions
     * @param $html
     *
     * @throws Exception
     */
    protected function renderHtmlChrome(array $rendererOptions, $html)
    {
        $unit = $this->getPDF()->getPdfUnit();
        $margins = $this->getPDF()->getMargins();
        $marginTop = $margins['top'];
        $marginRight = $margins['right'];
        $marginBottom = $margins['bottom'];
        $marginLeft = $margins['left'];
        $style = "
        <style>
        @media print {
            @page { margin: $marginTop$unit $marginRight$unit $marginBottom$unit $marginLeft$unit; }
            /*body { margin: $marginTop$unit $marginRight$unit $marginBottom$unit $marginLeft$unit; }*/
        }
        </style>
        ";
        $url = 'data:text/html,' . rawurlencode($style . $html);
        $name = 'rand_string.pdf';
        $outputDirectory = GeneralUtility::getFileAbsFileName('typo3temp/pdfviewhelpers/chrome/');
        $outFile= rtrim($outputDirectory, '/') . '/' . $name;
        $binaryLocation = '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome';
        $command = sprintf(
            '%s --headless --disable-gpu --no-margins --print-to-pdf=%s %s 2>&1',
            escapeshellarg($binaryLocation),
            escapeshellarg($outFile),
            escapeshellarg($url)
        );


        if (!is_dir($outputDirectory)) {
            GeneralUtility::mkdir_deep($outputDirectory);
        }

        exec($command);

        $numberOfPages = $this->getPDF()->setSourceFile($outFile);

        try {
            for ($i=0; $i<$numberOfPages; $i++) {
                $pageNumber = $i + 1;
                $pageTemplate = $this->getPDF()->importPage($pageNumber, PageBoundaries::MEDIA_BOX);
                $this->getPDF()->useTemplate($pageTemplate, 0, 0);

                if ($i + 1 < $numberOfPages) {
                    $this->getPDF()->AddPage();
                }
            }
        } catch (\Exception $e) {
            throw new Exception('Could not import page in ChromeViewHelper. ' . $e->getMessage() . ' ERROR: 1593934801 ', 1593934801, $e);
        }
    }
}
