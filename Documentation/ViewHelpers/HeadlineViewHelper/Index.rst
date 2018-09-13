.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

HeadlineViewHelper
------------------

Rendering text using the settings for headlines.
It is possible to easily define different default styles and apply them using the ``type`` attribute, see chapter :ref:`Text Types <text-types>`.

**Basic Usage**
::

	<pdf:headline>Title</pdf:headline>
	<pdf:headline text="Alternative syntax"/>

**Advanced Usage**
::

	<pdf:headline
		trim="0"
		removeDoubleWhitespace="1"
		color="#333"
		fontFamily="arial"
		fontSize="22"
		fontStyle="bold"
		lineHeight="1.5"
		characterSpacing="0.2"
		alignment="right"
		paragraphSpacing="0"
		autoHyphenation="1"
		padding="{top: 1, right: 0, bottom: 0, left: 0}"
		>Title</pdf:headline>
