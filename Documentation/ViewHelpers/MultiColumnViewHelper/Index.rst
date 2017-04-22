.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

MultiColumnViewHelper / ColumnViewHelper
----------------------------------------

These ViewHelpers have to be used together in order to generate a multi column layout. Columns are always of equal width.

**Important:** The parsing of the Fluid template can not be cached when these ViewHelpers are used. This can lead to a significant loss in performance.

::

	<pdf:multiColumn>
		<pdf:column>
			<pdf:text>Column 1</pdf:text>
		</pdf:column>
		<pdf:column>
			<pdf:text>Column 2</pdf:text>
		</pdf:column>
		<pdf:column>
			<pdf:text>Column 3</pdf:text>
		</pdf:column>
	</pdf:multiColumn>
