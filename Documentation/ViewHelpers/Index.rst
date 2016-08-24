.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _viewhelpers:

ViewHelpers
===========


.. _documentviewhelper:

DocumentViewHelper
------------------

This ViewHelper must be to first to be used in your template and wrap all the other ViewHelpers. It is responsible for
generating the document.

::

	<pdf:document outputDestination="I" title="Bithost Example">
		[..]
	</pdf:document>

.. _pageviewhelper:

PageViewHelper
--------------

This ViewHelper must be placed right within a document ViewHelper. It can be used to manually add new pages.

::

	<pdf:document>
		<pdf:page>
			<pdf:text>Page 1</pdf:text>
		</pdf:page>
		<pdf:page>
			<pdf:text>Page 2</pdf:text>
		</pdf:page>
	</pdf:document>

.. _multicolumnviewhelper:

MultiColumnViewHelper / ColumnViewHelper
----------------------------------------

These ViewHelpers have to be used together in order to generate a multi column layout. Columns are always of equal width.

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

.. _headlineviewhelper:

HeadlineViewHelper
------------------

Rendering text using the settings for headlines.

::

	<pdf:headline>Title</pdf:headline>
	<pdf:headline text="Alternative syntax"/>

.. _textviewhelper:

TextViewHelper
--------------

Rendering text using the settings for text.

::

	<pdf:text>Title</pdf:text>
	<pdf:text text="Alternative syntax"/>

.. _listviewhelper:

ListViewHelper
--------------

Rendering a list given as a one dimensional array.

::

	<pdf:list listElements="{0: 'Websites using TYPO3', 1: 'Application Development', 2: 'Mobile Apps', 3: 'Hosting'}"/>
	<pdf:list listElements="{someArrayProperty}"/>

.. _imageviewhelper:

ImageViewHelper
--------------

Rendering the image given as src, the path is always relative to the webroot.

::

	<pdf:image src="typo3conf/ext/pdfviewhelpers/Resources/Public/Example/Bithost.jpg" width="200" />

.. _htmlviewhelper:

HtmlViewHelper
--------------

Rendering any html content using TCPDF's method writeHTML. The default text settings are those from generalText.
It is possible to include a css style tag and also inline styles. This ViewHelper is especially useful for rendering Rich Text.

::

	<pdf:html>
		<style>
			h1 {
				color: #ff642c;
			}
		</style>

		<h1>Some html headline</h1>
		<p style="color: #3a718a;">Lorem ipsum</p>

		{someAdditionalRichText}
	</pdf:html>
