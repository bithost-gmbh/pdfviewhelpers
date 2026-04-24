<?php

$EM_CONF[$_EXTKEY] = [
	'title' => 'PDF ViewHelpers',
	'description' => 'Provides various Fluid ViewHelpers to create PDF documents. Under the hood pdfviewhelpers uses TCPDF and FPDI.',
	'category' => 'fe',
	'author' => 'Markus Mächler, Esteban Gehring',
	'author_email' => 'markus.maechler@bithost.ch, esteban.gehring@bithost.ch',
	'author_company' => 'Bithost GmbH',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => '0',
	'createDirs' => '',
	'clearCacheOnLoad' => 0,
	'version' => '3.1.1',
	'constraints' => [
		'depends' => [
			'typo3' => '13.4.0-14.3.99',
			'php' => '8.2.0-0.0.0',
		],
		'conflicts' => [],
		'suggests' => [],
	],
];
