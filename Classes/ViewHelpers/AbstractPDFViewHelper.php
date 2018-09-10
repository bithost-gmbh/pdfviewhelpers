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
use Bithost\Pdfviewhelpers\Model\BasePDF;
use Bithost\Pdfviewhelpers\Service\HyphenationService;
use Bithost\Pdfviewhelpers\Service\SettingsConversionService;
use Bithost\Pdfviewhelpers\Service\ValidationService;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * AbstractPDFViewHelper
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Marin <esteban.marin@bithost.ch>
 */
abstract class AbstractPDFViewHelper extends AbstractViewHelper
{
    /**
     * Do not escape output of ViewHelpers
     *
     * @var boolean
     */
    protected $escapingInterceptorEnabled = false;

    /**
     * Do not escape output of ViewHelpers
     *
     * @var boolean
     */
    protected $escapeChildren = false;

    /**
     * Do not escape output of ViewHelpers
     *
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @var ConfigurationManagerInterface
     */
    protected $configurationManager = null;

    /**
     * @var ValidationService
     */
    protected $validationService = null;

    /**
     * @var HyphenationService
     */
    protected $hyphenationService = null;

    /**
     * @var SettingsConversionService
     */
    protected $settingsConversionService = null;

    /**
     * @param ConfigurationManagerInterface $configurationManager
     */
    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }

    /**
     * @param ValidationService $validationService
     */
    public function injectValidationService(ValidationService $validationService)
    {
        $this->validationService = $validationService;
    }

    /**
     * @param HyphenationService $hyphenationService
     */
    public function injectHyphenationService(HyphenationService $hyphenationService)
    {
        $this->hyphenationService = $hyphenationService;
    }

    /**
     * @param SettingsConversionService $settingsConversionService
     */
    public function injectSettingsConversionService(SettingsConversionService $settingsConversionService)
    {
        $this->settingsConversionService = $settingsConversionService;
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    public function initializeObject()
    {
        $this->settings = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS, 'Pdfviewhelpers', 'tx_pdfviewhelpers');

        if (!is_array($this->settings) || !isset($this->settings['staticTypoScriptSetupIncluded'])) {
            throw new Exception('No pdfviewhelpers settings found. Please make sure you have included the static TypoScript template. ERROR: 1470982083', 1470982083);
        }

        $this->settingsConversionService->setSettings($this->settings);
    }

    /**
     * @param BasePDF $pdf
     *
     * @return void
     *
     * @throws Exception
     */
    protected function setPDF(BasePDF $pdf)
    {
        if ($this instanceof DocumentViewHelper && !$this->viewHelperVariableContainer->exists('DocumentViewHelper', 'pdf')) {
            $this->viewHelperVariableContainer->add('DocumentViewHelper', 'pdf', $pdf);
            $this->hyphenationService->setPDF($pdf);
        } else {
            throw new Exception('The PDF Object has already been created, or the function setPDF() was not called from an instance of DocumentViewHelper. ERROR: 1363682312', 1363682312);
        }
    }

    /**
     * @return BasePDF
     *
     * @throws Exception
     */
    protected function getPDF()
    {
        if ($this->viewHelperVariableContainer->exists('DocumentViewHelper', 'pdf')) {
            return $this->viewHelperVariableContainer->get('DocumentViewHelper', 'pdf');
        } else {
            throw new Exception('No PDF Object found. Please use the DocumentViewHelper first in your template! ERROR: 1363682433', 1363682433);
        }
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    protected function removePDF()
    {
        if ($this instanceof DocumentViewHelper && $this->viewHelperVariableContainer->exists('DocumentViewHelper', 'pdf')) {
            $this->viewHelperVariableContainer->remove('DocumentViewHelper', 'pdf');
        } else {
            throw new Exception('The PDF Object has not yet been created, or the function removePDF() was not called from an instance of DocumentViewHelper. ERROR: 1526021339', 1526021339);
        }
    }

    /**
     * @param array $multiColumnContext
     *
     * @return void
     */
    protected function setMultiColumnContext(array $multiColumnContext)
    {
        $this->viewHelperVariableContainer->addOrUpdate('MultiColumnViewHelper', 'multiColumnContext', $multiColumnContext);
    }

    /**
     * @return array $multiColumnContext
     */
    protected function getMultiColumnContext()
    {
        if ($this->viewHelperVariableContainer->exists('MultiColumnViewHelper', 'multiColumnContext')) {
            return $this->viewHelperVariableContainer->get('MultiColumnViewHelper', 'multiColumnContext');
        } else {
            return null;
        }
    }
}
