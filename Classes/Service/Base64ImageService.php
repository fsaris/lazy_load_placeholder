<?php
namespace MiniFranske\LazyLoadPlaceholder\Service;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
use TYPO3\CMS\Core\SingletonInterface;

/**
 * Class GifBuilderService
 * @package MiniFranske\LazyLoadPlaceholder\Service
 */
class Base64ImageService implements SingletonInterface
{

    /**
     * Generate base64 gif data string
     *
     * Code is from https://github.com/Lorti/dominant-colors-lazy-loading-wordpress-plugins
     * @author     Manuel Wieser <office@manuelwieser.com>
     *
     * @param string $color Hex color code (example: #FF0011)
     * @return string
     */
    public function generateGif($color)
    {
        $header = '474946383961';
        $logical_screen_descriptor = '01000100800100';
        $image_descriptor = '2c000000000100010000';
        $image_data = '0202440100';
        $trailer = '3b';
        $gif = implode([
            $header,
            $logical_screen_descriptor,
            ltrim($color, '#'),
            '000000',
            $image_descriptor,
            $image_data,
            $trailer
        ]);
        return 'data:image/gif;base64,' . base64_encode(hex2bin($gif));
    }

    public function generateSvg($color, $width = 1, $height = 1)
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 %1$d %2$d"><rect x="0" y="0" width="%1$d" height="%2$d" style="fill:%3$s"/></svg>';
        return 'data:image/svg+xml;base64,' . base64_encode(sprintf($svg, (int)$width, (int)$height, $color));
    }
}