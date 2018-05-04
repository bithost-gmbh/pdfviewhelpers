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
 * ImageViewHelper
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Marin <esteban.marin@bithost.ch>
 */
class ImageViewHelper extends AbstractContentElementViewHelper {

	/**
	 * @return void
	 */
	public function initializeArguments() {
		parent::initializeArguments();

		$this->registerArgument('src', 'string', '', TRUE, NULL);
	}

	/**
	 * @return void
	 */
	public function initialize() {
		parent::initialize();
	}

	/**
	 * @return void
	 *
	 * @throws ValidationException
	 */
	public function render() {
		$this->initializeMultiColumnSupport();

		$src = GeneralUtility::getFileAbsFileName($this->arguments['src']);

		if (!file_exists($src)) {
			throw new ValidationException('Image path "' . $this->arguments['src'] . '" does not exist. ERROR: 1471036581', 1471036581);
		}

		switch ($this->getImageRenderMode($src)) {
			case 'image':
				$this->getPDF()->Image($src, $this->arguments['posX'], $this->arguments['posY'], $this->arguments['width'], $this->arguments['height'], '', '', '', FALSE, 300, '', FALSE, FALSE, 0, FALSE, FALSE, TRUE, FALSE);
				break;
			case 'imageEPS':
				$this->getPDF()->ImageEps($src, $this->arguments['posX'], $this->arguments['posY'], $this->arguments['width'], $this->arguments['height'], '', TRUE, '', '', 0, TRUE, FALSE);
				break;
			case 'imageSVG':
				$this->getPDF()->ImageSVG($src, $this->arguments['posX'], $this->arguments['posY'], $this->arguments['width'], $this->arguments['height'], '', '', '', 0, TRUE);
				break;
		}

		$this->getPDF()->SetY($this->getPDF()->getImageRBY());
	}

}
