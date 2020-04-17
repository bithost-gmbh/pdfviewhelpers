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
use TYPO3Fluid\Fluid\Core\Compiler\TemplateCompiler;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\ViewHelperNode;

/**
 * MultiColumnViewHelper
 * ATTENTION: Templates using this ViewHelper may not be compiled and thus lead to longer loading times (see method compile)
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
 */
class MultiColumnViewHelper extends AbstractPDFViewHelper
{
    /**
     * @var array
     */
    protected $childNodes = [];

    /**
     * @return void
     *
     * @throws Exception
     */
    public function initialize()
    {
        parent::initialize();

        $multiColumnContext = [];
        $multiColumnContext['pageWidth'] = $this->getPDF()->getPageWidth();
        $multiColumnContext['pageMargins'] = $this->getPDF()->getMargins();
        $multiColumnContext['pageWidthWithoutMargins'] = $multiColumnContext['pageWidth'] - $multiColumnContext['pageMargins']['right'] - $multiColumnContext['pageMargins']['left'];
        $multiColumnContext['columns'] = [];
        $multiColumnContext['columnPadding'] = [];
        $multiColumnContext['numberOfColumns'] = 0;
        $multiColumnContext['posY'] = $this->getPDF()->GetY();
        $multiColumnContext['longestColumnPage'] = 0;
        $multiColumnContext['longestColumnPosY'] = 0;
        $multiColumnContext['posX'] = $this->getPDF()->GetX();
        $multiColumnContext['currentPosX'] = $this->getPDF()->GetX();
        $multiColumnContext['startingPage'] = $this->getPDF()->getPage();

        foreach ($this->childNodes as $childNode) {
            if ($childNode instanceof ViewHelperNode
                && $childNode->getViewHelperClassName() === 'Bithost\Pdfviewhelpers\ViewHelpers\ColumnViewHelper'
            ) {
                $multiColumnContext['columns'][] = $childNode;
                $multiColumnContext['numberOfColumns']++;
            }
        }

        $multiColumnContext['defaultColumnWidth'] = $multiColumnContext['pageWidthWithoutMargins'] / $multiColumnContext['numberOfColumns'];
        $multiColumnContext['isInAColumn'] = true;

        $this->pushMultiColumnContext($multiColumnContext);
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    public function render()
    {
        $multiColumnContext = $this->getCurrentMultiColumnContext();

        /** @var ViewHelperNode $column */
        foreach ($multiColumnContext['columns'] as $column) {
            $this->getPDF()->setPage($multiColumnContext['startingPage']);
            $this->getPDF()->SetY($multiColumnContext['posY']);

            $column->evaluate($this->renderingContext);

            //get possible new multi column context
            $multiColumnContext = $this->getCurrentMultiColumnContext();

            if ($multiColumnContext['longestColumnPage'] < $this->getPDF()->getPage() ||
                ($multiColumnContext['longestColumnPage'] === $this->getPDF()->getPage() &&  $multiColumnContext['longestColumnPosY'] < $this->getPDF()->GetY())
            ) {
                $multiColumnContext['longestColumnPosY'] = $this->getPDF()->GetY();
                $multiColumnContext['longestColumnPage'] = $this->getPDF()->getPage();
            }

            $multiColumnContext['currentPosX'] += $multiColumnContext['columnWidth'] + $multiColumnContext['columnPadding']['right'];
            $this->setCurrentMultiColumnContext($multiColumnContext);
        }

        $this->getPDF()->setPage($multiColumnContext['longestColumnPage']);
        $this->getPDF()->SetY($multiColumnContext['longestColumnPosY']);
        $this->getPDF()->SetX($multiColumnContext['posX']);

        $this->popMultiColumnContext();
    }

    /**
     * @param array $childNodes
     *
     * @return void
     */
    public function setChildNodes(array $childNodes)
    {
        $this->childNodes = $childNodes;
    }

    /**
     * Disable compilation of templates using MultiColumnViewHelper because it is currently not possible
     * to access child nodes within a compiled template.
     *
     * @param string $argumentsName
     * @param string $closureName
     * @param string $initializationPhpCode
     * @param ViewHelperNode $node
     * @param TemplateCompiler $compiler
     *
     * @return string
     */
    public function compile($argumentsName, $closureName, &$initializationPhpCode, ViewHelperNode $node, TemplateCompiler $compiler)
    {
        $compiler->disable();

        return '\'\'';
    }
}
