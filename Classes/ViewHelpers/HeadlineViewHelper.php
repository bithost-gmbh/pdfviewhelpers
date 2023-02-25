<?php

declare(strict_types=1);

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

/**
 * HeadlineViewHelper
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
 */
class HeadlineViewHelper extends AbstractTextViewHelper
{
    protected function getSettingsKey(): string
    {
        return 'headline';
    }

    /**
     * @inheritDoc
     */
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('addToTableOfContent', 'boolean', 'If true this headline is added to the table of content.', false, $this->settings['headline']['addToTableOfContent']);
        $this->registerArgument('tableOfContentLevel', 'integer', 'The level this headline is added to in the table of content.', false, $this->settings['headline']['tableOfContentLevel']);
    }

    /**
     * @inheritDoc
     *
     * @throws Exception
     */
    public function initialize()
    {
        parent::initialize();

        if ($this->arguments['addToTableOfContent']) {
            $this->getPDF()->Bookmark(
                $this->arguments['text'],
                $this->arguments['tableOfContentLevel'],
                -1,
                '',
                '',
                $this->arguments['color']
            );
        }
    }
}
