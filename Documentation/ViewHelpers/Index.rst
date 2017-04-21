.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _viewhelpers:

ViewHelpers
===========

- DocumentViewHelper_
- PageViewHelper_
- PageBreakViewHelper_
- MultiColumnViewHelper_
- HeadlineViewHelper_
- TextViewHelper_
- ListViewHelper_
- ImageViewHelper_
- HtmlViewHelper_

.. _DocumentViewHelper:

DocumentViewHelper
------------------

This ViewHelper must be to first to be used in your template and wrap all the other ViewHelpers. It is responsible for
generating the document.

**Simple Usage**
::

	<pdf:document>
		[..]
	</pdf:document>

**Advanced Usage**
::

	<pdf:document
		title="Bithost Example"
		subject="Welcome message"
		author="Bithost GmbH"
		keywords="example, test"
		outputDestination="I"
		outputPath="example.pdf">
		[..]
	</pdf:document>

.. _PageViewHelper:

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

.. _PageBreakViewHelper:

PageBreakViewHelper
-------------------

Adds a page break within a single page. Can be used for conditional page breaks for instance.

::

	<pdf:page>
		Page 1
		<pdf:pageBreak />
		Page 2

		Conditional page break
		<f:if condition="{someCondition}">
			<pdf:pageBreak />
		</f:if>
	</pdf:page>

.. _MultiColumnViewHelper:

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

.. _HeadlineViewHelper:

HeadlineViewHelper
------------------

Rendering text using the settings for headlines.

::

	<pdf:headline>Title</pdf:headline>
	<pdf:headline text="Alternative syntax"/>

.. _TextViewHelper:

TextViewHelper
--------------

Rendering text using the settings for text.

::

	<pdf:text>Title</pdf:text>
	<pdf:text text="Alternative syntax"/>

.. _ListViewHelper:

ListViewHelper
--------------

Rendering a list given as a one dimensional array.

::

	<pdf:list listElements="{0: 'Websites using TYPO3', 1: 'Application Development', 2: 'Mobile Apps', 3: 'Hosting'}"/>
	<pdf:list listElements="{someArrayProperty}"/>

.. _ImageViewHelper:

ImageViewHelper
---------------

Rendering the image given as src, the path is always relative to the webroot.

::

	<pdf:image src="typo3conf/ext/pdfviewhelpers/Resources/Public/Example/Bithost.jpg" width="200" />

.. _HtmlViewHelper:

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
