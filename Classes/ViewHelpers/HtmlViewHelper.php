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

use Bithost\Pdfviewhelpers\Exception\ValidationException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use UnexpectedValueException;

/**
 * HtmlViewHelper
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
 */
class HtmlViewHelper extends AbstractContentElementViewHelper
{
    protected $escapeChildren = true;

    /**
     * @inheritDoc
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();

        $this->registerArgument('autoHyphenation', 'boolean', 'If true the text will be hyphenated automatically.', false, (bool) $this->settings['generalText']['autoHyphenation']);
        $this->registerArgument('styleSheet', 'string', 'The path to an external style sheet being used to style this HTML content.', false, $this->settings['html']['styleSheet']);
        $this->registerArgument('padding', 'array', 'The padding of the HTML element as array.', false, null);
        $this->registerArgument('listIndentWidth', 'float', 'Set custom width for list indentation.', false, $this->settings['html']['listIndentWidth']);

        if (strlen((string) $this->settings['html']['autoHyphenation'])) {
            $this->registerArgument('autoHyphenation', 'boolean', '', false, (bool) $this->settings['html']['autoHyphenation']);
        }
    }

    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();

        if (is_array($this->arguments['padding'])) {
            $this->arguments['padding'] = array_merge($this->settings['html']['padding'] ?? [], $this->arguments['padding']);
        } else {
            $this->arguments['padding'] = $this->settings['html']['padding'];
        }

        $this->validationService->validatePadding($this->arguments['padding']);
    }

    /**
     * @inheritDoc
     *
     * @throws ValidationException
     */
    public function render(): void
    {
        $html = $this->renderChildren();
        $pdf = $this->getPDF();
        $generalText = $this->settings['generalText'];

        $this->initializeMultiColumnSupport();

        $posX = $this->arguments['posX'] + $this->arguments['padding']['left'];

        if (is_null($this->arguments['width'])) {
            $width = $pdf->getPageWidth() - $pdf->getMargins()['right'] - $this->arguments['padding']['right'] - $posX;
        } else {
            $width = $this->arguments['width'] - $this->arguments['padding']['left'] - $this->arguments['padding']['right'];
        }

        // The HTML is rendered using the general text settings, the style sheet can overwrite them
        $this->validationService->validateColor($generalText['color']);
        $styles = 'body{color:' . $generalText['color'] . ';line-height:' . (float) $generalText['lineHeight'] . ';}';

        if (!empty($this->arguments['listIndentWidth'])) {
            $styles .= 'ul,ol{padding-left:' . (float) $this->arguments['listIndentWidth'] . $pdf->getUnit() . ';}';
        }

        if (!empty($this->arguments['styleSheet'])) {
            $styles .= $this->getStyleSheet();
        }

        $textStyle = $pdf->getTextStyle();
        $pdf->setFont($generalText['fontFamily'], $this->conversionService->convertSpeakingFontStyleToTcpdfFontStyle($generalText['fontStyle']), (float) $generalText['fontSize']);
        $pdf->setCharacterSpacing((float) $generalText['characterSpacing']);
        $pdf->setHyphenation($this->arguments['autoHyphenation'] ? $this->getHyphenFilePath() : '');

        $pdf->writeHtml('<style>' . $styles . '</style>' . $html, $posX, $this->arguments['posY'] + $this->arguments['padding']['top'], $width);

        $pdf->setHyphenation('');
        $pdf->setTextStyle($textStyle);
        $pdf->setY($pdf->getY() + $this->arguments['padding']['bottom']);
    }

    /**
     * @throws ValidationException
     */
    protected function getStyleSheet(): string
    {
        $styleSheetFile = $this->conversionService->convertFileSrcToFileObject($this->arguments['styleSheet']);

        if ($styleSheetFile) {
            return $styleSheetFile->getContents();
        }

        $styleSheetFileName = GeneralUtility::getFileAbsFileName($this->arguments['styleSheet']);

        if ($styleSheetFileName && file_exists($styleSheetFileName)) {
            return file_get_contents($styleSheetFileName);
        }

        throw new UnexpectedValueException('Provided CSS file can\'t be loaded: ' . $this->arguments['styleSheet'], 1729853324);
    }
}
