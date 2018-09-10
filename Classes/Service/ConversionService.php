<?php

namespace Bithost\Pdfviewhelpers\Service;

/***
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
 ***/

use Bithost\Pdfviewhelpers\Exception\Exception;
use Bithost\Pdfviewhelpers\Exception\ValidationException;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * ConversionService
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Marin <esteban.marin@bithost.ch>
 */
class ConversionService implements SingletonInterface
{
    /**
     * @var array
     */
    protected $settings = null;

    /**
     * @var ResourceFactory
     */
    protected $resourceFactory = null;

    /**
     * @param ResourceFactory $resourceFactory
     */
    public function injectResourceFactory(ResourceFactory $resourceFactory)
    {
        $this->resourceFactory = $resourceFactory;
    }

    /**
     * @param string $colorHex
     *
     * @return array
     */
    public function convertHexToRGB($colorHex)
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
     * @param string $orientation
     *
     * @return string
     *
     * @throws ValidationException
     */
    public function convertSpeakingOrientationToTcpdfOrientation($orientation)
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
     * @param string $outputDestination
     *
     * @return string
     *
     * @throws ValidationException
     */
    public function convertSpeakingOutputDestinationToTcpdfOutputDestination($outputDestination)
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
     * Converts pdfviewhelper fontStyle syntax to TCPDF syntax. This function is necessary because TCPDF uses an empty
     * string to represent "regular", but we can not do this because of the settings inheritance (empty means inherit).
     *
     * @param string $fontStyle
     *
     * @return string
     *
     * @throws ValidationException
     */
    public function convertSpeakingFontStyleToTcpdfFontStyle($fontStyle)
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
            default:
                throw new ValidationException('Invalid font style "' . $fontStyle . '" provided. ERROR: 1536238089', 1536238089);
        }
    }

    /**
     * @param string $alignment
     *
     * @return string
     *
     * @throws ValidationException
     */
    public function convertSpeakingAlignmentToTcpdfAlignment($alignment)
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
     * @param string $extension
     *
     * @return string
     *
     * @throws Exception
     */
    public function convertImageExtensionToRenderMode($extension)
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
     * @param mixed $src
     *
     * @return FileInterface
     *
     * @throws Exception
     */
    public function convertFileSrcToFileObject($src)
    {
        if ($src instanceof FileInterface) {
            return $src;
        } else {
            $file = $this->resourceFactory->retrieveFileOrFolderObject($src);

            if (!($file instanceof FileInterface)) {
                throw new Exception("Invalid file src provided, must be either a uid, combined identifier, path/filename or implement FileInterface. ERROR: 1536560752", 1536560752);
            }

            return $file;
        }
    }

    /**
     * @param array $settings
     */
    public function setSettings(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @return array
     *
     * @throws Exception
     */
    public function getSettings()
    {
        if (is_array($this->settings)) {
            return $this->settings;
        } else {
            throw new Exception('No settings found in SettingsConversionService. ERROR: 1536482194', 1536482194);
        }
    }

    /**
     * @param string $imageTypes
     *
     * @return array
     */
    protected function convertImageTypeStringToImageTypeArray($imageTypes)
    {
        return explode(',', str_replace(' ', '', strtolower($imageTypes)));
    }
}
