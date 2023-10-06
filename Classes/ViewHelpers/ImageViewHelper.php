<?php

declare(strict_types=1);

namespace Bithost\Pdfviewhelpers\ViewHelpers;

/* * *
 *
 * This file is part of the "PDF ViewHelpers" Extension for TYPO3 CMS.
 *
 *  (c) 2016 Markus Mächler <markus.maechler@bithost.ch>, Bithost GmbH
 *           Esteban Gehring <esteban.gehring@bithost.ch>, Bithost GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * * */

use Bithost\Pdfviewhelpers\Exception\Exception;
use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Extbase\Service\ImageService;

/**
 * ImageViewHelper
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
 */
class ImageViewHelper extends AbstractContentElementViewHelper
{
    protected ImageService $imageService;

    public function injectImageService(ImageService $imageService): void
    {
        $this->imageService = $imageService;
    }

    /**
     * @inheritDoc
     */
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('src', 'mixed', 'The source of the image, can be a TYPO3 path, a File or FileReference object.', true, null);
        $this->registerArgument('link', 'string', 'The link added to the image.', false, null);
        $this->registerArgument('alignment', 'string', 'The alignment of the image if it does not fill up the full width.', false, $this->settings['image']['alignment']);
        $this->registerArgument('fitOnPage', 'boolean', 'If true the image will automatically be rescaled to fit on page.', false, $this->settings['image']['fitOnPage']);
        $this->registerArgument('padding', 'array', 'The image padding given as array.', false, []);
        $this->registerArgument('processingInstructions', 'array', 'The processing instructions applied to the image.', false, []);
    }

    /**
     * @inheritDoc
     *
     * @throws Exception
     */
    public function initialize()
    {
        parent::initialize();

        $this->arguments['alignment'] = $this->conversionService->convertSpeakingAlignmentToTcpdfAlignment($this->arguments['alignment']);
        $this->arguments['padding'] = array_merge($this->settings['image']['padding'] ?? [], $this->arguments['padding']);
        $this->arguments['processingInstructions'] = array_merge($this->settings['image']['processingInstructions'] ?? [], $this->arguments['processingInstructions']);
    }

    /**
     * @throws Exception
     */
    public function render(): void
    {
        $this->initializeMultiColumnSupport();

        $imageFile = $this->conversionService->convertFileSrcToFileObject($this->arguments['src']);
        $processedImage = $this->processImage($imageFile, $this->arguments['processingInstructions']);

        $src = '@' . $processedImage->getContents();
        $extension = $processedImage->getExtension();

        $multiColumnContext = $this->getCurrentMultiColumnContext();
        $isInAColumn = is_array($multiColumnContext) && ($multiColumnContext['isInAColumn'] ?? false);
        $initialMargins = $this->getPDF()->getMargins();
        $this->arguments['posY'] += $this->arguments['padding']['top'];

        if ($isInAColumn) {
            $marginLeft = $this->arguments['posX'] + $this->arguments['padding']['left'];
            $marginRight =  $this->getPDF()->getPageWidth() - $marginLeft - $multiColumnContext['columnWidth'] + $this->arguments['padding']['right'];
        } else {
            $marginLeft = $this->arguments['posX'] + $this->arguments['padding']['left'];
            $marginRight = $initialMargins['right'] + $this->arguments['padding']['right'];
        }

        $this->getPDF()->setMargins($marginLeft, $initialMargins['top'], $marginRight);

        switch ($this->conversionService->convertImageExtensionToRenderMode($extension)) {
            case 'image':
                $this->getPDF()->Image(
                    $src,
                    $this->arguments['posX'],
                    $this->arguments['posY'],
                    $this->arguments['width'],
                    $this->arguments['height'],
                    $extension,
                    $this->arguments['link'],
                    '',
                    false,
                    300,
                    $this->arguments['alignment'],
                    false,
                    false,
                    0,
                    true,
                    false,
                    $this->arguments['fitOnPage'],
                    false
                );
                break;
            case 'imageEPS':
                $this->getPDF()->ImageEps(
                    $src,
                    $this->arguments['posX'],
                    $this->arguments['posY'],
                    $this->arguments['width'],
                    $this->arguments['height'],
                    $this->arguments['link'],
                    true,
                    '',
                    $this->arguments['alignment'],
                    0,
                    $this->arguments['fitOnPage'],
                    false
                );
                break;
            case 'imageSVG':
                $this->getPDF()->ImageSVG(
                    $src,
                    $this->arguments['posX'],
                    $this->arguments['posY'],
                    $this->arguments['width'],
                    $this->arguments['height'],
                    $this->arguments['link'],
                    '',
                    $this->arguments['alignment'],
                    0,
                    $this->arguments['fitOnPage']
                );
                break;
        }

        $this->getPDF()->setMargins($initialMargins['left'], $initialMargins['top'], $initialMargins['right']);
        $this->getPDF()->setY($this->getPDF()->getImageRBY() + $this->arguments['padding']['bottom']);
    }

    protected function processImage(FileInterface $imageFile, array $processingInstructions): FileInterface
    {
        $imageFileCrop = $imageFile->hasProperty('crop') && $imageFile->getProperty('crop') ? json_decode($imageFile->getProperty('crop'), true) : [];

        if (!empty($processingInstructions) || !empty($imageFileCrop)) {
            if (isset($processingInstructions['crop'])) {
                $argumentsCrop = is_array($processingInstructions['crop']) ? $processingInstructions['crop'] : json_decode($processingInstructions['crop'] ?? '', true);
                $argumentsCrop = is_array($argumentsCrop) ? $argumentsCrop : [];
            } else {
                $argumentsCrop = [];
            }
            $crop = array_merge($imageFileCrop, $argumentsCrop);

            $cropVariantCollection = CropVariantCollection::create((string) json_encode($crop));
            $cropVariant = $processingInstructions['cropVariant'] ?? 'default';
            $cropArea = $cropVariantCollection->getCropArea($cropVariant);

            unset($processingInstructions['cropVariant']);

            if ($cropArea->isEmpty()) {
                unset($processingInstructions['crop']);
            } else {
                $processingInstructions['crop'] = $cropArea->makeAbsoluteBasedOnFile($imageFile);
            }

            if (!empty($processingInstructions)) {
                return $this->imageService->applyProcessingInstructions($imageFile, $processingInstructions);
            }
        }

        return $imageFile;
    }
}
