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

/**
 * HeadlineViewHelper
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Marin <esteban.marin@bithost.ch>
 */
class HeadlineViewHelper extends AbstractTextViewHelper {

	/**
	 * @return void
	 */
	public function initializeArguments() {
		parent::initializeArguments();

		if (!empty($this->settings['headline']['trim'])) {
			$this->overrideArgument('trim', 'boolean', '', FALSE, $this->settings['headline']['trim']);
		}
		if (!empty($this->settings['headline']['color'])) {
			$this->overrideArgument('color', 'string', '', FALSE, $this->settings['headline']['color']);
		}
		if (!empty($this->settings['headline']['fontFamily'])) {
			$this->overrideArgument('fontFamily', 'string', '', FALSE, $this->settings['headline']['fontFamily']);
		}
		if (!empty($this->settings['headline']['fontSize'])) {
			$this->overrideArgument('fontSize', 'integer', '', FALSE, $this->settings['headline']['fontSize']);
		}
		if (!empty($this->settings['headline']['fontStyle'])) {
			$this->overrideArgument('fontStyle', 'string', '', FALSE, $this->settings['headline']['fontStyle']);
		}
		if (!empty($this->settings['headline']['alignment'])) {
			$this->overrideArgument('alignment', 'string', '', FALSE, $this->settings['headline']['alignment']);
		}
		if (!empty($this->settings['headline']['paragraphSpacing'])) {
			$this->overrideArgument('paragraphSpacing', 'float', '', FALSE, $this->settings['headline']['paragraphSpacing']);
		}
	}

	/**
	 * @return void
	 */
	public function initialize() {
		parent::initialize();

		if (empty($this->arguments['padding'])) {
			if (!empty($this->settings['headline']['padding'])) {
				$this->arguments['padding'] = $this->settings['headline']['padding'];
			} else {
				$this->arguments['padding'] = $this->settings['generalText']['padding'];
			}
		}

		if ($this->isValidPadding($this->arguments['padding'])) {
			$this->getPDF()->setCellPaddings($this->arguments['padding']['left'], $this->arguments['padding']['top'], $this->arguments['padding']['right'], $this->arguments['padding']['bottom']);
		}
	}

}
