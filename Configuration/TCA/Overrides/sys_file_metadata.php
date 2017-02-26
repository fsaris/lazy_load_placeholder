<?php
defined('TYPO3_MODE') || die('Access denied');

$additionalColumns = [
    'dominant_color' => [
        'exclude' => 1,
        'l10n_mode' => 'exclude',
        'l10n_display' => 'defaultAsReadonly',
        'label' => 'LLL:EXT:dominant_image_color/Resources/Private/Language/locallang_db.xlf:sys_file_metadata.dominant_color',
        'config' => [
            'type' => 'input',
            'renderType' => 'colorpicker',
            'size' => '7',
            'eval' => 'trim',
            'default' => '',
        ],
    ],
];


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('sys_file_metadata', $additionalColumns);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'sys_file_metadata',
    'dominant_color',
    TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE,
    'after:source'
);