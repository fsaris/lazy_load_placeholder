<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Dominant image color extractor',
    'description' => 'Determine the dominant image color to use as background for lazy loading images etc',
    'category' => 'be',
    'state' => 'stable',
    'clearCacheOnLoad' => 1,
    'author' => 'Frans Saris',
    'author_email' => 'franssaris@gmail.com',
    'author_company' => 'MiniFranske',
    'version' => '0.0.1',
    'constraints' => [
        'depends' => [
            'typo3' => '7.6.0-8.7.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
