<?php

declare(strict_types=1);

namespace Bithost\Pdfviewhelpers\Model;

/***
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
 ***/

/**
 * FullFeatureShowCase
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
 */
class FullFeatureShowCase extends BasePDF
{
    /**
     * @inheritdoc
     */
    public function basePdfHeader(): void
    {
    }

    /**
     * @inheritdoc
     */
    public function basePdfFooter(): void
    {
        $this->setY(-20);
        $this->setDrawColor(140, 140, 140);
        $this->Line(15, $this->y, $this->w - 15, $this->y);

        $this->setY(-17);
        $this->setTextColor(140, 140, 140);
        $this->setFontSize(11);
        $this->Cell($this->w - 15, 10, 'Page ' . $this->getAliasNumPage() . ' of ' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 1);
    }
}
