<?php

if (!defined('TYPO3')) {
	die ('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('pdfviewhelpers', 'Configuration/TypoScript', 'pdfviewhelpers');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('pdfviewhelpers', 'Configuration/TypoScript/Extensions/News', 'pdfviewhelpers - EXT:news');
