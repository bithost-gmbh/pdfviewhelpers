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
 * AbstractContentElementViewHelper
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Marin <esteban.marin@bithost.ch>
 */
abstract class AbstractContentElementViewHelper extends AbstractPDFViewHelper {

	/**
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('posX', 'integer', '', FALSE, NULL);
		$this->registerArgument('posY', 'integer', '', FALSE, NULL);
		$this->registerArgument('width', 'integer', '', FALSE, NULL);
		$this->registerArgument('height', 'integer', '', FALSE, NULL);
	}

	/**
	 * @return void
	 */
	public function initialize() {
		if (!is_null($this->arguments['width'])) {
			$this->isValidWidth($this->arguments['width']);
		}

		if (!is_null($this->arguments['height'])) {
			$this->isValidHeight($this->arguments['height']);
		}

		$this->arguments['posX'] = $this->getPDF()->GetX();
		$this->arguments['posY'] = $this->getPDF()->GetY();
	}

	/**
	 * @param string $colorHex
	 *
	 * @return array
	 */
	protected function convertHexToRGB($colorHex) {
		$colorHex = str_replace("#", "", $colorHex);

		if (strlen($colorHex) == 3) {
			$r = hexdec(substr($colorHex, 0, 1) . substr($colorHex, 0, 1));
			$g = hexdec(substr($colorHex, 1, 1) . substr($colorHex, 1, 1));
			$b = hexdec(substr($colorHex, 2, 1) . substr($colorHex, 2, 1));
		} else {
			$r = hexdec(substr($colorHex, 0, 2));
			$g = hexdec(substr($colorHex, 2, 2));
			$b = hexdec(substr($colorHex, 4, 2));
		}

		return ['R' => $r, 'G' => $g, 'B' => $b];
	}

	/**
	 * @param string $imageTypes
	 *
	 * @return array 
	 */
	protected function convertImageTypeStringToImageTypeArray($imageTypes) {
		return explode(',', str_replace(' ', '', $imageTypes));
	}

	/**
	 * @return void
	 */
	protected function initializeMultiColumnSupport() {
		$multiColumnContext = $this->getMultiColumnContext();

		if ($multiColumnContext['isInAColumn']) {
			$this->arguments['width'] = $multiColumnContext['columnWidth'];
			$this->arguments['posX'] = $multiColumnContext['currentPosX'];
		}
	}

	/**
	 * @param string $src
	 *
	 * @return string
	 *
	 * @throws ValidationException
	 */
	protected function getImageRenderMode($src) {
		$fileExtension = pathinfo($src, PATHINFO_EXTENSION);

		if (in_array($fileExtension, $this->convertImageTypeStringToImageTypeArray($this->settings['config']['allowedImageTypes']['image']))) {
			return 'image';
		} elseif (in_array($fileExtension, $this->convertImageTypeStringToImageTypeArray($this->settings['config']['allowedImageTypes']['imageEPS']))) {
			return 'imageEPS';
		} elseif (in_array($fileExtension, $this->convertImageTypeStringToImageTypeArray($this->settings['config']['allowedImageTypes']['imageSVG']))) {
			return 'imageSVG';
		} else {
			throw new ValidationException('Imagetype is not supported. ERROR: 1363778014', 1363778014);
		}
	}

	/**
	 * @return array|bool $multiColumnContext
	 */
	protected function getMultiColumnContext() {
		if ($this->viewHelperVariableContainer->exists('MultiColumnViewHelper', 'multiColumnContext')) {
			return $this->viewHelperVariableContainer->get('MultiColumnViewHelper', 'multiColumnContext');
		} else {
			return FALSE;
		}
	}

	/**
	 * @param string $colorHex
	 *
	 * @return boolean
	 *
	 * @throws ValidationException
	 */
	protected function isValidColor($colorHex) {
		if (preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $colorHex)) {
			return TRUE;
		} else {
			throw new ValidationException('Your Color is invalid. Use #000 or #000000.', 1363765272);
		}
	}

	/**
	 * @param string $width
	 *
	 * @return boolean
	 *
	 * @throws ValidationException
	 */
	protected function isValidWidth($width) {
		if (is_numeric($width)) {
			return TRUE;
		} else {
			throw new ValidationException('Width must be an integer. ERROR: 1363765672', 1363765372);
		}
	}

	/**
	 * @param string $height
	 *
	 * @return boolean
	 *
	 * @throws ValidationException
	 */
	protected function isValidHeight($height) {
		if (is_numeric($height)) {
			return TRUE;
		} else {
			throw new ValidationException('Height must be an integer. ERROR: 1363766372', 1363765372);
		}
	}

}
