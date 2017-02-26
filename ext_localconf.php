<?php
defined('TYPO3_MODE') || die('Access denied');

\TYPO3\CMS\Core\Resource\Index\ExtractorRegistry::getInstance()->registerExtractionService(
    \MiniFranske\DominantImageColor\Resource\Extractor\DominantColorExtractor::class
);