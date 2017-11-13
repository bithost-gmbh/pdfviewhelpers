<?php


$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['isOutputting']['tx_pdfviewhelpers'] = \Bithost\Pdfviewhelpers\Hooks\TypoScriptFrontendController::class.'->isOutputting';