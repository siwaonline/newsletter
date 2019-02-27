<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Newsletter',
    'description' => 'Send any pages as Newsletter and provide statistics on opened emails and clicked links.',
    'category' => 'module',
    'version' => '5.0.0-dev',
    'state' => 'stable',
    'uploadfolder' => 1,
    'author' => 'Ecodev',
    'author_email' => 'contact@ecodev.ch',
    'author_company' => 'Ecodev',
    'constraints' => [
        'depends' => [
            'php' => '7.2.0-0.0.0',
            'typo3' => '9.5.0-10.x',
            'scheduler' => '9.5.0-10.x',
        ],
    ],
];
