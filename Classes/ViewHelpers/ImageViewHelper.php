<?php

namespace Bithost\Pdfviewhelpers\ViewHelpers;

/* * *
 *
 * This file is part of the "PDF ViewHelpers" Extension for TYPO3 CMS.
 *
 *  (c) 2016 Markus Mächler <markus.maechler@bithost.ch>, Bithost GmbH
 *           Esteban Marin <esteban.marin@bithost.ch>, Bithost GmbH
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
use TYPO3\CMS\Extbase\Service\ImageService;

/**
 * ImageViewHelper
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Marin <esteban.marin@bithost.ch>
 */
class ImageViewHelper extends AbstractContentElementViewHelper
{
    /**
     * @var ImageService
     */
    protected $imageService;

    /**
     * @param ImageService $imageService
     */
    public function injectImageService(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('src', 'mixed', '', true, null);
        $this->registerArgument('link', 'string', '', false, null);
        $this->registerArgument('alignment', 'string', '', false, $this->settings['image']['alignment']);
        $this->registerArgument('padding', 'array', '', false, []);
        $this->registerArgument('processingInstructions', 'array', '', false, $this->settings['image']['processingInstructions']);
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    public function initialize()
    {
        parent::initialize();

        $this->arguments['alignment'] = $this->conversionService->convertSpeakingAlignmentToTcpdfAlignment($this->arguments['alignment']);
        $this->arguments['padding'] = array_merge($this->settings['image']['padding'], $this->arguments['padding']);
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    public function render()
    {
        $this->initializeMultiColumnSupport();

        $imageFile = $this->conversionService->convertFileSrcToFileObject($this->arguments['src']);
        $processedImage = $imageFile;

        if (!empty($this->arguments['processingInstructions'])) {
            $processedImage = $this->imageService->applyProcessingInstructions($imageFile, $this->arguments['processingInstructions']);
        }

        $src = '@' . $processedImage->getContents();
        $extension = $processedImage->getExtension();

        $multiColumnContext = $this->getCurrentMultiColumnContext();
        $isInAColumn = is_array($multiColumnContext) && $multiColumnContext['isInAColumn'];
        $initialMargins = $this->getPDF()->getMargins();
        $this->arguments['posY'] += $this->arguments['padding']['top'];

        if ($isInAColumn) {
            $marginLeft = $this->arguments['posX'] + $this->arguments['padding']['left'];
            $marginRight =  $this->getPDF()->getPageWidth() - $marginLeft - $multiColumnContext['columnWidth'] + $this->arguments['padding']['right'];
        } else {
            $marginLeft = $this->arguments['posX'] + $this->arguments['padding']['left'];
            $marginRight = $initialMargins['right'] + $this->arguments['padding']['right'];
        }

        $this->getPDF()->SetMargins($marginLeft, $initialMargins['top'], $marginRight);

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
                    true,
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
                    true,
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
                    true
                );
                break;
        }

        $this->getPDF()->SetMargins($initialMargins['left'], $initialMargins['top'], $initialMargins['right']);
        $this->getPDF()->SetY($this->getPDF()->getImageRBY() + $this->arguments['padding']['bottom']);
    }
}
