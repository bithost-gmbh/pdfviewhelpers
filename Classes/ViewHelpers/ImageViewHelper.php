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

/**
 * ImageViewHelper
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Marin <esteban.marin@bithost.ch>
 */
class ImageViewHelper extends AbstractContentElementViewHelper
{
    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('src', 'mixed', '', true, null);
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
        $src = '@' . $imageFile->getContents();
        $extension = $imageFile->getExtension();

        switch ($this->conversionService->convertImageExtensionToRenderMode($extension)) {
            case 'image':
                $this->getPDF()->Image($src, $this->arguments['posX'], $this->arguments['posY'], $this->arguments['width'], $this->arguments['height'], '', '', '', false, 300, '', false, false, 0, false, false, true, false);
                break;
            case 'imageEPS':
                $this->getPDF()->ImageEps($src, $this->arguments['posX'], $this->arguments['posY'], $this->arguments['width'], $this->arguments['height'], '', true, '', '', 0, true, false);
                break;
            case 'imageSVG':
                $this->getPDF()->ImageSVG($src, $this->arguments['posX'], $this->arguments['posY'], $this->arguments['width'], $this->arguments['height'], '', '', '', 0, true);
                break;
        }

        $this->getPDF()->SetY($this->getPDF()->getImageRBY());
    }
}
