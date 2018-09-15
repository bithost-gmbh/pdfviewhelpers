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

        if (strlen($this->settings['html']['autoHyphenation'])) {
            $this->overrideArgument('autoHyphenation', 'boolean', '', false, (boolean) $this->settings['html']['autoHyphenation']);
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
        $padding = $this->settings['generalText']['padding'];

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
        $this->getPDF()->setCellPaddings($padding['left'], $padding['top'], $padding['right'], $padding['bottom']);

        $this->getPDF()->writeHTML($htmlStyle . $html, true, false, true, false, '');
    }
}
