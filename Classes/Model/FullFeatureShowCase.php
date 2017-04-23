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

/**
 * FullFeatureShowCase
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Marin <esteban.marin@bithost.ch>
 */
class FullFeatureShowCase extends \FPDI {
	/**
	 * @return void
	 */
	public function Header() {
		$header1 = "Bithost GmbH - Milchbuckstrasse 83 CH-8057 Zürich";
		$header2 = "hallo@bithost.ch - www.bithost.ch";

		$this->SetTextColor(140, 140, 140);
		$this->SetFontSize(11);

		$this->MultiCell(null, null, $header1, 0, 'L', FALSE, 1, 15, 10, TRUE, 0, FALSE, TRUE, 0, 'T', FALSE);
		$this->MultiCell(null, null, $header2, 0, 'R', FALSE, 1, 15, 10, TRUE, 0, FALSE, TRUE, 0, 'T', FALSE);

		$this->SetDrawColor(140, 140, 140);
		$this->Line(15, $this->y + 5, $this->w - 15, $this->y + 5);
	}

	/**
	 * @return void
	 */
	public function Footer() {
		$this->SetY(-20);
		$this->SetDrawColor(140, 140, 140);
		$this->Line(15, $this->y, $this->w - 15, $this->y);

		$this->SetY(-17);
		$this->SetTextColor(140, 140, 140);
		$this->SetFontSize(11);
		$this->Cell($this->w - 15, 10, 'Page '.$this->getAliasNumPage() . ' of '.$this->getAliasNbPages(), 0, false, 'C', 0, '', 1, false, 'T', 'M');
	}
}
