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

use TYPO3\CMS\Core\SingletonInterface;

/**
 * OutputService
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
 */
class OutputService implements SingletonInterface
{
    protected bool $isOutputDestinationOut = false;

    protected bool $disableCache = true;

    public function shouldDisablePageCache(): bool
    {
        return $this->isOutputDestinationOut() && $this->getDisableCache();
    }

    public function isOutputDestinationOut(): bool
    {
        return $this->isOutputDestinationOut;
    }

    public function setIsOutputDestinationOut(bool $isOutputDestinationOut): void
    {
        $this->isOutputDestinationOut = $isOutputDestinationOut;
    }

    public function getDisableCache(): bool
    {
        return $this->disableCache;
    }

    public function setDisableCache(bool $disableCache): void
    {
        $this->disableCache = $disableCache;
    }
}
