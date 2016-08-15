<?php
namespace Bithost\Pdfviewhelpers\Model;

/***
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
 ***/

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * BithostTCPDF
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Marin <esteban.marin@bithost.ch>
 */
class BithostTCPDF extends \TCPDF {
	/**
	 * @return void
	 */
	public function Header() {
		$extPath = ExtensionManagementUtility::extPath('pdfviewhelpers');
		$address = "Bithost GmbH \nMilchubckstrasse 83 \nCH-8057 Zürich \n\nhallo@bithost.ch \n044 585 28 20 \n\nwww.bithost.ch";

		$this->SetTextColor(140, 140, 140);
		$this->SetFontSize(11);

		$this->Image($extPath . 'Resources/Public/images/logo.png', 15, 15, 56, 24, '', '', '', FALSE, 300, '', FALSE, FALSE, 0, FALSE, FALSE, FALSE, FALSE);
		$this->MultiCell(null, null, $address, 0, 'R', FALSE, 1, 0, 45, TRUE, 0, FALSE, TRUE, 0, 'T', FALSE);
	}

	/**
	 * @return void
	 */
	public function Footer() {

	}
}
