<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Lazy load placeholders',
    'description' => 'Create placeholders for lazy loading images for instance based on the dominant image color',
    'category' => 'be',
    'state' => 'stable',
    'clearCacheOnLoad' => 1,
    'author' => 'Frans Saris',
    'author_email' => 'franssaris@gmail.com',
    'author_company' => 'MiniFranske',
    'version' => '0.0.2',
    'constraints' => [
        'depends' => [
            'typo3/cms-core' => '*',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
