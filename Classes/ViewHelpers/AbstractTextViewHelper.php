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

/**
 * AbstractTextViewHelper
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Marin <esteban.marin@bithost.ch>
 */
abstract class AbstractTextViewHelper extends AbstractContentElementViewHelper {

	/**
	 * @return void
	 */
	public function initializeArguments() {
		parent::initializeArguments();

		$this->registerArgument('trim', 'boolean', '', FALSE, $this->settings['generalText']['trim']);
		$this->registerArgument('removeDoubleWhitespace', 'boolean', '', FALSE, $this->settings['generalText']['removeDoubleWhitespace']);
		$this->registerArgument('color', 'string', '', FALSE, $this->settings['generalText']['color']);
		$this->registerArgument('fontFamily', 'string', '', FALSE, $this->settings['generalText']['fontFamily']);
		$this->registerArgument('fontSize', 'integer', '', FALSE, $this->settings['generalText']['fontSize']);
		$this->registerArgument('fontStyle', 'string', '', FALSE, $this->settings['generalText']['fontStyle']);
		$this->registerArgument('padding', 'array', '', FALSE, NULL);
		$this->registerArgument('text', 'string', '', FALSE, NULL);
		$this->registerArgument('alignment', 'string', 'Text Alignment. Possible values: "left", "center", "right", "justify". Defaults to "left"', false, $this->settings['generalText']['alignment']);
		$this->registerArgument('paragraphSpacing', 'float', 'Spacing after each paragraph. Defaults to 0', FALSE, $this->settings['generalText']['paragraphSpacing']);
	}

	/**
	 * @return void
	 */
	public function initialize() {
		parent::initialize();

		if (empty($this->arguments['text'])) {
			$this->arguments['text'] = $this->renderChildren();
		}

		if ($this->arguments['trim']) {
			$this->arguments['text'] = trim($this->arguments['text']);
		}

		if ($this->arguments['removeDoubleWhitespace']) {
			$this->arguments['text'] = preg_replace('/[ \t]+/', ' ', $this->arguments['text']);
		}

		if ($this->isValidColor($this->arguments['color'])) {
			$this->arguments['color'] = $this->convertHexToRGB($this->arguments['color']);
			$this->getPDF()->SetTextColor($this->arguments['color']['R'], $this->arguments['color']['G'], $this->arguments['color']['B']);
		}

		if ($this->isValidFontSize($this->arguments['fontSize'])) {
			$this->getPDF()->SetFontSize($this->arguments['fontSize']);
		}

		if ($this->isValidFontFamily($this->arguments['fontFamily']) && $this->isValidFontStyle($this->arguments['fontStyle'])) {
			$this->getPDF()->SetFont($this->arguments['fontFamily'], self::convertToTcpdfFontStyle($this->arguments['fontStyle']));
		}
	}

	/**
	 * @return void
	 */
	public function render() {
		$this->initializeMultiColumnSupport();

		$paragraphs = explode(
			"\n", str_replace("\r\n", "\n", $this->arguments['text'])
		);
		$posY = $this->arguments['posY'];

		foreach ($paragraphs as $paragraph) {
			if ($this->arguments['trim']) {
				$paragraph = trim($paragraph);
			}

			$this->getPDF()->MultiCell(
				$this->arguments['width'], $this->arguments['height'] / count($paragraphs), $paragraph, 0, $this->getAlignmentString($this->arguments['alignment']), FALSE, 1, $this->arguments['posX'], $posY, TRUE, 0, FALSE, TRUE, 0, 'T', FALSE
			);

			if ($this->isValidParagraphSpacing($this->arguments['paragraphSpacing']) && $this->arguments['paragraphSpacing'] > 0
			) {
				$this->getPDF()->Ln((float) $this->arguments['paragraphSpacing'], false);
			}

			$posY = $this->getPDF()->GetY();
		}
	}

	/**
	 * Converts pdfviewhelper fontStyle syntax to TCPDF syntax. This function is necessary because TCPDF uses an empty
	 * string to represent "regular", but we can not do this because of the settings inheritance (empty means inherit).
	 *
	 * @param string $pdfviewhelperFontStyle
	 *
	 * @return string
	 */
	public static function convertToTcpdfFontStyle($pdfviewhelperFontStyle) {
		if ($pdfviewhelperFontStyle === 'R') {
			return '';
		}

		return $pdfviewhelperFontStyle;
	}

