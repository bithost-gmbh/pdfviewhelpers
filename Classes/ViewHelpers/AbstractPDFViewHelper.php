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
use Bithost\Pdfviewhelpers\Exception\ValidationException;
use Bithost\Pdfviewhelpers\Model\BasePDF;
use Bithost\Pdfviewhelpers\MultiColumn\ContextStack;
use Bithost\Pdfviewhelpers\Service\ConversionService;
use Bithost\Pdfviewhelpers\Service\HyphenationService;
use Bithost\Pdfviewhelpers\Service\ValidationService;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * AbstractPDFViewHelper
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
 */
abstract class AbstractPDFViewHelper extends AbstractViewHelper
{
    /**
     * Do not escape output of ViewHelpers
     *
     * @var bool
     */
    protected $escapeChildren = false;

    /**
     * Do not escape output of ViewHelpers
     *
     * @var bool
     */
    protected $escapeOutput = false;

    protected array $settings = [];
    protected ConfigurationManagerInterface $configurationManager;
    protected ValidationService $validationService;
    protected HyphenationService $hyphenationService;
    protected ConversionService $conversionService;

    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager): void
    {
        $this->configurationManager = $configurationManager;
    }

    public function injectValidationService(ValidationService $validationService): void
    {
        $this->validationService = $validationService;
    }

    public function injectHyphenationService(HyphenationService $hyphenationService): void
    {
        $this->hyphenationService = $hyphenationService;
    }

    public function injectConversionService(ConversionService $conversionService): void
    {
        $this->conversionService = $conversionService;
    }

    /**
     * @throws Exception
     */
    public function initializeObject(): void
    {
        $this->settings = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS, 'Pdfviewhelpers', 'tx_pdfviewhelpers');

        if (!is_array($this->settings) || !isset($this->settings['staticTypoScriptSetupIncluded'])) {
            throw new Exception('No pdfviewhelpers settings found. Please make sure you have included the static TypoScript template. ERROR: 1470982083', 1470982083);
        }

        $this->conversionService->setSettings($this->settings);
    }

    protected function setPDF(BasePDF $pdf): void
    {
        $this->viewHelperVariableContainer->addOrUpdate('DocumentViewHelper', 'pdf', $pdf);
        $this->hyphenationService->setPDF($pdf);
    }

    /**
     * @throws Exception
     */
    protected function getPDF(): BasePDF
    {
        if ($this->viewHelperVariableContainer->exists('DocumentViewHelper', 'pdf')) {
            return $this->viewHelperVariableContainer->get('DocumentViewHelper', 'pdf');
        }
        throw new Exception('No PDF Object found. Please use the DocumentViewHelper first in your template! ERROR: 1363682433', 1363682433);

    }

    /**
     * @throws Exception
     */
    protected function removePDF(): void
    {
        if ($this instanceof DocumentViewHelper && $this->viewHelperVariableContainer->exists('DocumentViewHelper', 'pdf')) {
            $this->viewHelperVariableContainer->remove('DocumentViewHelper', 'pdf');
        } else {
            throw new Exception('The PDF Object has not yet been created, or the function removePDF() was not called from an instance of DocumentViewHelper. ERROR: 1526021339', 1526021339);
        }
    }

    protected function pushMultiColumnContext(array $multiColumnContext): void
    {
        if (!$this->viewHelperVariableContainer->exists('MultiColumnViewHelper', 'contextStack')) {
            $contextStack = new ContextStack();
            $contextStack->push($multiColumnContext);

            $this->viewHelperVariableContainer->add('MultiColumnViewHelper', 'contextStack', $contextStack);
        } else {
            $contextStack = $this->viewHelperVariableContainer->get('MultiColumnViewHelper', 'contextStack');

            $contextStack->push($multiColumnContext);
        }
    }

    protected function popMultiColumnContext(): ?array
    {
        if ($this->viewHelperVariableContainer->exists('MultiColumnViewHelper', 'contextStack')) {
            $contextStack = $this->viewHelperVariableContainer->get('MultiColumnViewHelper', 'contextStack');

            return $contextStack->pop();
        }
        return null;

    }

    protected function getCurrentMultiColumnContext(): ?array
    {
        if ($this->viewHelperVariableContainer->exists('MultiColumnViewHelper', 'contextStack')) {
            $contextStack = $this->viewHelperVariableContainer->get('MultiColumnViewHelper', 'contextStack');
            $top = $contextStack->top();

            return is_array($top) ? $top : null;
        }
        return null;

    }

    protected function setCurrentMultiColumnContext(array $multiColumnContext): void
    {
        $this->popMultiColumnContext();
        $this->pushMultiColumnContext($multiColumnContext);
    }

    /**
     * @throws ValidationException
     */
    protected function getHyphenFileName(): string
    {
        if ($this->viewHelperVariableContainer->exists('DocumentViewHelper', 'hyphenFile')) {
            return $this->viewHelperVariableContainer->get('DocumentViewHelper', 'hyphenFile');
        }
        throw new ValidationException('No hyphenFile configured, make sure to configure a hyphenFile for the DocumentViewHelper. ERROR: 1536993844', 1536993844);

    }

    protected function getTCPDFInstallPath(?string $path): string
    {
        $reflector = new \ReflectionClass(\TCPDF::class);
        $pathParts = pathinfo($reflector->getFileName());
        $installFolder = $pathParts['dirname'];

        return rtrim($installFolder, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($path ?? '', DIRECTORY_SEPARATOR);
    }
}
