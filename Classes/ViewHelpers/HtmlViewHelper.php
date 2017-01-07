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
 * HtmlViewHelper
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Marin <esteban.marin@bithost.ch>
 */
class HtmlViewHelper extends AbstractContentElementViewHelper {

	/**
	 * @return void
	 */
	public function render() {
		$html = $this->renderChildren();
		$color = $this->convertHexToRGB($this->settings['generalText']['color']);
		$padding = $this->settings['generalText']['padding'];

		//reset settings to generalText
		$this->getPDF()->SetTextColor($color['R'], $color['G'], $color['B']);
		$this->getPDF()->SetFontSize($this->settings['generalText']['fontSize']);
		$this->getPDF()->SetFont($this->settings['generalText']['fontFamily']);
		$this->getPDF()->setCellPaddings($padding['left'], $padding['top'], $padding['right'], $padding['bottom']);

		$this->getPDF()->writeHTML($html, TRUE, FALSE, TRUE, FALSE, '');
	}

}
