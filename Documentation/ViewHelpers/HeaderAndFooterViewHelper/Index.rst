.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

.. _viewhelpers-header-footer:

HeaderViewHelper and FooterViewHelper
-------------------------------------

These ViewHelpers can be used to set header and footer for pages. Thereby they are always applied to one of the following scopes:

- **document:** The header and footer are applied to all the pages.
- **thisPage:** The header and footer are only applied to the first page they are set on.
- **thisPageIncludingPageBreaks** The header and footer are only applied to the page they are set on including sub pages triggered by an auto page break or the ``PageBreakViewHelper``.

The scope is implicitly set depending on where you place the ViewHelpers. ViewHelpers that are descendants of the ``DocumentViewHelper`` have as
default scope ``document`` applied. ViewHelpers that are descendants of the ``PageViewHelper`` have as default scope ``thisPageIncludingPageBreaks`` applied.
It is also possible to set the scope explicitly using the scope ViewHelper attribute.

ViewHelpers that are defined within a ``PageViewHelper`` may overwrite ViewHelpers that are defined on document level. Please see the examples section
for an extended :ref:`example of the header and footer usage <headerandfooter>`.

**Document wide header and footer**
::

	<pdf:document>
		<pdf:header>
			<pdf:text>Header</pdf:text>
		</pdf:header>
		<pdf:footer>
			<pdf:text>Footer</pdf:text>
		</pdf:footer>

		<pdf:page>
			<pdf:text>Content goes here</pdf:text>
		</pdf:page>
	</pdf:document>

**Page wide header and footer**
::

	<pdf:document>
		<pdf:page>
			<pdf:header scope="thisPage" posY="15">
				<pdf:text>Header</pdf:text>
			</pdf:header>
			<pdf:footer posY="-15">
				<pdf:text>Footer with default scope thisPageIncludingPageBreaks</pdf:text>
			</pdf:footer>

			<pdf:text>Content goes here</pdf:text>
		</pdf:page>
	</pdf:document>
