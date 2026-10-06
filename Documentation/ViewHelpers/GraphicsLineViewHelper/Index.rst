.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

Graphics LineViewHelper
-----------------------

This ViewHelper allows to render a simple line. By default it renders a horizontal line matching the current page width.
It is however also possible to specify start and end point of the line explicitly.
The style attribute supports the keys ``width``, ``color``, ``cap`` (butt, round, square), ``join`` (miter, round, bevel), ``dash`` (e.g. ``3,1``) and ``phase``.

**Basic Usage**

::

	<pdf:graphics.line />

**Advanced Usage**

::

	<pdf:graphics.line
		padding="{top: 1, right: 10, bottom: 1, left: 10}"
		style="{color: '#8C8C8C', width: 0.5}" />
