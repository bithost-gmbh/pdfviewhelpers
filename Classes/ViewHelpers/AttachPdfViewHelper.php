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

use TYPO3\CMS\Core\Utility\GeneralUtility;

class AttachPdfViewHelper extends AbstractPDFViewHelper
{
    public function initializeArguments()
    {
        $this->registerArgument('path', 'string', 'Path to the PDF', true);
    }

    public function render()
    {
        $path = GeneralUtility::getFileAbsFileName($this->arguments['path']);

        if (is_file($path)) {
            // Store source file possibly defined on document level in order to restore it later
            $previousSourceFile = $this->getPDF()->getSourceFile();
            $pageCount = $this->getPDF()->setSourceFile($path);

            for ($pageNumbers = 1; $pageNumbers <= $pageCount; $pageNumbers++) {
                $templateId = $this->getPDF()->importPage($pageNumbers);
                $size = $this->getPDF()->getTemplateSize($templateId);

                $this->getPDF()->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $this->getPDF()->useTemplate($templateId, 0, 0, null, null, true);
            }

            if ($previousSourceFile !== null) {
                $this->getPDF()->setSourceFile($previousSourceFile);
            }
        }
    }
}
