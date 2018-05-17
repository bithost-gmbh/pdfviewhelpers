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
use TYPO3\CMS\Fluid\Core\Parser\SyntaxTree\AbstractNode;
use TYPO3\CMS\Fluid\Core\Parser\SyntaxTree\ViewHelperNode;
use TYPO3\CMS\Fluid\Core\Compiler\TemplateCompiler;

/**
 * MultiColumnViewHelper
 * ATTENTION: Templates using this ViewHelper may not be compiled and thus lead to longer loading times (see method compile)
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Marin <esteban.marin@bithost.ch>
 */
class MultiColumnViewHelper extends AbstractPDFViewHelper implements \TYPO3\CMS\Fluid\Core\ViewHelper\Facets\ChildNodeAccessInterface
{
    /**
     * @var array
     */
    protected $childNodes = [];

    /**
     * @var array
     */
    private $multiColumnContext = [];

    /**
     * @return void
     */
    public function initialize()
    {
        $this->multiColumnContext['pageWidth'] = $this->getPDF()->getPageWidth();
        $this->multiColumnContext['pageMargins'] = $this->getPDF()->getMargins();
        $this->multiColumnContext['pageWidthWithoutMargins'] = $this->multiColumnContext['pageWidth'] - $this->multiColumnContext['pageMargins']['right'] - $this->multiColumnContext['pageMargins']['left'];
        $this->multiColumnContext['numberOfColumns'] = 0;
        $this->multiColumnContext['posY'] = $this->getPDF()->GetY();
        $this->multiColumnContext['longestColumnPosY'] = 0;
        $this->multiColumnContext['posX'] = $this->getPDF()->GetX();
        $this->multiColumnContext['currentPosX'] = $this->getPDF()->GetX();
        $this->multiColumnContext['startingPage'] = $this->getPDF()->getPage();

        foreach ($this->childNodes as $childNode) {
            if ($childNode instanceof \TYPO3\CMS\Fluid\Core\Parser\SyntaxTree\ViewHelperNode && $childNode->getViewHelperClassName() === 'Bithost\Pdfviewhelpers\ViewHelpers\ColumnViewHelper'
            ) {
                $this->multiColumnContext['columns'][] = $childNode;
                $this->multiColumnContext['numberOfColumns']++;
            }
        }
        $this->multiColumnContext['columnWidth'] = $this->multiColumnContext['pageWidthWithoutMargins'] / $this->multiColumnContext['numberOfColumns'];
        $this->multiColumnContext['isInAColumn'] = true;
    }

    /**
     * @return void
     */
    public function render()
    {
        $this->setMultiColumnContext($this->multiColumnContext);

        /** @var ViewHelperNode $column */
        foreach ($this->multiColumnContext['columns'] as $column) {
            $this->multiColumnContext = $this->getMultiColumnContext();

            $this->getPDF()->setPage($this->multiColumnContext['startingPage']);
            $this->getPDF()->SetY($this->multiColumnContext['posY']);

            $column->evaluate($this->renderingContext);

            if ($this->multiColumnContext['longestColumnPosY'] < $this->getPDF()->GetY()) {
                $this->multiColumnContext['longestColumnPosY'] = $this->getPDF()->GetY();
            }

            $this->multiColumnContext['currentPosX'] += $this->multiColumnContext['columnWidth'];
            $this->setMultiColumnContext($this->multiColumnContext);
        }

        $this->multiColumnContext['isInAColumn'] = false;

        $this->getPDF()->SetY($this->multiColumnContext['longestColumnPosY']);
        $this->getPDF()->SetX($this->multiColumnContext['posX']);
        $this->setMultiColumnContext($this->multiColumnContext);
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
     * @param array $multiColumnContext
     *
     * @return void
     */
    private function setMultiColumnContext(array $multiColumnContext)
    {
        $this->viewHelperVariableContainer->addOrUpdate('MultiColumnViewHelper', 'multiColumnContext', $multiColumnContext);
    }

    /**
     * @return array $multiColumnContext
     *
     * @throws Exception
     */
    private function getMultiColumnContext()
    {
        if ($this->viewHelperVariableContainer->exists('MultiColumnViewHelper', 'multiColumnContext')) {
            return $this->viewHelperVariableContainer->get('MultiColumnViewHelper', 'multiColumnContext');
        } else {
            throw new Exception('No multiColumnContext found! ERROR: 1363872784', 1363872784);
        }
    }

    /**
     * Disable compilation of templates using MultiColumnViewHelper because it is currently not possible
     * to access child nodes within a compiled template.
     *
     * @param string $argumentsName
     * @param string $closureName
     * @param string $initializationPhpCode
     * @param AbstractNode $node
     * @param TemplateCompiler $compiler
     *
     * @return string
     */
    public function compile($argumentsName, $closureName, &$initializationPhpCode, AbstractNode $node, TemplateCompiler $compiler)
    {
        $compiler->disable();

        return '\'\'';
    }
}
