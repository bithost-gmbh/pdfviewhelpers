<?php

$EM_CONF[$_EXTKEY] = [
	'title' => 'PDF ViewHelpers',
	'description' => 'Provides ViewHelpers to create PDF documents.',
	'category' => 'plugin',
	'author' => 'Markus Mächler, Esteban Marin',
	'author_email' => 'markus.maechler@bithost.ch, esteban.marin@bithost.ch',
	'author_company' => 'Bithost GmbH',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => '0',
	'createDirs' => '',
	'clearCacheOnLoad' => 0,
	'version' => '1.0.1',
	'constraints' => [
		'depends' => [
			'typo3' => '6.2.0-7.6.99',
			'php' => '5.4.0-7.0.99',
		],
		'conflicts' => [],
		'suggests' => [],
	],
];
