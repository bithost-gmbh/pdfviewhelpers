.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

Graphics LineViewHelper
-----------------------

This ViewHelper allows to render a simple line. By default it renders a horizontal line matching the current page width.
It is however also possible to specify start and end point of the line explicitly.
Please see https://tcpdf.org/examples/example_012/ to find out what values can be used on the style attribute.

**Basic Usage**
::

	<pdf:graphics.line />

**Advanced Usage**
::

	<pdf:graphics.line
		padding="{top:1, right:0, bottom:0, left:0}"
		style="{color: '#8C8C8C', width: 0.5}" />
