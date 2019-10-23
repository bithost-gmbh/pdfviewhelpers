.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

ImageViewHelper
---------------

This ViewHelper renders an image given as src. As src argument you may provide a valid TYPO3 path or an object implementing TYPO3 FAL FileInterface (e.g. File or FileReference).

::

	<pdf:image src="EXT:pdfviewhelpers/Resources/Public/Example/Bithost.jpg" width="200" />
	<pdf:image src="{file}" width="50%" alignment="center" link="https://www.bithost.ch" />
	<pdf:image src="{fileReference}" width="100" alignment="right" padding="{right: 10}" fitOnPage="0" />

The Image ViewHelper supports image processing using the ``processingInstructions`` attribute.
The attribute value is directly passed to ``ImageService->applyProcessingInstructions``, thus it supports all configurations supported by this method.

::

	<pdf:image src="{file}" processingInstructions="{maxWidth: '600'}" />
	<pdf:image src="{file}" processingInstructions="{width: '200c'}" width="200" />
