<?php

return [
    'frontend' => [
        'pdfviewhelpers' => [
            'target' => \Bithost\Pdfviewhelpers\Middleware\PdfViewHelpersStopOutputMiddleware::class,
            'after' => 'typo3/cms-frontend/page-argument-validator'
        ]
    ]
];
