.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


ListViewHelper
--------------

Rendering a list given as a one dimensional array.

**Basic Usage**
::

	<pdf:list listElements="{0: 'Websites using TYPO3', 1: 'Application Development', 2: 'Mobile Apps', 3: 'Hosting'}"/>
	<pdf:list listElements="{someArrayProperty}"/>

**Advanced Usage**
::

	<pdf:list
		trim="1"
		removeDoubleWhitespace="1"
		color="#999999"
		fontFamily="arial"
		fontSize="14"
		fontStyle="B"
		alignment="C"
		autoHyphenation="1"
		padding="{top:1, right:0, bottom:0, left:0}"

		bulletColor="#333"
		bulletImageSrc="EXT:pdfviewhelpers/some/path/image.png"
		bulletSize="2.5"

		listElements="{0: 'Websites using TYPO3', 1: 'Application Development', 2: 'Mobile Apps', 3: 'Hosting'}"
		/>
