<?php

declare(strict_types=1);

namespace Bithost\Pdfviewhelpers\Service;

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
use Bithost\Pdfviewhelpers\Exception\ValidationException;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * ConversionService
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
 */
class ConversionService implements SingletonInterface
{
    protected array $settings = [];
    protected ResourceFactory $resourceFactory;

    public function injectResourceFactory(ResourceFactory $resourceFactory): void
    {
        $this->resourceFactory = $resourceFactory;
    }

    public function convertHexToRGB(string $colorHex): array
    {
        $colorHex = str_replace("#", "", $colorHex);

        if (strlen($colorHex) == 3) {
            $r = hexdec(substr($colorHex, 0, 1) . substr($colorHex, 0, 1));
            $g = hexdec(substr($colorHex, 1, 1) . substr($colorHex, 1, 1));
            $b = hexdec(substr($colorHex, 2, 1) . substr($colorHex, 2, 1));
        } else {
            $r = hexdec(substr($colorHex, 0, 2));
            $g = hexdec(substr($colorHex, 2, 2));
            $b = hexdec(substr($colorHex, 4, 2));
        }

        return ['R' => $r, 'G' => $g, 'B' => $b];
    }

    /**
     * @throws ValidationException
     */
    public function convertSpeakingOrientationToTcpdfOrientation(string $orientation): string
    {
        switch ($orientation) {
            case 'portrait':
            case 'P':
                return 'P';
            case 'landscape':
            case 'L':
                return 'L';
            default:
                throw new ValidationException('Invalid page orientation "' . $orientation . '" provided. ERROR: 1536238253', 1536238253);
        }
    }

    /**
     * @throws ValidationException
     */
    public function convertSpeakingOutputDestinationToTcpdfOutputDestination(string $outputDestination): string
    {
        switch ($outputDestination) {
            case 'inline':
            case 'I':
                return 'I';
            case 'download':
            case 'D':
                return 'D';
            case 'file':
            case 'F':
                return 'F';
            case 'file-inline':
            case 'FI':
                return 'FI';
            case 'file-download':
            case 'FD':
                return 'FD';
            case 'email':
            case 'E':
                return 'E';
            case 'string':
            case 'S':
                return 'S';
            default:
                throw new ValidationException('Invalid output destination "' . $outputDestination . '" provided. ERROR: 1536238646', 1536238646);
        }
    }

    /**
     * @throws ValidationException
     */
    public function convertSpeakingFontStyleToTcpdfFontStyle(string $fontStyle): string
    {
        switch ($fontStyle) {
            case 'bold':
            case 'B':
                return 'B';
            case 'italic':
            case 'I':
                return 'I';
            case 'underline':
            case 'U':
                return 'U';
            case 'regular':
            case 'R':
                return '';
            case 'bold-italic':
            case 'BI':
                return 'BI';
            default:
                throw new ValidationException('Invalid font style "' . $fontStyle . '" provided. ERROR: 1536238089', 1536238089);
        }
    }

    /**
     * @throws ValidationException
     */
    public function convertSpeakingAlignmentToTcpdfAlignment(string $alignment): string
    {
        switch ($alignment) {
            case 'left':
            case 'L':
                return 'L';
            case 'center':
            case 'C':
                return 'C';
            case 'right':
            case 'R':
                return 'R';
            case 'justify':
            case 'J':
                return 'J';
            default:
                throw new ValidationException('Invalid alignment "' . $alignment . '" provided. ERROR: 1536237714', 1536237714);
        }
    }

    /**
     * @param string|int|float $width A simple integer (e.g. 100) or percentage value (e.g. 50%)
     * @param int|float $fullWidth The width that is considered to be 100%
     *
     * @throws ValidationException
     */
    public function convertSpeakingWidthToTcpdfWidth($width, $fullWidth): float
    {
        if (is_string($width) && mb_substr($width, -1) === '%') {
            $stringPercentage = rtrim($width, '%');
            $invalidWidth = !is_numeric($stringPercentage);
            $columnWidth = ((float) $stringPercentage / 100) * $fullWidth;
        } else {
            $columnWidth = $width;
            $invalidWidth = !is_numeric($columnWidth);
        }

        if ($invalidWidth) {
            throw new ValidationException('Invalid width "' . $width . '" provided. ERROR: 1536398597', 1536398597);
        }

        return (float) $columnWidth;
    }

    /**
     * @throws Exception
     */
    public function convertImageExtensionToRenderMode(string $extension): string
    {
        $settings = $this->getSettings();
        $extension = strtolower($extension);

        if (in_array($extension, $this->convertImageTypeStringToImageTypeArray($settings['config']['allowedImageTypes']['image']))) {
            return 'image';
        } elseif (in_array($extension, $this->convertImageTypeStringToImageTypeArray($settings['config']['allowedImageTypes']['imageEPS']))) {
            return 'imageEPS';
        } elseif (in_array($extension, $this->convertImageTypeStringToImageTypeArray($settings['config']['allowedImageTypes']['imageSVG']))) {
            return 'imageSVG';
        } else {
            throw new ValidationException('Image type is not supported. ERROR: 1363778014', 1363778014);
        }
    }

    /**
     * @param string|FileInterface $src
     *
     * @throws Exception
     */
    public function convertFileSrcToFileObject($src): FileInterface
    {
        $file = null;
        $previousException = null;
        $previousExceptionMessage = '';

        if ($src instanceof FileInterface) {
            $file = $src;
        } else {
            try {
                $file = $this->resourceFactory->retrieveFileOrFolderObject($src);
            } catch (\Exception $e) {
                //invalid file provided
                $previousException = $e;
                $previousExceptionMessage = ' ' . $e->getMessage();
            }
        }

        if (!($file instanceof FileInterface)) {
            throw new ValidationException("Invalid file src provided, must be either a uid, combined identifier, path/filename or implement FileInterface." . $previousExceptionMessage . " ERROR: 1536560752", 1536560752, $previousException);
        }

        return $file;
    }

    public function setSettings(array $settings): void
    {
        $this->settings = $settings;
    }

    /**
     * @throws Exception
     */
    protected function getSettings(): array
    {
        if (is_array($this->settings)) {
            return $this->settings;
        } else {
            throw new Exception('No settings found in SettingsConversionService. ERROR: 1536482194', 1536482194);
        }
    }

    protected function convertImageTypeStringToImageTypeArray(string $imageTypes): array
    {
        return explode(',', str_replace(' ', '', strtolower($imageTypes)));
    }
}
