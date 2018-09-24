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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * HtmlViewHelper
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Marin <esteban.marin@bithost.ch>
 */
class HtmlViewHelper extends AbstractContentElementViewHelper
{
    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('autoHyphenation', 'boolean', '', false, (boolean) $this->settings['generalText']['autoHyphenation']);
        $this->registerArgument('styleSheet', 'string', '', false, $this->settings['html']['styleSheet']);
        $this->registerArgument('padding', 'array', '', false, null);

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

        $this->getPDF()->writeHTML($htmlStyle . $html, true, false, true, false, '');

        $this->getPDF()->SetY($this->getPDF()->GetY() + $this->arguments['padding']['bottom']);
        $this->getPDF()->SetMargins($initialMargins['left'], $initialMargins['top'], $initialMargins['right']);
    }
}
