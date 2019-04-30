.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

TableOfContentViewHelper / HtmlBookmarkTemplateViewHelper
---------------------------------------------------------

The ``TableOfContentViewHelper`` allows to render a table of content when placed inside a ``PageViewHelper``. The ``HtmlBookmarkTemplateViewHelper`` allows to style the content table entries using HTML.
Text rendered with a ``HeadlineViewHelper`` can be added to the table of content automatically by setting ``plugin.tx_pdfviewhelpers.settings.headline.addToTableOfContent = 1``.
Additionally it is possible to use the ``BookmarkViewHelper`` to add entries to the table of content.

Note that the ``TableOfContentViewHelper`` should be placed on the last page of the document, using the ``page`` attribute it can be moved to the front.
Please see the examples section for an extended :ref:`example of the table of content usage <tableofcontent>`.

**IMPORTANT NOTICE:** Please note that TCPDF does not produce valid PDF documents when bookmarks are used.
Although most PDF viewers are still able to render the document you might run into validity troubles using these ViewHelpers.

**Regular Mode**
::

	<pdf:page tableOfContentPage="1">
		<pdf:tableOfContent />
	</pdf:page>

**HTML Mode**

In HTML mode the variables ``#TOC_DESCRIPTION#`` and ``#TOC_PAGE_NUMBER#`` get replaced with the bookmark title and the page number respectively.

::

	<pdf:page tableOfContentPage="1">
		<pdf:tableOfContent page="1" name="Index" htmlMode="1">
			<pdf:htmlBookmarkTemplate level="0">
				<table border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td width="155mm">
							<span style="font-size:12pt;">LEVEL 0: #TOC_DESCRIPTION#</span>
						</td>
						<td width="25mm">
							<span style="font-size:12pt;" align="right">#TOC_PAGE_NUMBER#</span>
						</td>
					</tr>
				</table>
			</pdf:htmlBookmarkTemplate>
			<pdf:htmlBookmarkTemplate level="1">
				<table border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td width="5mm">&nbsp;</td>
						<td width="150mm">
							<span style="font-size:12pt;color: #ff642c;">LEVEL 1: #TOC_DESCRIPTION#</span>
						</td>
						<td width="25mm">
							<span style="font-size:12pt;color: #ff642c;" align="right">#TOC_PAGE_NUMBER#</span>
						</td>
					</tr>
				</table>
			</pdf:htmlBookmarkTemplate>
		</pdf:tableOfContent>
	</pdf:page>