.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


TextViewHelper
--------------

Rendering text using the settings for text.
It is possible to easily define different default styles and apply them using the ``type`` attribute, see chapter :ref:`Text Types <text-types>`.

**Basic Usage**

::

	<pdf:text>Title</pdf:text>
	<pdf:text text="Alternative syntax"/>

**Advanced Usage**

::

	<pdf:text
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
		paragraphLineFeed="1"
		autoHyphenation="1"
		padding="{top: 1, right: 0, bottom: 0, left: 0}"
		width="80%"
		>Title</pdf:text>
