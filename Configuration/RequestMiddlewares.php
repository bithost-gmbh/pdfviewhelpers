<?php

return [
    'frontend' => [
        'bithost/pdfviewhelpers/stop-output' => [
            'target' => \Bithost\Pdfviewhelpers\Middleware\Frontend\StopOutputMiddleware::class,
            'after' => 'typo3/cms-frontend/page-argument-validator'
        ]
    ]
];
