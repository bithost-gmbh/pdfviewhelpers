.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

BookmarkViewHelper
------------------

This ViewHelper allows to set a custom bookmark that will add an entry to the table of content.
Text rendered with a ``HeadlineViewHelper`` can be added to the table of content automatically by setting ``plugin.tx_pdfviewhelpers.settings.headline.addToTableOfContent = 1``.

**Basic Usage**

::

	 <pdf:bookmark text="Adding custom bookmark" />
	 <pdf:bookmark>Adding custom bookmark</pdf:bookmark>

**Advanced Usage**

::

	<pdf:bookmark text="Adding custom bookmark" level="1" fontStyle="bold" color="#ff642c" />