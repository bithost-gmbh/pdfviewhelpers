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

/**
 * AbstractTextViewHelper
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Marin <esteban.marin@bithost.ch>
 */
abstract class AbstractTextViewHelper extends AbstractContentElementViewHelper
{
    /**
     * @var array
     */
    protected $mergeProperties = [
        'trim',
        'removeDoubleWhitespace',
        'color',
        'fontFamily',
        'fontSize',
        'fontStyle',
        'padding',
        'text',
        'alignment',
        'paragraphSpacing',
        'autoHyphenation',
        'lineHeight',
        'characterSpacing',
    ];

    /**
     * @return string
     */
    abstract protected function getSettingsKey();

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('trim', 'boolean', '', false, null);
        $this->registerArgument('removeDoubleWhitespace', 'boolean', '', false, null);
        $this->registerArgument('color', 'string', '', false, null);
        $this->registerArgument('fontFamily', 'string', '', false, null);
        $this->registerArgument('fontSize', 'integer', '', false, null);
        $this->registerArgument('fontStyle', 'string', '', false, null);
        $this->registerArgument('lineHeight', 'float', '', false, null);
        $this->registerArgument('characterSpacing', 'float', '', false, null);
        $this->registerArgument('padding', 'array', '', false, []);
        $this->registerArgument('text', 'string', '', false, null);
        $this->registerArgument('alignment', 'string', '', false, null);
        $this->registerArgument('paragraphSpacing', 'float', '', false, null);
        $this->registerArgument('autoHyphenation', 'boolean', '', false, null);
        $this->registerArgument('type', 'string', '', false, null);
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    public function initialize()
    {
        parent::initialize();

        $this->mergeSettingsAndArguments();

        if (empty($this->arguments['text'])) {
            $this->arguments['text'] = $this->renderChildren();
        }

        if ($this->arguments['trim']) {
            $this->arguments['text'] = trim($this->arguments['text']);
        }

        if ($this->arguments['removeDoubleWhitespace']) {
            $this->arguments['text'] = preg_replace('/[ \t]+/', ' ', $this->arguments['text']);
        }

        if ($this->arguments['autoHyphenation']) {
            $this->arguments['text'] = $this->hyphenationService->hyphenateText(
                $this->arguments['text'],
                $this->hyphenationService->getHyphenFilePath($this->getHyphenFileName())
            );
        }

        if ($this->validationService->validateColor($this->arguments['color'])) {
            $this->arguments['color'] = $this->conversionService->convertHexToRGB($this->arguments['color']);
            $this->getPDF()->SetTextColor($this->arguments['color']['R'], $this->arguments['color']['G'], $this->arguments['color']['B']);
        }

        if ($this->validationService->validateFontSize($this->arguments['fontSize'])) {
            $this->getPDF()->SetFontSize($this->arguments['fontSize']);
        }

        if ($this->validationService->validateFontFamily($this->arguments['fontFamily'])) {
            $this->getPDF()->SetFont($this->arguments['fontFamily'], $this->conversionService->convertSpeakingFontStyleToTcpdfFontStyle($this->arguments['fontStyle']));
        }

        if ($this->validationService->validateLineHeight($this->arguments['lineHeight'])) {
            $this->getPDF()->setCellHeightRatio($this->arguments['lineHeight']);
        }

        if ($this->validationService->validateCharacterSpacing($this->arguments['characterSpacing'])) {
            $this->getPDF()->setFontSpacing($this->arguments['characterSpacing']);
        }

        if ($this->validationService->validatePadding($this->arguments['padding'])) {
            $this->getPDF()->setCellPaddings(
                $this->arguments['padding']['left'],
                0,
                $this->arguments['padding']['right'],
                0
            );
        }
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    public function render()
    {
        $this->initializeMultiColumnSupport();

        $paragraphs = explode("\n", str_replace("\r\n", "\n", $this->arguments['text']));
        $posY = $this->arguments['posY'] + $this->arguments['padding']['top'];

        foreach ($paragraphs as $paragraph) {
            if ($this->arguments['trim']) {
                $paragraph = trim($paragraph);
            }

            $this->getPDF()->MultiCell($this->arguments['width'], $this->arguments['height'] / count($paragraphs), $paragraph, 0, $this->conversionService->convertSpeakingAlignmentToTcpdfAlignment($this->arguments['alignment']), false, 1, $this->arguments['posX'], $posY, true, 0, false, true, 0, 'T', false);

            if ($this->validationService->validateParagraphSpacing($this->arguments['paragraphSpacing']) && $this->arguments['paragraphSpacing'] > 0) {
                $this->getPDF()->Ln((float)$this->arguments['paragraphSpacing'], false);
            }

            $posY = $this->getPDF()->GetY();
        }

        $this->getPDF()->SetY($this->getPDF()->GetY() + $this->arguments['padding']['bottom']);
    }

    /**
     * @param array $default
     * @param array $overwrite
     *
     * @return array
     */
    protected function mergeSettingsArrays(array $default, array $overwrite)
    {
        $mergedArray = $default;

        foreach ($this->mergeProperties as $mergeProperty) {
            if (isset($overwrite[$mergeProperty])
                && $overwrite[$mergeProperty] !== null
                && (!is_string($overwrite[$mergeProperty]) || mb_strlen($overwrite[$mergeProperty]))
            ) {
                if (is_array($overwrite[$mergeProperty])) {
                    $mergedArray[$mergeProperty] = array_merge($mergedArray[$mergeProperty], $overwrite[$mergeProperty]);
                } else {
                    $mergedArray[$mergeProperty] = $overwrite[$mergeProperty];
                }
            }
        }

        return $mergedArray;
    }

    /**
     * Merges settings with the following priority (higher priority overwrites lower priority):
     *
     * 0. generalText
     * 1. text|headline|list
     * 2. types[generalText|text|headline|list]
     * 3. arguments
     *
     * @return void
     *
     * @throws ValidationException
     */
    protected function mergeSettingsAndArguments()
    {
        $settingsKey = $this->getSettingsKey();
        $mergedSettings = $this->mergeSettingsArrays($this->settings['generalText'], $this->settings[$settingsKey]);

        if (isset($this->arguments['type'])) {
            $type = $this->arguments['type'];

            if (isset($this->settings[$settingsKey]['types'][$type])) {
                $mergedSettings = $this->mergeSettingsArrays($mergedSettings, $this->settings[$settingsKey]['types'][$type]);
            } else if (isset($this->settings['generalText']['types'][$type])) {
                $mergedSettings = $this->mergeSettingsArrays($mergedSettings, $this->settings['generalText']['types'][$type]);
            } else {
                throw new ValidationException('Unknown text style type "' . $this->arguments['type'] . '" used. ERROR: 1536704610', 1536704610);
            }
        }

        $mergedSettings = $this->mergeSettingsArrays($mergedSettings, $this->arguments);

        foreach ($mergedSettings as $key => $setting) {
            $this->arguments[$key] = $setting;
        }
    }
}
