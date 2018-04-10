<?php

$EM_CONF[$_EXTKEY] = [
	'title' => 'PDF ViewHelpers',
	'description' => 'Provides various Fluid ViewHelpers to create PDF documents. It is possible to use existing PDFs as template and extend them using these ViewHelpers. Under the hood pdfviewhelpers uses TCPDF and FPDI.',
	'category' => 'fe',
	'author' => 'Markus Mächler, Esteban Marin',
	'author_email' => 'markus.maechler@bithost.ch, esteban.marin@bithost.ch',
	'author_company' => 'Bithost GmbH',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => '0',
	'createDirs' => '',
	'clearCacheOnLoad' => 0,
	'version' => '1.4.0',
	'constraints' => [
		'depends' => [
			'typo3' => '6.2.0-8.7.99',
			'php' => '5.4.0-7.1.99',
		],
		'conflicts' => [],
		'suggests' => [],
	],
];