	/**
	 * @param string $fontSize
	 *
	 * @return boolean
	 *
	 * @throws ValidationException
	 */
	protected function isValidFontSize($fontSize) {
		if (is_numeric($fontSize)) {
			return TRUE;
		} else {
			throw new ValidationException('FontSize must be an integer. ERROR: 1363765372', 1363765372);
		}
	}

	/**
	 * @param array $padding
	 *
	 * @return boolean
	 *
	 * @throws ValidationException
	 */
	protected function isValidPadding($padding) {
		if (isset($padding['top'], $padding['right'], $padding['bottom'], $padding['left']) && is_numeric($padding['top']) && is_numeric($padding['right']) && is_numeric($padding['bottom']) && is_numeric($padding['left'])
		) {
			return TRUE;
		} else {
			throw new ValidationException('Padding must be an Array with Elements: top:[int],right:[int],bottom:[int],left:[int] ERROR: 1363769351', 1363769351);
		}
	}

	/**
	 * @param string $paragraphSpacing
	 *
	 * @return boolean
	 *
	 * @throws ValidationException
	 */
	protected function isValidParagraphSpacing($paragraphSpacing) {
		if (is_numeric($paragraphSpacing)) {
			return TRUE;
		} else {
			throw new ValidationException('ParagraphSpacing must be an integer. ERROR: 1363765379', 1363765379);
		}
	}

	/**
	 * @param string $alignment
	 *
	 * @return boolean
	 *
	 * @throws ValidationException
	 */
	protected function isValidAlignment($alignment) {
		if (in_array($alignment, ['left', 'center', 'right', 'justify'])) {
			return TRUE;
		} else {
			throw new ValidationException('Alignment must be "left", "center", "right" or "justify". ERROR: 1363765672', 1363765672);
		}
	}

	/**
	 * @param string $fontStyle
	 *
	 * @return boolean
	 *
	 * @throws ValidationException
	 */
	protected function isValidFontStyle($fontStyle) {
		if (in_array($fontStyle, ['B', 'I', 'U', 'R'])) {
			return TRUE;
		} else {
			throw new ValidationException('FontStyle must be "B" (bold), "I" (italic), "U" (underline) or "R" (regular). ERROR: 1492799612', 1492799612);
		}
	}

	/**
	 * Check fontFamily for compatibility with TCPDF naming conventions
	 *
	 * @param string $fontFamily
	 *
	 * @return boolean
	 *
	 * @throws ValidationException
	 */
	protected function isValidFontFamily($fontFamily) {
		//TCPDF transformation START
		$tcpdfFontFamilyName = strtolower($fontFamily);
		$tcpdfFontFamilyName = preg_replace('/[^a-z0-9_]/', '', $tcpdfFontFamilyName);
		$search  = ['bold', 'oblique', 'italic', 'regular'];
		$replace = ['b', 'i', 'i', ''];
		$tcpdfFontFamilyName = str_replace($search, $replace, $tcpdfFontFamilyName);
		if (empty($tcpdfFontFamilyName)) {
			// set generic name
			$tcpdfFontFamilyName = 'tcpdffont';
		}
		//TCPDF transformation END

		if ($fontFamily === $tcpdfFontFamilyName) {
			return TRUE;
		} else {
			throw new ValidationException('Invalid fontFamily name "' . $fontFamily . '". Name must only contain letters "a-z0-9_" and none of the words "bold", "oblique", "italic" and "regular". ERROR: 1492809393', 1492809393);
		}
	}

	/**
	 * @param string $alignment
	 *
	 * @return string
	 */
	protected function getAlignmentString($alignment) {
		$alignmentString = 'L';

		switch ($alignment) {
			case 'left':
			case 'L':
				$alignmentString = 'L';
				break;
			case 'center':
			case 'C':
				$alignmentString = 'C';
				break;
			case 'right':
			case 'R':
				$alignmentString = 'R';
				break;
			case 'justify':
			case 'J':
				$alignmentString = 'J';
				break;
		}

		return $alignmentString;
	}

}
