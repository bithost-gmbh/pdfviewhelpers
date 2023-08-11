<?php

declare(strict_types=1);

namespace Bithost\Pdfviewhelpers\ViewHelpers;

/* * *
 *
 * This file is part of the "PDF ViewHelpers" Extension for TYPO3 CMS.
 *
 *  (c) 2023 Georg Ringer <gr@studiomitte.com>, Studio Mitte GmbH
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

use TYPO3\CMS\Core\Core\Environment;

class AttachPdfViewHelper extends AbstractPDFViewHelper
{
    public function initializeArguments()
    {
        $this->registerArgument('path', 'string', 'Path to the PDF', true);
        $this->registerArgument('prefixPublicPath', 'bool', 'If set, the public path is prefixed', false, false);
    }

    public function render()
    {
        $path = $this->arguments['path'];
        if ($this->arguments['prefixPublicPath']) {
            $path = Environment::getPublicPath() . '/' . ltrim($path, '/');
        }

        if (is_file($path)) {
            $pageCount = $this->getPDF()->setSourceFile($path);
            for ($pageNumbers = 1; $pageNumbers <= $pageCount; $pageNumbers++) {
                $templateId = $this->getPDF()->importPage($pageNumbers);

                $size = $this->getPDF()->getTemplateSize($templateId);
                if ($size['w'] > $size['h']) {
                    $this->getPDF()->AddPage('L', [$size['w'], $size['h']]);
                } else {
                    $this->getPDF()->AddPage('P', [$size['w'], $size['h']]);
                }

                $this->getPDF()->useTemplate($templateId, 0, 0, null, null, true);
            }
        }
    }
}
