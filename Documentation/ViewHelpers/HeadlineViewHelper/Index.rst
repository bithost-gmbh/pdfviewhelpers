.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

HeadlineViewHelper
------------------

Rendering text using the settings for headlines.

**Basic Usage**
::

	<pdf:headline>Title</pdf:headline>
	<pdf:headline text="Alternative syntax"/>

**Advanced Usage**
::

	<pdf:headline
		trim="0"
		color="#333"
		fontFamily="arial"
		fontSize="22"
		fontStyle="B"
		alignment="R"
		padding="{top:1, right:0, bottom:0, left:0}"
		>Title</pdf:headline>
