﻿.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

HtmlViewHelper
--------------

Rendering any html content using TCPDF's method ``writeHTML``. The default text settings are those from generalText.
It is possible to include a css style tag and also inline styles. This ViewHelper is especially useful for rendering Rich Text.

Please note that the use of CSS within a style tag can lead to parsing issues with Fluid, use an external style sheet in that case.

**Basic Usage**

::

	<pdf:html>
		<h1>Some html headline</h1>
		<p>Lorem ipsum</p>
	</pdf:html>

**Advanced Usage**

::

	<pdf:html styleSheet="fileadmin/template/pdf_styles.css" autoHyphenation="1" padding="{bottom: 5}" listIndentWidth="0">
		<style>
			h1 {
				color: #ff642c;
			}
		</style>

		<h1>Some html headline</h1>
		<p style="color: #3a718a;">Lorem ipsum</p>

		{someAdditionalRichText}
	</pdf:html>
