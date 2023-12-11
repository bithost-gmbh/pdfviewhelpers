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

/**
 * AbstractTextViewHelper
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
 */
abstract class AbstractTextViewHelper extends AbstractContentElementViewHelper
{
    protected array $mergeProperties = [
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
        'paragraphLineFeed',
        'autoHyphenation',
        'lineHeight',
        'characterSpacing',
    ];

    abstract protected function getSettingsKey(): string;

    /**
     * @inheritDoc
     */
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('trim', 'boolean', 'If true leading and trailing whitespace is removed.', false, null);
        $this->registerArgument('removeDoubleWhitespace', 'boolean', 'If true double whitespaces are removed.', false, null);
        $this->registerArgument('color', 'string', 'The color in HEX format: #000 or #000000.', false, null);
        $this->registerArgument('fontFamily', 'string', 'The font family name.', false, null);
        $this->registerArgument('fontSize', 'integer', 'The font size.', false, null);
        $this->registerArgument('fontStyle', 'string', 'The font style: regular, bold, italic or underline', false, null);
        $this->registerArgument('lineHeight', 'float', 'The relative line height.', false, null);
        $this->registerArgument('characterSpacing', 'float', 'The spacing between individual characters.', false, null);
        $this->registerArgument('padding', 'array', 'The cell padding given as array.', false, []);
        $this->registerArgument('text', 'string', 'The text to be rendered.', false, null);
        $this->registerArgument('alignment', 'string', 'The text alignment: left, center, right or justify', false, null);
        $this->registerArgument('paragraphSpacing', 'float', 'The spacing between text paragraphs.', false, null);
        $this->registerArgument('autoHyphenation', 'boolean', 'If true the text will be hyphenated automatically.', false, null);
        $this->registerArgument('paragraphLineFeed', 'boolean', 'If true a line feed is inserted after each paragraph.', false, null);
        $this->registerArgument('type', 'string', 'The text type configuration to be used.', false, null);
    }

    /**
     * @inheritDoc
     *
     * @throws Exception
     */
    public function initialize()
    {
        parent::initialize();

        $this->mergeSettingsAndArguments();

        if (empty($this->arguments['text'])) {
            // Workaround for an issue with the PageNumberAliasViewHelper and custom fonts, also see https://github.com/bithost-gmbh/pdfviewhelpers/issues/187
            if ($this->validationService->validateFontFamily($this->arguments['fontFamily'])) {
                $this->getPDF()->SetFont($this->arguments['fontFamily'], $this->conversionService->convertSpeakingFontStyleToTcpdfFontStyle($this->arguments['fontStyle']));
            }

            $this->arguments['text'] = (string) $this->renderChildren();
        }

        if ($this->arguments['trim']) {
            $this->arguments['text'] = trim((string) $this->arguments['text']);
        }

        if ($this->arguments['removeDoubleWhitespace']) {
            $this->arguments['text'] = preg_replace('/[ \t]+/', ' ', (string) $this->arguments['text']);
        }

        if ($this->arguments['autoHyphenation']) {
            $this->arguments['text'] = $this->hyphenationService->hyphenateText(
                (string) $this->arguments['text'],
                $this->hyphenationService->getHyphenFilePath($this->getHyphenFileName())
            );
        }

        if ($this->validationService->validateColor($this->arguments['color'])) {
            $this->arguments['color'] = $this->conversionService->convertHexToRGB($this->arguments['color']);
            $this->getPDF()->setTextColor($this->arguments['color']['R'], $this->arguments['color']['G'], $this->arguments['color']['B']);
        }

        if ($this->validationService->validateFontSize($this->arguments['fontSize'])) {
            $this->getPDF()->setFontSize($this->arguments['fontSize']);
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
     * @throws Exception
     */
    public function render(): void
    {
        $this->initializeMultiColumnSupport();

        $paragraphs = explode("\n", str_replace("\r\n", "\n", (string) $this->arguments['text']));
        $posY = $this->arguments['posY'] + $this->arguments['padding']['top'];

        foreach ($paragraphs as $paragraph) {
            if ($this->arguments['trim']) {
                $paragraph = trim((string) $paragraph);
            }

            if ($this->arguments['paragraphLineFeed']) {
                $paragraph .= "\n";
            }

            $this->getPDF()->MultiCell($this->arguments['width'], $this->arguments['height'] / count($paragraphs), $paragraph, 0, $this->conversionService->convertSpeakingAlignmentToTcpdfAlignment($this->arguments['alignment']), false, 1, $this->arguments['posX'], $posY, true, 0, false, true, 0, 'T', false);

            if ($this->validationService->validateParagraphSpacing($this->arguments['paragraphSpacing']) && $this->arguments['paragraphSpacing'] > 0) {
                $this->getPDF()->Ln((float)$this->arguments['paragraphSpacing'], false);
            }

            $posY = $this->getPDF()->GetY();
        }

        $this->getPDF()->setY($this->getPDF()->GetY() + $this->arguments['padding']['bottom']);
    }

    protected function mergeSettingsArrays(array $default, array $overwrite): array
    {
        $mergedArray = $default;

        foreach ($this->mergeProperties as $mergeProperty) {
            if (
                isset($overwrite[$mergeProperty])
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
     * @throws ValidationException
     */
    protected function mergeSettingsAndArguments(): void
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
