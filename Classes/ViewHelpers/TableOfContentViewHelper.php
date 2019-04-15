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
 * TableOfContentViewHelper
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Marin <esteban.marin@bithost.ch>
 */
class TableOfContentViewHelper extends AbstractPDFViewHelper
{
    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('page', 'integer', '', false, $this->settings['tableOfContent']['page']);
        $this->registerArgument('numbersFont', 'string', '', false, $this->settings['tableOfContent']['numbersFont']);
        $this->registerArgument('filter', 'string', '', false, $this->settings['tableOfContent']['filter']);
        $this->registerArgument('name', 'string', '', false, $this->settings['tableOfContent']['name']);
        $this->registerArgument('htmlMode', 'boolean', '', false, $this->settings['tableOfContent']['htmlMode']);
        $this->registerArgument('fontFamily', 'string', '', false, $this->settings['tableOfContent']['fontFamily']);
        $this->registerArgument('fontSize', 'integer', '', false, $this->settings['tableOfContent']['fontSize']);
        $this->registerArgument('lineHeight', 'float', '', false, $this->settings['tableOfContent']['lineHeight']);
        $this->registerArgument('characterSpacing', 'float', '', false, $this->settings['tableOfContent']['characterSpacing']);
        $this->registerArgument('padding', 'array', '', false, []);
    }

    public function initialize()
    {
        parent::initialize();

        if (empty($this->arguments['numbersFont'])) {
            $this->arguments['numbersFont'] = $this->settings['generalText']['fontFamily'];
        }

        if (empty($this->arguments['fontFamily'])) {
            $this->arguments['fontFamily'] = $this->settings['generalText']['fontFamily'];
        }

        if (empty($this->arguments['fontSize'])) {
            $this->arguments['fontSize'] = $this->settings['generalText']['fontSize'];
        }

        if (empty($this->arguments['lineHeight'])) {
            $this->arguments['lineHeight'] = $this->settings['generalText']['lineHeight'];
        }

        if (empty($this->arguments['characterSpacing'])) {
            $this->arguments['characterSpacing'] = $this->settings['generalText']['characterSpacing'];
        }

        $this->arguments['padding'] = array_merge($this->settings['tableOfContent']['padding'], $this->arguments['padding']);

        $this->viewHelperVariableContainer->addOrUpdate('TableOfContentViewHelper', 'bookmarkTemplates', []);
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    public function render()
    {
        $this->getPDF()->SetFontSize($this->arguments['fontSize']);
        $this->getPDF()->SetFont($this->arguments['fontFamily']);
        $this->getPDF()->setCellPaddings($this->arguments['padding']['left'], $this->arguments['padding']['top'], $this->arguments['padding']['right'], $this->arguments['padding']['bottom']);
        $this->getPDF()->setCellHeightRatio($this->settings['generalText']['lineHeight']);
        $this->getPDF()->setFontSpacing($this->arguments['characterSpacing']);

        if ($this->arguments['htmlMode']) {
            $this->renderChildren();
            $bookmarkTemplates = $this->viewHelperVariableContainer->get('TableOfContentViewHelper', 'bookmarkTemplates');

            if (empty($bookmarkTemplates)) {
                throw new Exception('No html bookmark templates found, please add templates using the HtmlBookmarkTemplateViewHelper. ERROR: 1555308328', 1555308328);
            }

            $this->getPDF()->addHTMLTOC($this->arguments['page'], $this->arguments['name'], $bookmarkTemplates);
        } else {
            $this->getPDF()->addTOC($this->arguments['page'], $this->arguments['numbersFont'], $this->arguments['filter'], $this->arguments['name']);
        }
    }
}
