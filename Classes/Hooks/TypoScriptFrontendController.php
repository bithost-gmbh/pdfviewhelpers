<?php

namespace Bithost\Pdfviewhelpers\Hooks;

use TYPO3\CMS\Core\SingletonInterface;

class TypoScriptFrontendController implements SingletonInterface {
	/**
	 * Prevent any output when a pdf is rendered, especially any headers being set!
	 *
	 * @param	array		$params Parameters from frontend
	 * @param	object		$ref TSFE object
	 * @return	void
	 */
	function isOutputting(&$params, $ref) {
		if (isset($params['pObj']->applicationData['tx_pdfviewhelpers']['pdfOutput'])
			&& $params['pObj']->applicationData['tx_pdfviewhelpers']['pdfOutput'] === true
		) {
			$params['enableOutput'] = false;
		}
	}
}