.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

PageViewHelper
--------------

This ViewHelper must be placed right within a document ViewHelper. It can be used to manually add new pages.

**Basic Usage**
::

	<pdf:document>
		<pdf:page>
			<pdf:text>Page 1</pdf:text>
		</pdf:page>
		<pdf:page>
			<pdf:text>Page 2</pdf:text>
		</pdf:page>
	</pdf:document>

**Advanced Usage**
::

	<pdf:document>
		<pdf:page autoPageBreak="1" orientation="L" format="A5">
			<pdf:text>Page 1</pdf:text>
		</pdf:page>
		<pdf:page autoPageBreak="0" orientation="P" format="A3">
			<pdf:text>Page 2</pdf:text>
		</pdf:page>
	</pdf:document>
