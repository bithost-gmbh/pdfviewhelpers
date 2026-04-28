.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

.. _upgrade-guides-3-to-4:

Upgrading from 3.x.x to 4.x.x
=============================

HtmlViewHelper children escaping
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
The ``HtmlViewHelper`` has been changed to escape children now (``$escapeChildren = true``).
If you used to pass variables containing escaped HTML to the ViewHelper without applying ``f:format.html``, this behaviour will break.

::

	<pdf:html>{someRichText}</pdf:html>

You will have to change this to apply ``f:format.html`` or ``f:format.raw`` to the variable:

::

	<pdf:html>{someRichText -> f:format.html}</pdf:html>
	<pdf:html>{someRichText -> f:format.raw}</pdf:html>

Fluid 5
^^^^^^^

All ViewHelpers have been adapted to support Fluid v5.
This might lead to breaking changes in custom ViewHelpers that extend ``EXT:pdfviewhelpers`` ViewHelpers.
Breaking changes are listed in the Fluid documentation:

`Changelog 5.x <https://docs.typo3.org/permalink/fluid:changelog-5-x>`_
