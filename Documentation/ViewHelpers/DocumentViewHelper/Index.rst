.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

DocumentViewHelper
------------------

This ViewHelper must be the first to be used in your template and wrap all the other ViewHelpers. It is responsible for
generating the document.

**Basic Usage**
::

	<pdf:document>
		[..]
	</pdf:document>

**Advanced Usage**
::

	<pdf:document
		title="Bithost Example"
		subject="Welcome message"
		author="Bithost GmbH"
		keywords="example, test"
		outputDestination="inline"
		outputPath="example.pdf"
		sourceFile="EXT:pdfviewhelpers/Resources/Public/Examples/ExtendExistingPDFs/pdf_template.pdf"
		unit="cm"
		unicode="1"
		encoding="UTF-8"
		pdfa="1">
		[..]
	</pdf:document>
