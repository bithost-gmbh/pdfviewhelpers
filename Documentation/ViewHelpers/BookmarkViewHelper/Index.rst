.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

BookmarkViewHelper
------------------

This ViewHelper allows to set a custom bookmark that will add an entry to the table of content. Note that the HeadlineViewHelper automatically adds headlines to the table of content.

**Basic Usage**
::

	 <pdf:bookmark text="Adding custom bookmark" />
	 <pdf:bookmark>Adding custom bookmark</pdf:bookmark>

It is possible to use these ViewHelpers to position elements relatively when used together with a math ViewHelper.
The following example requires the extension vhs to be installed.

**Advanced Usage**
::

	<pdf:bookmark text="Adding custom bookmark" level="1" fontStyle="bold" color="#ff642c" />