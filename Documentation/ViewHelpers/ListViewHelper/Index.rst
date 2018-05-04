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
		color="#999999"
		fontFamily="arial"
		fontSize="14"
		fontStyle="B"
		alignment="C"
		autoHyphenation="1"
		listElements="{0: 'Websites using TYPO3', 1: 'Application Development', 2: 'Mobile Apps', 3: 'Hosting'}"
		/>
