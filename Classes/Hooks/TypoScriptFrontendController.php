<?php

namespace Bithost\Pdfviewhelpers\Hooks;

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

use TYPO3\CMS\Core\SingletonInterface;

/**
 * TypoScriptFrontendController
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Marin <esteban.marin@bithost.ch>
 */
class TypoScriptFrontendController implements SingletonInterface
{
    /**
     * Prevent any output when a pdf is rendered, especially any headers being set!
     *
     * @param    array $params Parameters from frontend
     * @param    object $ref TSFE object
     * @return    void
     */
    public function isOutputting(&$params, $ref)
    {
        if (isset($params['pObj']->applicationData['tx_pdfviewhelpers']['pdfOutput'])
            && $params['pObj']->applicationData['tx_pdfviewhelpers']['pdfOutput'] === true
        ) {
            $params['enableOutput'] = false;
        }
    }
}
