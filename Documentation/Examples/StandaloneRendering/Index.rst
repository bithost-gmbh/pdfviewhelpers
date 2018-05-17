.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

.. _standalonerendering:

Standalone Rendering
====================

It is possible to render a PDF document as standalone view. That is especially useful if you want to attach the document to an email or you want to generate multiple PDFs in one request.

.. _standalonerendering_php:

PHP
---

::

	<?php
	$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
	$standaloneView = $objectManager->get(\TYPO3\CMS\Fluid\View\StandaloneView::class);
	$templatePath = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName('EXT:pdfviewhelpers/Resources/Public/Examples/BasicUsage/Bithost.html');

	$standaloneView->setFormat('html');
	$standaloneView->setTemplatePathAndFilename($templatePath);

	$pdf = $standaloneView->render(); //return pdf as string, or simply save to file system


.. _standalonerendering_fluidreturnstring:

Fluid Template - return String
------------------------------

::

	{namespace pdf=Bithost\Pdfviewhelpers\ViewHelpers}

	<pdf:document outputDestination="S">
		<pdf:page>
			<pdf:text>Your content</pdf:text>
		</pdf:page>
	</pdf:document>


.. _standalonerendering_fluidsavefile:

Fluid Template - save file
--------------------------

::

	{namespace pdf=Bithost\Pdfviewhelpers\ViewHelpers}

	<pdf:document outputDestination="F" outputPath="fileadmin/document.pdf">
		<pdf:page>
			<pdf:text>Your content</pdf:text>
		</pdf:page>
	</pdf:document>