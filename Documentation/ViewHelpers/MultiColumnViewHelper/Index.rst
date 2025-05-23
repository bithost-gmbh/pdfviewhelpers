.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

MultiColumnViewHelper / ColumnViewHelper
----------------------------------------

These ViewHelpers have to be used together in order to generate a multi column layout. By default all columns are of equal width.
It is however possible to specify the ``width`` of a column with an absolute or percentage value. In addition it possible to set a ``padding`` value for each column.

**Important:** The parsing of the Fluid template can not be cached when these ViewHelpers are used. This can lead to a significant loss in performance.


**Basic Usage**

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

**Advanced Usage**

::

	<pdf:multiColumn>
		<pdf:column width="60%" padding="{right: 2}">
			<pdf:text>Column 1</pdf:text>
		</pdf:column>
		<pdf:column width="20%" padding="{right: 2}">
			<pdf:text>Column 2</pdf:text>
		</pdf:column>
		<pdf:column width="20%">
			<pdf:text>Column 3</pdf:text>
		</pdf:column>
	</pdf:multiColumn>

	<pdf:multiColumn>
		<pdf:column width="100">
			<pdf:text>Column 1</pdf:text>
		</pdf:column>
		<pdf:column width="45">
			<pdf:text>Column 2</pdf:text>
		</pdf:column>
		<pdf:column width="45">
			<pdf:text>Column 3</pdf:text>
		</pdf:column>
	</pdf:multiColumn>

