<?php

declare(strict_types=1);

namespace Bithost\Pdfviewhelpers\HtmlRenderer;

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

use Bithost\Pdfviewhelpers\Exception\ValidationException;
use Psr\Container\ContainerExceptionInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * HtmlRendererLocator
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
 */
class HtmlRendererLocator
{
    private ServiceLocator $serviceLocator;

    public function __construct(ServiceLocator $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function has(string $id): bool
    {
        return $this->serviceLocator->has($id);
    }

    /**
     * @throws ValidationException
     * @throws ContainerExceptionInterface
     */
    public function get(string $id): HtmlRenderer
    {
        if ($this->serviceLocator->has($id)) {
            return $this->serviceLocator->get($id);
        }

        throw new ValidationException('No renderer found for identifier "' . $id . '". ERROR: 1680367253', 1680367253);
    }
}
