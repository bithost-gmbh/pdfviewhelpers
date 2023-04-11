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
use Bithost\Pdfviewhelpers\HtmlRenderer\HtmlRenderer;
use Bithost\Pdfviewhelpers\HtmlRenderer\HtmlRendererLocator;
use Bithost\Pdfviewhelpers\HtmlRenderer\TcpdfHtmlRenderer;
use Psr\Container\ContainerExceptionInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * HtmlViewHelper
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
 */
class HtmlViewHelper extends AbstractContentElementViewHelper
{
    protected HtmlRendererLocator $htmlRendererLocator;

    public function injectHtmlRendererLocator(HtmlRendererLocator $container): void
    {
        $this->htmlRendererLocator = $container;
    }

    /**
     * @inheritDoc
     */
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('renderer', 'string', 'The identifier of the HTML renderer to be used.', false, $this->settings['html']['renderer']);
        $this->registerArgument('rendererOptions', 'array', 'The options passed to the render HTML method of the renderer.', false, []);
        $this->registerArgument('autoHyphenation', 'boolean', 'If true the text will be hyphenated automatically.', false, (bool) $this->settings['generalText']['autoHyphenation']);
        $this->registerArgument('styleSheet', 'string', 'The path to an external style sheet being used to style this HTML content.', false, $this->settings['html']['styleSheet']);
        $this->registerArgument('padding', 'array', 'The padding of the HTML element as array.', false, null);

        if (strlen((string) $this->settings['html']['autoHyphenation'])) {
            $this->overrideArgument('autoHyphenation', 'boolean', '', false, (bool) $this->settings['html']['autoHyphenation']);
        }
    }

    /**
     * @inheritDoc
     */
    public function initialize()
    {
        parent::initialize();

        if (is_array($this->arguments['padding'])) {
            $this->arguments['padding'] = array_merge($this->settings['html']['padding'] ?? [], $this->arguments['padding']);
        } else {
            $this->arguments['padding'] = $this->settings['html']['padding'];
        }

        $this->arguments['rendererOptions'] = array_merge($this->settings['html']['rendererOptions'] ?? [], $this->arguments['rendererOptions']);

        $this->validationService->validatePadding($this->arguments['padding']);
    }

    /**
     * @throws Exception if an invalid style sheet path is provided
     */
    public function render(): void
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

        $this->getPDF()->setMargins($marginLeft, $initialMargins['top'], $marginRight);

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
        $this->getPDF()->setTextColor($color['R'], $color['G'], $color['B']);
        $this->getPDF()->setFontSize($this->settings['generalText']['fontSize']);
        $this->getPDF()->SetFont($this->settings['generalText']['fontFamily'], $this->conversionService->convertSpeakingFontStyleToTcpdfFontStyle($this->settings['generalText']['fontStyle']));
        $this->getPDF()->setCellPaddings(0, 0, 0, 0); //reset padding to avoid errors on nested tags
        $this->getPDF()->setCellHeightRatio($this->settings['generalText']['lineHeight']);
        $this->getPDF()->setFontSpacing($this->settings['generalText']['characterSpacing']);

        $this->getPDF()->setY($this->arguments['posY'] + $this->arguments['padding']['top']);

        $this->getHtmlRenderer()->renderHtmlToPDF($htmlStyle . $html, $this->getPDF(), $this->settings, $this->arguments);

        $this->getPDF()->setY($this->getPDF()->GetY() + $this->arguments['padding']['bottom']);
        $this->getPDF()->setMargins($initialMargins['left'], $initialMargins['top'], $initialMargins['right']);
    }

    /**
     * @throws ValidationException
     * @throws ContainerExceptionInterface
     */
    protected function getHtmlRenderer(): HtmlRenderer
    {
        if ($this->arguments['renderer']) {
            return $this->htmlRendererLocator->get($this->arguments['renderer']);
        } else {
            return GeneralUtility::makeInstance(TcpdfHtmlRenderer::class);
        }
    }
}
