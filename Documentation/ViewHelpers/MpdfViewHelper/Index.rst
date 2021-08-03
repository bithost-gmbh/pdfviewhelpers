.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

MpdfViewHelper
--------------

Rendering html content using mPDF (https://github.com/mpdf/mpdf) for extended HTML support. Please note that this ViewHelper does not perfectly
integrate with all the other ViewHelpers and settings as it relies on a different PDF generator.

**Basic Usage**
::

	<pdf:mpdf>
		<h1>Some html headline</h1>
		<p>Lorem ipsum</p>
	</pdf:mpdf>

All aspects of PDF generation can be controlled setting the configuration attribute, which is passed to the mPDF constructor.
See https://mpdf.github.io/configuration/configuration-v7-x.html and https://mpdf.github.io/reference/mpdf-variables/overview.html

**Advanced Usage**
::

	<pdf:mpdf
		styleSheet="EXT:yourext/Resources/Public/Css/pdf.css"
		autoHyphenation="1"
		padding="{bottom: 5}"
		configuration="{mode: 'utf-8', }">

		<style>
			h1 {
				color: #ff642c;
			}
		</style>

		<h1>Some html headline</h1>
		<p style="color: #3a718a;">Lorem ipsum</p>

		{someAdditionalRichText}

	</pdf:mpdf>
