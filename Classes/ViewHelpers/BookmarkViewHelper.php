<?php

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
use Bithost\Pdfviewhelpers\Exception\ValidationException;

/**
 * BookmarkViewHelper
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
 */
class BookmarkViewHelper extends AbstractPDFViewHelper
{
    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('text', 'string', 'The text of the bookmark.', false, '');
        $this->registerArgument('level', 'integer', 'The level of the table of content the bookmark is added to.', false, $this->settings['bookmark']['level']);
        $this->registerArgument('fontStyle', 'string', 'The font style of this bookmark.', false, $this->settings['bookmark']['fontStyle']);
        $this->registerArgument('color', 'string', 'The color of this bookmark.', false, $this->settings['bookmark']['color']);
    }

    /**
     * @return void
     *
     * @throws ValidationException
     */
    public function initialize()
    {
        parent::initialize();

        if (empty($this->arguments['text'])) {
            $this->arguments['text'] = $this->renderChildren();
        }

        if (empty($this->arguments['color'])) {
            $this->arguments['color'] = $this->settings['generalText']['color'];
        }

        if ($this->validationService->validateColor($this->arguments['color'])) {
            $this->arguments['color'] = $this->conversionService->convertHexToRGB($this->arguments['color']);
        }

        if (empty($this->arguments['fontStyle'])) {
            $this->arguments['fontStyle'] = $this->settings['generalText']['fontStyle'];
        }

        $this->arguments['fontStyle'] = $this->conversionService->convertSpeakingFontStyleToTcpdfFontStyle($this->arguments['fontStyle']);
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    public function render()
    {
        $this->getPDF()->Bookmark(
            $this->arguments['text'],
            $this->arguments['level'],
            -1,
            '',
            $this->arguments['fontStyle'],
            $this->arguments['color'],
            -1,
            ''
        );
    }
}
