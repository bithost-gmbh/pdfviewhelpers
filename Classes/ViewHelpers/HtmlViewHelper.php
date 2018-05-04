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

use Bithost\Pdfviewhelpers\Exception\ValidationException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * HtmlViewHelper
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Marin <esteban.marin@bithost.ch>
 */
class HtmlViewHelper extends AbstractContentElementViewHelper {

	/**
	 * @return void
	 */
	public function initializeArguments() {
		parent::initializeArguments();

		$this->registerArgument('autoHyphenation', 'boolean', '', FALSE, $this->settings['html']['autoHyphenation']);
		$this->registerArgument('hyphenFile', 'string', '', FALSE, $this->settings['html']['hyphenFile']);
		$this->registerArgument('styleSheet', 'string', '', FALSE, $this->settings['html']['styleSheet']);
	}

	/**
	 * @return void
	 *
	 * @throws ValidationException if an invalid style sheet path is provided
	 */
	public function render() {
		$html = $this->renderChildren();
		$htmlStyle = '';
		$color = $this->convertHexToRGB($this->settings['generalText']['color']);
		$padding = $this->settings['generalText']['padding'];

		if (!empty($this->arguments['styleSheet'])) {
			$styleSheetPath = PATH_site . $this->arguments['styleSheet'];

			if (!file_exists($styleSheetPath) || !is_readable($styleSheetPath)) {
				throw new ValidationException('Path to style sheet "' . $styleSheetPath . '" does not exist or file is not readable. ERROR: 1492706529', 1492706529);
			}

			$htmlStyle = '<style>' . file_get_contents($styleSheetPath) . '</style>';
		}

		if ($this->arguments['autoHyphenation']) {
			$hyphenFilePath = GeneralUtility::getFileAbsFileName('EXT:pdfviewhelpers/Resources/Private/Hyphenation/' . $this->arguments['hyphenFile']);

			if (!file_exists($hyphenFilePath) || !is_readable($hyphenFilePath)) {
				throw new ValidationException('Path to hyphen file "' . $hyphenFilePath . '" does not exist or file is not readable. ERROR: 1525410458', 1525410458);
			}

			$html = $this->getPDF()->hyphenateText($html, $hyphenFilePath);
		}

		//reset settings to generalText
		$this->getPDF()->SetTextColor($color['R'], $color['G'], $color['B']);
		$this->getPDF()->SetFontSize($this->settings['generalText']['fontSize']);
		$this->getPDF()->SetFont($this->settings['generalText']['fontFamily'], AbstractTextViewHelper::convertToTcpdfFontStyle($this->settings['generalText']['fontStyle']));
		$this->getPDF()->setCellPaddings($padding['left'], $padding['top'], $padding['right'], $padding['bottom']);

		$this->getPDF()->writeHTML($htmlStyle . $html, TRUE, FALSE, TRUE, FALSE, '');
	}

}
