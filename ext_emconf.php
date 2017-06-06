<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Youku.com online media connector',
    'description' => 'Youku.com online media integration for TYPO3',
    'category' => 'misc',
    'state' => 'beta',
    'clearCacheOnLoad' => 1,
    'author' => 'Frans Saris [beech.it]',
    'author_email' => 't3ext@beech.it',
    'author_company' => 'Beech.it',
    'version' => '0.0.1',
    'constraints' => [
        'depends' => [
            'typo3' => '7.6.0-8.7.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
