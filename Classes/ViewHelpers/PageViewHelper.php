<?php
namespace Bithost\Pdfviewhelpers\ViewHelpers;

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

use Bithost\Pdfviewhelpers\Exception\Exception;
use FPDI;

/**
 * PageViewHelper
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Marin <esteban.marin@bithost.ch>
 */
class PageViewHelper extends AbstractPDFViewHelper {
	
	/**
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('autoPageBreak', 'boolean', '', FALSE, $this->settings['page.']['autoPageBreak']);
		$this->registerArgument('margins', 'array', '', FALSE, NULL);
		$this->registerArgument('importPage', 'integer', '', FALSE, $this->settings['page.']['importPage']);
	}
	
	/**
	 * @return void
	 */	
	public function initialize() {
		if (is_null($this->arguments['margins'])) {
			$this->arguments['margins'] = $this->settings['page.']['margins.'];
		}

		$this->getPDF()->SetMargins($this->arguments['margins']['left'], $this->arguments['margins']['top'], $this->arguments['margins']['right']);
		$this->getPDF()->SetAutoPageBreak($this->arguments['autoPageBreak'], $this->arguments['margins']['bottom']);
	}
	
	/**
	 * @return void
	 *
	 * @throws Exception
	 */
	public function render() {
		$templateId = -1;

		if (!empty($this->arguments['importPage'])) {
			if ($this->getPDF() instanceof FPDI) {
				$templateId = $this->getPDF()->importPage($this->arguments['importPage']);
			} else {
				throw new Exception('PDF object must be instance of FPDI to support option "sourceFile". ERROR: 1474144733', 1474144733);
			}
		}

		$this->getPDF()->AddPage();

		if (!empty($this->arguments['importPage'])) {
			$this->getPDF()->useTemplate($templateId);
		}

		$this->renderChildren();
	}
}
