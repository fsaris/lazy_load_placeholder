<?php
namespace MiniFranske\LazyLoadPlaceholder\ViewHelpers;

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
use MiniFranske\LazyLoadPlaceholder\Service\Base64ImageService;
use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Resource\Exception\ResourceDoesNotExistException;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * Resizes a given image (if required) and renders the respective img tag with a placeholder image in src and
 * the real image in data-src
 *
 * = Example =
 *
 * <code title="Image Object">
 * <f:image image="{imageObject}" />
 * </code>
 * <output>
 * <img alt="alt set in image record" src="data:image/svg+xml;base64...." data-src="fileadmin/_processed_/323223424.png" width="396" height="375" />
 * </output>
 */
class LazyImageViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * @var string
     */
    protected $tagName = 'img';

    /**
     * @var \TYPO3\CMS\Extbase\Service\ImageService
     */
    protected $imageService;

    /**
     * @var Base64ImageService
     */
    protected $base64ImageService;

    /**
     * @param \TYPO3\CMS\Extbase\Service\ImageService $imageService
     */
    public function injectImageService(\TYPO3\CMS\Extbase\Service\ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * @param Base64ImageService $base64ImageService
     */
    public function injectBase64ImageService(Base64ImageService $base64ImageService)
    {
        $this->base64ImageService = $base64ImageService;
    }

    /**
     * Initialize arguments.
     *
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();
        $this->registerTagAttribute('alt', 'string', 'Specifies an alternate text for an image', false);

        $this->registerArgument('src', 'string', 'a path to a file, a combined FAL identifier or an uid (int). If $treatIdAsReference is set, the integer is considered the uid of the sys_file_reference record. If you already got a FAL object, consider using the $image parameter instead');
        $this->registerArgument('treatIdAsReference', 'bool', 'given src argument is a sys_file_reference record');
        $this->registerArgument('image', 'object', 'a FAL object');
        $this->registerArgument('crop', 'string|bool', 'overrule cropping of image (setting to FALSE disables the cropping set in FileReference)');
        $this->registerArgument('cropVariant', 'string', 'select a cropping variant, in case multiple croppings have been specified or stored in FileReference', false, 'default');

        $this->registerArgument('width', 'string', 'width of the image. This can be a numeric value representing the fixed width of the image in pixels. But you can also perform simple calculations by adding "m" or "c" to the value. See imgResource.width for possible options.');
        $this->registerArgument('height', 'string', 'height of the image. This can be a numeric value representing the fixed height of the image in pixels. But you can also perform simple calculations by adding "m" or "c" to the value. See imgResource.width for possible options.');
        $this->registerArgument('minWidth', 'int', 'minimum width of the image');
        $this->registerArgument('minHeight', 'int', 'minimum width of the image');
        $this->registerArgument('maxWidth', 'int', 'minimum width of the image');
        $this->registerArgument('maxHeight', 'int', 'minimum width of the image');
        $this->registerArgument('absolute', 'bool', 'Force absolute URL', false, false);

        $this->registerArgument('fallbackBgColor', 'string', 'Fallback color to use when image has no dominant color', false, '#EEEEEE');
    }

    /**
     * Resizes a given image (if required) and renders the respective img tag
     *
     * @see https://docs.typo3.org/typo3cms/TyposcriptReference/ContentObjects/Image/
     *
     * @throws \TYPO3Fluid\Fluid\Core\ViewHelper\Exception
     * @return string Rendered tag
     */
    public function render()
    {
        if ((is_null($this->arguments['src']) && is_null($this->arguments['image'])) || (!is_null($this->arguments['src']) && !is_null($this->arguments['image']))) {
            throw new \TYPO3Fluid\Fluid\Core\ViewHelper\Exception('You must either specify a string src or a File object.', 1382284106);
        }

        try {
            $image = $this->imageService->getImage($this->arguments['src'] ?? '', $this->arguments['image'], (bool)$this->arguments['treatIdAsReference']);
            $cropString = $this->arguments['crop'];
            if ($cropString === null && $image->hasProperty('crop') && $image->getProperty('crop')) {
                $cropString = $image->getProperty('crop');
            }
            // New cropping since TYPO3 8.6
            if (class_exists(CropVariantCollection::class)) {
                $cropVariantCollection = CropVariantCollection::create((string)$cropString);
                $cropVariant = $this->arguments['cropVariant'] ?: 'default';
                $crop = $cropVariantCollection->getCropArea($cropVariant)->makeAbsoluteBasedOnFile($image);

                if (!$cropVariantCollection->getFocusArea($cropVariant)->isEmpty()) {
                    $this->tag->addAttribute('data-focus-area', $cropVariantCollection->getFocusArea($cropVariant)->makeAbsoluteBasedOnFile($image));
                }
            } else {
                $crop = $cropString;
            }
            $processingInstructions = [
                'width' => $this->arguments['width'],
                'height' => $this->arguments['height'],
                'minWidth' => $this->arguments['minWidth'],
                'minHeight' => $this->arguments['minHeight'],
                'maxWidth' => $this->arguments['maxWidth'],
                'maxHeight' => $this->arguments['maxHeight'],
                'crop' => $crop,
            ];
            $processedImage = $this->imageService->applyProcessingInstructions($image, $processingInstructions);
            $imageUri = $this->imageService->getImageUri($processedImage, $this->arguments['absolute']);

            $dominantColor = $this->arguments['fallbackBgColor'];
            if ($image->getProperty('dominant_color')) {
                $dominantColor = $image->getProperty('dominant_color');
            }
            $this->tag->addAttribute('src', $this->base64ImageService->generateSvg($dominantColor, $processedImage->getProperty('width'), $processedImage->getProperty('height')));
            $this->tag->addAttribute('data-src', $imageUri);
            $this->tag->addAttribute('width', $processedImage->getProperty('width'));
            $this->tag->addAttribute('height', $processedImage->getProperty('height'));

            $alt = $image->getProperty('alternative');
            $title = $image->getProperty('title');

            // The alt-attribute is mandatory to have valid html-code, therefore add it even if it is empty
            if (empty($this->arguments['alt'])) {
                $this->tag->addAttribute('alt', $alt);
            }
            if (empty($this->arguments['title']) && $title) {
                $this->tag->addAttribute('title', $title);
            }
        } catch (ResourceDoesNotExistException $e) {
            // thrown if file does not exist
        } catch (\UnexpectedValueException $e) {
            // thrown if a file has been replaced with a folder
        } catch (\RuntimeException $e) {
            // RuntimeException thrown if a file is outside of a storage
        } catch (\InvalidArgumentException $e) {
            // thrown if file storage does not exist
        }

        return $this->tag->render();
    }
}
