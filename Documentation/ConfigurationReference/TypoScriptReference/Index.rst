.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

TypoScript Reference
--------------------

Settings inheritance
^^^^^^^^^^^^^^^^^^^^

To avoid redundancy there is an inheritance structure within the settings. There are basically three levels top down:

1. **plugin.tx_pdfviewhelpers.settings.document|page|generalText:** The top level are global settings for document, page and generalText (all textual output).

2. **plugin.tx_pdfviewhelpers.settings.headline|text|list:** Headline, text and list inherit settings from generalText. All the settings from generalText may be overwritten here with specific settings.

3. **Fluid ViewHelper attributes:** The bottom level are Fluid ViewHelper attributes. All TypoScript settings may be overwritten using Fluid ViewHelper attributes with the same name. e.g:

::

	{namespace pdf=Bithost\Pdfviewhelpers\ViewHelpers}

	<pdf:document outputDestination="I" title="Bithost Example">
		<pdf:page autoPageBreak="1">
			<pdf:headline color="#333">Different color</pdf:headline>
		</pdf:page>
	</pdf:document>

Properties in plugin.tx_pdfviewhelpers.settings
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. container:: ts-properties

	============================================== ===================================== ==========================================
	Property                                       Data type                             Default
	============================================== ===================================== ==========================================
	config.class_                                  :ref:`t3tsref:data-type-string`       Bithost\\Pdfviewhelpers\\Model\\BasePDF
	config.disableCache_                           :ref:`t3tsref:data-type-boolean`      1
	config.exitAfterPdfContentOutput_              :ref:`t3tsref:data-type-boolean`      0
	config.jpgQuality_                             :ref:`t3tsref:data-type-integer`      100
	config.sRGBMode_                               :ref:`t3tsref:data-type-boolean`      0
	config.allowedImageTypes_                      Array                                 *See static TypoScript template*
	config.fonts.subset_                           :ref:`t3tsref:data-type-boolean`      1
	config.fonts.addTTFFont_                       Array                                 *See static TypoScript template*
	config.fonts.outputPath_                       :ref:`t3tsref:data-type-string`       typo3temp/pdfviewhelpers/fonts/
	document.title_                                :ref:`t3tsref:data-type-string`
	document.subject_                              :ref:`t3tsref:data-type-string`
	document.author_                               :ref:`t3tsref:data-type-string`
	document.keywords_                             :ref:`t3tsref:data-type-string`
	document.creator_                              :ref:`t3tsref:data-type-string`       TYPO3 EXT:pdfviewhelpers
	document.outputDestination_                    :ref:`t3tsref:data-type-string`       inline
	document.outputPath_                           :ref:`t3tsref:data-type-string`       document.pdf
	document.sourceFile_                           :ref:`t3tsref:data-type-string`
	document.unit_                                 :ref:`t3tsref:data-type-string`       mm
	document.unicode_                              :ref:`t3tsref:data-type-boolean`      1
	document.encoding_                             :ref:`t3tsref:data-type-string`       UTF-8
	document.pdfa_                                 :ref:`t3tsref:data-type-boolean`      0
	document.pdfua_                                :ref:`t3tsref:data-type-boolean`      0
	document.language_                             :ref:`t3tsref:data-type-string`       ger
	document.hyphenFile_                           :ref:`t3tsref:data-type-string`       hyph-de-ch-1901.tex
	page.autoPageBreak_                            :ref:`t3tsref:data-type-boolean`      0
	page.margin_                                   Array                                 {top: 15, right: 15, bottom: 15, left: 15}
	page.importPage_                               :ref:`t3tsref:data-type-integer`
	page.importPageOnAutomaticPageBreak_           :ref:`t3tsref:data-type-boolean`      1
	page.orientation_                              :ref:`t3tsref:data-type-string`       portrait
	page.format_                                   :ref:`t3tsref:data-type-string`       A4
	page.keepMargins_                              :ref:`t3tsref:data-type-boolean`      0
	page.tableOfContentPage_                       :ref:`t3tsref:data-type-boolean`      0
	header.posY_                                   :ref:`t3tsref:data-type-integer`      5
	footer.posY_                                   :ref:`t3tsref:data-type-integer`      -10
	avoidPageBreakInside.breakIfImpossibleToAvoid_ :ref:`t3tsref:data-type-boolean`      0
	generalText.trim_                              :ref:`t3tsref:data-type-boolean`      1
	generalText.removeDoubleWhitespace_            :ref:`t3tsref:data-type-boolean`      1
	generalText.color_                             :ref:`t3tsref:data-type-string`       #000
	generalText.fontFamily_                        :ref:`t3tsref:data-type-string`       helvetica
	generalText.fontSize_                          :ref:`t3tsref:data-type-integer`      11
	generalText.fontStyle_                         :ref:`t3tsref:data-type-string`       regular
	generalText.lineHeight_                        :ref:`t3tsref:data-type-float`        1.25
	generalText.characterSpacing_                  :ref:`t3tsref:data-type-float`        0
	generalText.padding_                           Array                                 {top: 0, right: 0, bottom: 0, left: 0}
	generalText.alignment_                         :ref:`t3tsref:data-type-string`       left
	generalText.paragraphSpacing_                  :ref:`t3tsref:data-type-integer`      2
	generalText.paragraphLineFeed_                 :ref:`t3tsref:data-type-boolean`      0
	generalText.autoHyphenation_                   :ref:`t3tsref:data-type-boolean`      0
	text.trim                                      :ref:`t3tsref:data-type-boolean`      *See generalText*
	text.removeDoubleWhitespace                    :ref:`t3tsref:data-type-boolean`      *See generalText*
	text.color.                                    :ref:`t3tsref:data-type-string`       *See generalText*
	text.fontFamily                                :ref:`t3tsref:data-type-string`       *See generalText*
	text.fontSize                                  :ref:`t3tsref:data-type-integer`      *See generalText*
	text.fontStyle                                 :ref:`t3tsref:data-type-string`       *See generalText*
	text.lineHeight                                :ref:`t3tsref:data-type-float`        *See generalText*
	text.characterSpacing                          :ref:`t3tsref:data-type-float`        *See generalText*
	text.padding                                   Array                                 *See generalText*
	text.alignment                                 :ref:`t3tsref:data-type-string`       *See generalText*
	text.paragraphSpacing                          :ref:`t3tsref:data-type-integer`      *See generalText*
	text.paragraphLineFeed                         :ref:`t3tsref:data-type-boolean`      *See generalText*
	text.autoHyphenation                           :ref:`t3tsref:data-type-boolean`      *See generalText*
	headline.trim                                  :ref:`t3tsref:data-type-boolean`      *See generalText*
	headline.removeDoubleWhitespace                :ref:`t3tsref:data-type-boolean`      *See generalText*
	headline.color                                 :ref:`t3tsref:data-type-string`       *See generalText*
	headline.fontFamily                            :ref:`t3tsref:data-type-string`       *See generalText*
	headline.fontSize                              :ref:`t3tsref:data-type-integer`      *See generalText*
	headline.fontStyle                             :ref:`t3tsref:data-type-string`       *See generalText*
	headline.lineHeight                            :ref:`t3tsref:data-type-float`        *See generalText*
	headline.characterSpacing                      :ref:`t3tsref:data-type-float`        *See generalText*
	headline.padding                               Array                                 {top: 6, bottom: 3}
	headline.alignment                             :ref:`t3tsref:data-type-string`       *See generalText*
	headline.paragraphSpacing                      :ref:`t3tsref:data-type-integer`      *See generalText*
	headline.paragraphLineFeed                     :ref:`t3tsref:data-type-boolean`      *See generalText*
	headline.autoHyphenation                       :ref:`t3tsref:data-type-boolean`      *See generalText*
	headline.addToTableOfContent                   :ref:`t3tsref:data-type-boolean`      0
	headline.tableOfContentLevel                   :ref:`t3tsref:data-type-integer`      0
	list.trim                                      :ref:`t3tsref:data-type-boolean`      *See generalText*
	list.removeDoubleWhitespace                    :ref:`t3tsref:data-type-boolean`      *See generalText*
	list.color                                     :ref:`t3tsref:data-type-string`       *See generalText*
	list.fontFamily                                :ref:`t3tsref:data-type-string`       *See generalText*
	list.fontSize                                  :ref:`t3tsref:data-type-integer`      *See generalText*
	list.fontStyle                                 :ref:`t3tsref:data-type-string`       *See generalText*
	list.lineHeight                                :ref:`t3tsref:data-type-float`        *See generalText*
	list.characterSpacing                          :ref:`t3tsref:data-type-float`        *See generalText*
	list.paragraphLineFeed                         :ref:`t3tsref:data-type-boolean`      *See generalText*
	list.padding                                   Array                                 {bottom: 2, left: 1.5}
	list.alignment                                 :ref:`t3tsref:data-type-string`       left
	list.bulletColor_                              :ref:`t3tsref:data-type-string`       #000
	list.bulletImageSrc_                           :ref:`t3tsref:data-type-string`
	list.bulletSize_                               :ref:`t3tsref:data-type-float`        1.5
	list.autoHyphenation                           :ref:`t3tsref:data-type-boolean`      *See generalText*
	image.alignment_                               :ref:`t3tsref:data-type-string`       left
	image.fitOnPage_                               :ref:`t3tsref:data-type-boolean`      1
	image.padding_                                 Array                                 {bottom: 2}
	image.processingInstructions_                  Array                                 {}
	html.autoHyphenation                           :ref:`t3tsref:data-type-boolean`      *See generalText*
	html.styleSheet_                               :ref:`t3tsref:data-type-string`
	html.padding_                                  Array                                 {top: 0, right: 0, bottom: 2, left: 0}
	graphics.line.padding_                         Array                                 {top: 4, right: 0, bottom: 5, left: 0}
	graphics.line.style_                           Array                                 {width: 0.25, color: #000}
	tableOfContent.page_                           :ref:`t3tsref:data-type-integer`      1
	tableOfContent.numbersFont_                    :ref:`t3tsref:data-type-string`
	tableOfContent.filter_                         :ref:`t3tsref:data-type-string`       .
	tableOfContent.name_                           :ref:`t3tsref:data-type-string`
	tableOfContent.htmlMode_                       :ref:`t3tsref:data-type-boolean`      0
	tableOfContent.fontFamily_                     :ref:`t3tsref:data-type-string`
	tableOfContent.fontSize_                       :ref:`t3tsref:data-type-float`
	tableOfContent.lineHeight_                     :ref:`t3tsref:data-type-float`
	tableOfContent.characterSpacing_               :ref:`t3tsref:data-type-float`
	tableOfContent.padding_                        Array                                 {bottom: 2}
	htmlBookmarkTemplate.level_                    :ref:`t3tsref:data-type-integer`      0
	htmlBookmarkTemplate.sanitizeWhitespace_       :ref:`t3tsref:data-type-boolean`      1
	bookmark.level_                                :ref:`t3tsref:data-type-integer`      0
	bookmark.fontStyle_                            :ref:`t3tsref:data-type-string`
	bookmark.color_                                :ref:`t3tsref:data-type-string`
	============================================== ===================================== ==========================================


Property details
^^^^^^^^^^^^^^^^

.. only:: html

	.. contents::
		:local:
		:depth: 1

.. _config.class:

config.class
""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.config.class =` :ref:`t3tsref:data-type-string`

Decides which PHP class should be used as TCPDF object. You can easily provide your own class in order to render custom header and footers or to customize TCPDF in any way.
Your provided class must inherit from Bithost\\Pdfviewhelpers\\Model\\BasePDF.

.. _config.disableCache:

config.disableCache
"""""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.config.disableCache =` :ref:`t3tsref:data-type-boolean`

Decides whether the TYPO3 frontend cache will be disabled or not.

.. _config.exitAfterPdfContentOutput:

config.exitAfterPdfContentOutput
""""""""""""""""""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.config.exitAfterPdfContentOutput =` :ref:`t3tsref:data-type-boolean`

Decides whether the PHP method ``exit`` is called after the PDF content has been sent to the browser.
This might solve issues when additional content is echoed and appended to the PDF document. However it might also lead to other unexpected behaviour so be careful.

.. _config.jpgQuality:

config.jpgQuality
"""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.config.jpgQuality =` :ref:`t3tsref:data-type-integer`

JpgQuality being used, values from 0 - 100.

.. _config.sRGBMode:

config.sRGBMode
"""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.config.sRGBMode =` :ref:`t3tsref:data-type-boolean`

Enable sRGBMode, see TCPDF documentation for further information.

.. _config.allowedImageTypes:

config.allowedImageTypes
""""""""""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.config.allowedImageTypes =` Array

Mapping of TCPDF image functions to their corresponding image file extensions.

.. _config.fonts.subset:

config.fonts.subset
"""""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.config.fonts.subset =` :ref:`t3tsref:data-type-boolean`

Decides whether to subset the used fonts or not. When this is set to true it is not possible to edit the generated PDF
if the font is not present in the users system, but the file size gets smaller.

.. _config.fonts.addTTFFont:

config.fonts.addTTFFont
"""""""""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.config.fonts.addTTFFont =` Array

Possibility to add custom fonts, please have a look at the dedicated chapter Custom Fonts.

.. _config.fonts.outputPath:

config.fonts.outputPath
"""""""""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.config.fonts.outputPath =` :ref:`t3tsref:data-type-string`

Path to directory where font files of custom fonts should be stored. This folder can safely be deleted and will automatically be re/created if it does not exist.

.. _document.title:

document.title
""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.document.title =` :ref:`t3tsref:data-type-string`

The title of the generated PDF document.

.. _document.subject:

document.subject
""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.document.subject =` :ref:`t3tsref:data-type-string`

The subject of the generated PDF document.

.. _document.author:

document.author
"""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.document.author =` :ref:`t3tsref:data-type-string`

The author of the generated PDF document.

.. _document.keywords:

document.keywords
"""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.document.keywords =` :ref:`t3tsref:data-type-string`

A comma separated list of keywords for the generated PDF document.

.. _document.creator:

document.creator
""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.document.creator =` :ref:`t3tsref:data-type-string`

The creator of the generated PDF document.

.. _document.outputDestination:

document.outputDestination
""""""""""""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.document.outputDestination =` :ref:`t3tsref:data-type-string`

The TCPDF output destination for the PDF. Possible values are:

	================== ======================================================================================
	outputDestination  Description
	================== ======================================================================================
	I / inline         Sending the PDF inline to the browser.
	D / download       Sending the PDF as immediate file download.
	F / file           Saving the PDF to the specified outputPath.
	FI / file-inline   Saving the PDF to the specified outputPath and sending it inline to the browser.
	FD / file-download Saving the PDF to the specified outputPath and sending it as immediate file download.
	E / email          Return the PDF as base64 mime multi-part email attachment (RFC 2045).
	S / string         Return the PDF as string.
	================== ======================================================================================

.. _document.outputPath:

document.outputPath
"""""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.document.outputPath =` :ref:`t3tsref:data-type-string`

The TCPDF output path of the document. If you are saving the file to filesystem this is a relative path from the
webroot directory e.g. ``fileadmin/pdfviewhelpers/projectXY.pdf``.
If you are sending it inline or as file download it is simply the name of the document e.g. ``projectXY.pdf``.

.. _document.sourceFile:

document.sourceFile
"""""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.document.sourceFile =` :ref:`t3tsref:data-type-string`

The sourceFile is a the path to a PDF document you want to use as a template (see also page.importPage).

.. _document.unit:

document.unit
"""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.document.unit =` :ref:`t3tsref:data-type-string`

The measurement unit used. Possible values are ``pt``, ``mm``, ``cm`` and ``in``.

.. _document.unicode:

document.unicode
""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.document.unicode =` :ref:`t3tsref:data-type-boolean`

Determines whether the input text is unicode or not.

.. _document.encoding:

document.encoding
"""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.document.encoding =` :ref:`t3tsref:data-type-string`

Charset encoding (used only when converting back html entities).

.. _document.pdfa:

document.pdfa
"""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.document.pdfa =` :ref:`t3tsref:data-type-boolean`

Sets the document to PDF/A mode if true.

.. _document.pdfua:

document.pdfua
""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.document.pdfua =` :ref:`t3tsref:data-type-boolean`

Enables experimental support for PDF/UA. Please note that this feature is very much incomplete, feel free to contribute any improvements on this!

.. _document.language:

document.language
"""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.document.language =` :ref:`t3tsref:data-type-string`

Decides which language settings are used from TCPDF. All possible language codes can be found in ``Resources/Private/tcpdf/examples/lang/``.

.. _document.hyphenFile:

document.hyphenFile
"""""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.document.hyphenFile =` :ref:`t3tsref:data-type-string`

The name of the hyphen file used for the automatic hyphenation. This needs to be set according to the language of your document.
All possible values can be found in the directory ``pdfviewhelpers/Resources/Private/Hyphenation/``.

Example values are: ``hyph-de-1996.tex``, ``hyph-en-gb.tex``, ``hyph-nl.tex``, ``hyph-fr.tex``

.. _page.autoPageBreak:

page.autoPageBreak
""""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.page.autoPageBreak =` :ref:`t3tsref:data-type-boolean`

Decides whether TCPDF uses auto page break or not. You can always add a new page by adding a new ``<pdf:page>`` tag to your template.

.. _page.margin:

page.margin
""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.page.margin =` Array

An array of the margin for each page. The default unit is millimeters.

.. _page.importPage:

page.importPage
"""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.page.importPage =` :ref:`t3tsref:data-type-integer`

Specifies which page should be used as template for the current page. Must be used together with document.sourceFile.

.. _page.importPageOnAutomaticPageBreak:

page.importPageOnAutomaticPageBreak
"""""""""""""""""""""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.page.importPageOnAutomaticPageBreak =` :ref:`t3tsref:data-type-boolean`

Determines whether a PDF template that is used on a page is also rendered when an automatic page break occurs.

.. _page.orientation:

page.orientation
""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.page.orientation =` :ref:`t3tsref:data-type-string`

Defines the orientation of the current page and the following pages. Possible values are ``P`` / ``portrait`` and ``L`` / ``landscape``.

.. _page.format:

page.format
"""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.page.format =` :ref:`t3tsref:data-type-string`

Defines the format of the current page. Possible values are e.g. ``A0`` - ``A12``, to see all possible values you have to check ``\TCPDF_STATIC::$page_formats``.

.. _page.keepMargins:

page.keepMargins
""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.page.keepMargins =` :ref:`t3tsref:data-type-string`

If true overwrites the default page margins with the current margins.

.. _page.tableOfContentPage:

page.tableOfContentPage
"""""""""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.page.tableOfContentPage =` :ref:`t3tsref:data-type-string`

If true the page will be rendered as a table of content page, e.g. it can be moved to the front.

.. _header.posY:

header.posY
"""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.header.posY =` :ref:`t3tsref:data-type-integer`

Defines the header position relative to the top of the page.

.. _footer.posY:

footer.posY
"""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.footer.posY =` :ref:`t3tsref:data-type-integer`

Defines the footer position relative to the top of the page. You can use negative numbers to place the footer relative to the bottom of the page.

.. _avoidPageBreakInside.breakIfImpossibleToAvoid:

avoidPageBreakInside.breakIfImpossibleToAvoid
"""""""""""""""""""""""""""""""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.avoidPageBreakInside.breakIfImpossibleToAvoid =` :ref:`t3tsref:data-type-boolean`

If set to true this ViewHelper inserts a page break even if the content does not fit on one page, meaning a page break is unavoidable.

.. _generalText.trim:

generalText.trim
""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.generalText.trim =`  :ref:`t3tsref:data-type-boolean`

If set to true leading and trailing spaces or tabs are trimmed.

.. _generalText.removeDoubleWhitespace:

generalText.removeDoubleWhitespace
""""""""""""""""""""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.generalText.removeDoubleWhitespace =`  :ref:`t3tsref:data-type-boolean`

If set to true double spaces within text elements are removed.

.. _generalText.color:

generalText.color
"""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.generalText.color =`  :ref:`t3tsref:data-type-string`

The text color as hex value, possible syntax are: ``#000`` or ``#000000``

.. _generalText.fontFamily:

generalText.fontFamily
""""""""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.generalText.fontFamily =`  :ref:`t3tsref:data-type-string`

The font family being used. A list of available fonts and a configuration to add your own fonts is available in the chapter  :ref:`custom fonts<custom-fonts>`.

.. _generalText.fontSize:

generalText.fontSize
""""""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.generalText.fontSize =`  :ref:`t3tsref:data-type-integer`

The font size being used.

.. _generalText.fontStyle:

generalText.fontStyle
"""""""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.generalText.fontStyle =`  :ref:`t3tsref:data-type-string`

The font style being used. Possible values are: ``R`` / ``regular``, ``B`` / ``bold``, ``I`` / ``italic``, ``U`` / ``underline``

.. _generalText.lineHeight:

generalText.lineHeight
""""""""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.generalText.lineHeight =`  :ref:`t3tsref:data-type-float`

Sets the line height with respect to the font size.

.. _generalText.characterSpacing:

generalText.characterSpacing
""""""""""""""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.generalText.characterSpacing =`  :ref:`t3tsref:data-type-float`

Sets the spacing between individual characters

.. _generalText.padding:

generalText.padding
"""""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.generalText.padding =`  Array

An array of the padding for each text element. The default unit is millimeters.

.. _generalText.alignment:

generalText.alignment
"""""""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.generalText.alignment =` :ref:`t3tsref:data-type-string`

Possible values are: ``L`` / ``left``, ``C`` / ``center``, ``R`` / ``right``, ``J`` / ``justify``

.. _generalText.paragraphSpacing:

generalText.paragraphSpacing
""""""""""""""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.generalText.paragraphSpacing =` :ref:`t3tsref:data-type-integer`

Defines the spacing of paragraphs separated by new lines.

.. _generalText.paragraphLineFeed:

generalText.paragraphLineFeed
""""""""""""""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.generalText.paragraphLineFeed =` :ref:`t3tsref:data-type-boolean`

Add new lines char after each paragraph (in justified text keeps left align the last line of each paragraph).

.. _generalText.autoHyphenation:

generalText.autoHyphenation
"""""""""""""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.generalText.autoHyphenation =` :ref:`t3tsref:data-type-boolean`

A boolean value indicating whether to use TCPDF's automatic hyphenation or not. You can also add soft hyphens yourself to your text with "&shy;".
If you use automatic hyphenation please make sure that you configure "config.hyphenFile" to match your language.

.. _headline.addToTableOfContent:

headline.addToTableOfContent
""""""""""""""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.generalText.addToTableOfContent =` :ref:`t3tsref:data-type-boolean`

If true the headline will be added to the table of content.

.. _headline.tableOfContentLevel:

headline.tableOfContentLevel
""""""""""""""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.generalText.tableOfContentLevel =` :ref:`t3tsref:data-type-integer`

Indicating the level of the headline in the table of content. Starting from ``0`` as top level, ``1`` for second, ``2`` for third level and so on.

.. _list.bulletColor:

list.bulletColor
""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.list.bulletColor =` :ref:`t3tsref:data-type-string`

The color of the bullet used as hex value, possible syntax are: ``#000`` or ``#000000``

.. _list.bulletImageSrc:

list.bulletImageSrc
"""""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.list.bulletImageSrc =` :ref:`t3tsref:data-type-string`

The path to an image that should be used as bullet.

.. _list.bulletSize:

list.bulletSize
"""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.list.bulletSize =` :ref:`t3tsref:data-type-float`

The size of the bullet as floating point value.

.. _image.alignment:

image.alignment
"""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.image.alignment =` :ref:`t3tsref:data-type-string`

Possible values are: ``L`` / ``left``, ``C`` / ``center``, ``R`` / ``right``

.. _image.fitOnPage:

image.fitOnPage
"""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.image.fitOnPage =` :ref:`t3tsref:data-type-boolean`

If true the image will automatically be rescaled to fit on page.

.. _image.padding:

image.padding
"""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.image.padding =` Array

The padding around the image.

.. _image.processingInstructions:

image.processingInstructions
""""""""""""""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.image.processingInstructions =` Array

An array of processing instructions that is passed to the method ``ImageService->applyProcessingInstructions``. A possible configuration looks like this:

::

	processingInstructions {
		width =
		height =
		maxHeight =
		minWidth =
		maxWidth = 200
		minHeight =
		crop {
			custom_crop {
				cropArea {
					width = 0.5
					height = 0.5
					x = 0
					y = 0
				}
			}
		}
		cropVariant = custom_crop
	}


.. _html.styleSheet:

html.styleSheet
"""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.html.styleSheet =` :ref:`t3tsref:data-type-string`

The path to a style sheet being used in the HtmlViewHelper. The can be provided relative to the webroot directory, e.g. "fileadmin/pdf_style.css".

.. _html.padding:

html.padding
""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.html.padding =` Array

The padding around the html element.

.. _graphics.line.padding:

graphics.line.padding
"""""""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.graphics.line.padding =` Array

Defines the padding around a line. The default unit is millimeters.

.. _graphics.line.style:

graphics.line.style
"""""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.graphics.line.style =` Array

An array defining line styles, please see the https://tcpdf.org/examples/example_012/ for all possible values.

.. _tableOfContent.page:

tableOfContent.page
"""""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.tableOfContent.page =` :ref:`t3tsref:data-type-integer`

Indicates at what place in the document the table of content will be rendered.

.. _tableOfContent.numbersFont:

tableOfContent.numbersFont
""""""""""""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.tableOfContent.numbersFont =` :ref:`t3tsref:data-type-string`

The font used to render the numbers. Note that a monospaced font must be used in order to guarantee correct alignment.

.. _tableOfContent.filter:

tableOfContent.filter
"""""""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.tableOfContent.filter =` :ref:`t3tsref:data-type-string`

The filter used to fill up the space between the entry title and the page number.

.. _tableOfContent.name:

tableOfContent.name
"""""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.tableOfContent.name =` :ref:`t3tsref:data-type-string`

The name used for the table of content bookmark.

.. _tableOfContent.htmlMode:

tableOfContent.htmlMode
"""""""""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.tableOfContent.htmlMode =` :ref:`t3tsref:data-type-boolean`

If true the table of content is rendered in HTML mode.

.. _tableOfContent.fontFamily:

tableOfContent.fontFamily
"""""""""""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.tableOfContent.fontFamily =` :ref:`t3tsref:data-type-string`

The ``fontFamily`` for the entries. Also see *See generalText*.

.. _tableOfContent.fontSize:

tableOfContent.fontSize
"""""""""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.tableOfContent.fontSize =` :ref:`t3tsref:data-type-float`

The ``fontSize`` of the top most level. Also see *See generalText*.

.. _tableOfContent.lineHeight:

tableOfContent.lineHeight
"""""""""""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.tableOfContent.lineHeight =` :ref:`t3tsref:data-type-float`

The ``lineHeight`` used for the table content entries. Also see *See generalText*.

.. _tableOfContent.characterSpacing:

tableOfContent.characterSpacing
"""""""""""""""""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.tableOfContent.characterSpacing =` :ref:`t3tsref:data-type-float`

The ``characterSpacing`` used for the table content entries. Also see *See generalText*.

.. _tableOfContent.padding:

tableOfContent.padding
""""""""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.tableOfContent.padding =` Array

The cell ``padding`` used for the table content entries.

.. _htmlBookmarkTemplate.level:

htmlBookmarkTemplate.level
""""""""""""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.htmlBookmarkTemplate.level =` :ref:`t3tsref:data-type-integer`

The bookmark entry level to which the HTML template should apply.

.. _htmlBookmarkTemplate.sanitizeWhitespace:

htmlBookmarkTemplate.sanitizeWhitespace
"""""""""""""""""""""""""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.htmlBookmarkTemplate.sanitizeWhitespace =` :ref:`t3tsref:data-type-boolean`

If true the input will be trimmed and whitespaces between HTML tags will be removed.

.. _bookmark.level:

bookmark.level
""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.bookmark.level =` :ref:`t3tsref:data-type-boolean`

The table of content level for the bookmark.

.. _bookmark.fontStyle:

bookmark.fontStyle
""""""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.bookmark.fontStyle =` :ref:`t3tsref:data-type-boolean`

The ``fontStyle`` this individual bookmark. Also see *See generalText*.

.. _bookmark.color:

bookmark.color
""""""""""""""

:typoscript:`plugin.tx_pdfviewhelpers.settings.bookmark.fontStyle =` :ref:`t3tsref:data-type-boolean`

The ``fontStyle`` this individual bookmark. Also see *See generalText*.