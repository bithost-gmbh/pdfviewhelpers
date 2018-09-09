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

/**
 * HeadlineViewHelper
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Marin <esteban.marin@bithost.ch>
 */
class HeadlineViewHelper extends AbstractTextViewHelper
{
    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();

        if (strlen($this->settings['headline']['trim'])) {
            $this->overrideArgument('trim', 'boolean', '', false, (boolean)$this->settings['headline']['trim']);
        }
        if (strlen($this->settings['headline']['removeDoubleWhitespace'])) {
            $this->overrideArgument('removeDoubleWhitespace', 'boolean', '', false, (boolean)$this->settings['headline']['removeDoubleWhitespace']);
        }
        if (!empty($this->settings['headline']['color'])) {
            $this->overrideArgument('color', 'string', '', false, $this->settings['headline']['color']);
        }
        if (!empty($this->settings['headline']['fontFamily'])) {
            $this->overrideArgument('fontFamily', 'string', '', false, $this->settings['headline']['fontFamily']);
        }
        if (!empty($this->settings['headline']['fontSize'])) {
            $this->overrideArgument('fontSize', 'integer', '', false, $this->settings['headline']['fontSize']);
        }
        if (!empty($this->settings['headline']['fontStyle'])) {
            $this->overrideArgument('fontStyle', 'string', '', false, $this->settings['headline']['fontStyle']);
        }
        if (!empty($this->settings['headline']['alignment'])) {
            $this->overrideArgument('alignment', 'string', '', false, $this->settings['headline']['alignment']);
        }
        if (strlen($this->settings['headline']['paragraphSpacing'])) {
            $this->overrideArgument('paragraphSpacing', 'float', '', false, $this->settings['headline']['paragraphSpacing']);
        }
        if (strlen($this->settings['headline']['autoHyphenation'])) {
            $this->overrideArgument('autoHyphenation', 'boolean', '', false, $this->settings['headline']['autoHyphenation']);
        }
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    public function initialize()
    {
        parent::initialize();

        $this->arguments['padding'] = array_merge($this->settings['generalText']['padding'], $this->settings['headline']['padding'], $this->arguments['padding']);

        if ($this->validationService->validatePadding($this->arguments['padding'])) {
            $this->getPDF()->setCellPaddings(
                $this->arguments['padding']['left'],
                $this->arguments['padding']['top'],
                $this->arguments['padding']['right'],
                $this->arguments['padding']['bottom']
            );
        }
    }
}
