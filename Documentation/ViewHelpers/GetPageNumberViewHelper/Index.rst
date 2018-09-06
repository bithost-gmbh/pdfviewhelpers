.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

GetPageNumberAliasViewHelper / GetTotalNumberOfPagesAliasViewHelper
-------------------------------------------------------------------

These ViewHelpers return alias strings to the current page number as well as the total number of pages. Please note
that they do not return an integer value, but a string that is replaced by the correct number at the end of the PDF
generation. This may lead to alignment errors when trying to align text including these ViewHelpers right or centered.

::

	{pdf:getPageNumberAlias()}
	{pdf:getTotalNumberOfPagesAlias()}