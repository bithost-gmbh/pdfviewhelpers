# Changelog for TYPO3 CMS Extension pdfviewhelpers

## 2.2.0 - Not yet released

- Adds configurable output path for generated font files, [#128](https://github.com/bithost-gmbh/pdfviewhelpers/issues/128)
- Fixes images not working in footer, [#129](https://github.com/bithost-gmbh/pdfviewhelpers/issues/129)
- Adds fit on page option to ImageViewHelper

## 2.1.0 - April 30, 2019
- Adds paragraph line feed to textual ViewHelpers, [#107](https://github.com/bithost-gmbh/pdfviewhelpers/issues/107) (Thanks [@emmemme](https://github.com/emmemme))
- Adds image processing to ImageViewHelper, [#104](https://github.com/bithost-gmbh/pdfviewhelpers/issues/104)
- Adds table of content ViewHelpers, [#103](https://github.com/bithost-gmbh/pdfviewhelpers/issues/103)
- Updates TCPDF to version `6.2.26`
- Updates FPDI to version `2.2.0`
- Updates documentation

## 2.0.2 - December 1, 2018
- Fixes import page on automatic page break not working, [#99](https://github.com/bithost-gmbh/pdfviewhelpers/issues/99) (Thanks [@liayn](https://github.com/liayn))
- Updates documentation

## 2.0.1 - October 18, 2018
- Fixes a PDF 1.7 compliance issue, [#97](https://github.com/bithost-gmbh/pdfviewhelpers/issues/97)
- Updates documentation

## 2.0.0 - October 4, 2018
- Adds HeaderViewHelper and FooterViewHelper, [#31](https://github.com/bithost-gmbh/pdfviewhelpers/issues/31)
- Adds PageNumberAliasViewHelper and TotalNumberOfPagesAliasViewHelpers, [#31](https://github.com/bithost-gmbh/pdfviewhelpers/issues/31)
- Adds LineViewHelper
- Adds support for speaking settings
- Adds partial overwriting of text padding, [#25](https://github.com/bithost-gmbh/pdfviewhelpers/issues/25)
- Adds width and padding to ColumnViewHelper
- Adds xml namespaces and schema
- Adds HyphenationService to cache and speed up hyphenation
- Adds validation and settings conversion services
- Adds PDF constructor arguments to DocumentViewHelper
- Adds optional exit after PDF output
- Adds lineHeight and characterSpacing option to text
- Adds text types to easily apply different styles
- Adds multi column context stack to avoid header and footer context interference
- Adds AvoidPageBreakInsideViewHelper
- Adds support for percentage width
- Adds padding, link and alignment to ImageViewHelper
- Adds template for EXT:news, [#75](https://github.com/bithost-gmbh/pdfviewhelpers/issues/75)
- Adds LineBreakViewHelper
- Adds padding and multi column support to HtmlViewHelper
- Updates TCPDF to version `6.2.25`
- Updates FPDI to version `2.1.0`
- Moves language and hyphenFile settings to DocumentViewHelper, [#39](https://github.com/bithost-gmbh/pdfviewhelpers/issues/39)
- Changes PHP requirement to minimum `5.6`
- Changes default value of `page.autoPageBreak` to `1`
- Fixes MultiColumnViewHelper columns not correctly initialized
- Fixes ListViewHelper not working correctly with page breaks
- Changes `page.margins` to `page.margin`
- Replaces classes EmptyFPDI, EmptyTCPDF by BasePDF, Removes class BithostTCPDF
- Enforces usage of BasePDF

## 1.6.0 - August 14, 2018
- Adds support for TYPO3 9 LTS, [#59](https://github.com/bithost-gmbh/pdfviewhelpers/issues/59) (Thanks [@luberti](https://github.com/luberti))
- Drops support for TYPO3 6 LTS
- Adds support for FAL images, [#52](https://github.com/bithost-gmbh/pdfviewhelpers/issues/52)

## 1.5.2 - July 2, 2018
- Changes PHP requirement to minimum `5.4`, [#56](https://github.com/bithost-gmbh/pdfviewhelpers/issues/56)
- Changes code style to PSR-2, [#23](https://github.com/bithost-gmbh/pdfviewhelpers/issues/23)
- Adds functional tests, [#21](https://github.com/bithost-gmbh/pdfviewhelpers/issues/21)

## 1.5.1 - May 17, 2018
- Fixes not able to create multiple PDF documents per request
- Updates documentation
- Fixes PHP `5.4` compatibility error
- Moves ExtensionManagementUtility::addStaticFile to Overrides/sys_template.php, [#45](https://github.com/bithost-gmbh/pdfviewhelpers/issues/45)
- Fixes outputDestination E and S not really working, [#47](https://github.com/bithost-gmbh/pdfviewhelpers/issues/47)
- Disables ViewHelper output escaping, [#46](https://github.com/bithost-gmbh/pdfviewhelpers/issues/46)

## 1.5.0 - May 4, 2018
- Updates documentation
- Adds (optional) automatic hyphenation to all textual ViewHelpers, [#44](https://github.com/bithost-gmbh/pdfviewhelpers/issues/44)
- Adds support for absolute, relative and TYPO3 EXT: paths, [#43](https://github.com/bithost-gmbh/pdfviewhelpers/issues/43)
- Adds format to PageViewHelper, [#42](https://github.com/bithost-gmbh/pdfviewhelpers/issues/42)
- Fixes importPage does not work with autoPageBreak=1, [#41](https://github.com/bithost-gmbh/pdfviewhelpers/issues/41)

## 1.4.0 - April 10, 2018
- Fixes posX and posY not working, [#37](https://github.com/bithost-gmbh/pdfviewhelpers/issues/37) (Thanks [@PeterSchuhmann](https://github.com/PeterSchuhmann))
- Updates TCPDF to version `6.2.17`
- Updates FPDI to version `1.6.2`
- Improves PdfaShowCase example
- Adds option sRGBMode to config, [#38](https://github.com/bithost-gmbh/pdfviewhelpers/issues/38)
- Adds GetPosXViewHelper and GetPosYViewHelper, [#40](https://github.com/bithost-gmbh/pdfviewhelpers/issues/40)
- Adds custom ViewHelper documentation, [#26](https://github.com/bithost-gmbh/pdfviewhelpers/issues/26)
- Removes default author and title settings

## 1.3.4 - February 7, 2018
- Fixes bug in BE context, [#35](https://github.com/bithost-gmbh/pdfviewhelpers/issues/35) (Thanks [@liayn](https://github.com/liayn))
- Adds more bugs to fix later

## 1.3.3 - November 27, 2017
- Prevents any output after the pdf file, allowing to validate against PDF/A with a custom constructor, [#32](https://github.com/bithost-gmbh/pdfviewhelpers/issues/32) (Thanks [@koritnik](https://github.com/koritnik))
- Updates documentation

## 1.3.2 - Oktober 30, 2017
- Fixes Warning in TCPDF on PHP `7.1.x`, [#28](https://github.com/bithost-gmbh/pdfviewhelpers/issues/28) (Thanks [@koritnik](https://github.com/koritnik))

## 1.3.1 - September 16, 2017
- Removes default font type in order for TCPDF to auto detect type
- Adds .htaccess protection to folder Resources/Private, [#27](https://github.com/bithost-gmbh/pdfviewhelpers/pull/27) (Thanks [@derhansen](https://github.com/derhansen))
- Fixes images not loaded anymore in documentation
- Adds new extension icon

## 1.3.0 - April 23, 2017
- Adds support for TYPO3 8.7 LTS, [#18](https://github.com/bithost-gmbh/pdfviewhelpers/issues/18)
- Adds PageBreakViewHelper, [#16](https://github.com/bithost-gmbh/pdfviewhelpers/issues/16)
- Adds possibility to load html styles from external file, [#14](https://github.com/bithost-gmbh/pdfviewhelpers/issues/14)
- Adds orientation to PageViewHelper
- Adds fontStyle to text
- Adds a TypoScript way for adding custom fonts, [#9](https://github.com/bithost-gmbh/pdfviewhelpers/issues/9)
- Adds minor improvements on text handling
- Updates documentation 

## 1.2.3 - March 21, 2017
- Fixes configuration manager initialization error, [#19](https://github.com/bithost-gmbh/pdfviewhelpers/issues/19)

## 1.2.2 - March 16, 2017
- Fixes PHP 5.4 compatibility issue, [#17](https://github.com/bithost-gmbh/pdfviewhelpers/issues/17)
- Fixes typo3/cms composer dependency error, [#17](https://github.com/bithost-gmbh/pdfviewhelpers/issues/17)

## 1.2.1 - January 14, 2017
- Adds support for backend usage, [#15](https://github.com/bithost-gmbh/pdfviewhelpers/pull/15) (Thanks [@Gernott](https://github.com/gernott))
- Changes default class to EmptyFPDI, [#12](https://github.com/bithost-gmbh/pdfviewhelpers/issues/12)

## 1.2.0 - September 18, 2016
- Adds FPDI Support (use existing PDFs as template), [#7](https://github.com/bithost-gmbh/pdfviewhelpers/issues/7)

## 1.1.0 - August 24, 2016
- Adds HTML ViewHelper, [#3](https://github.com/bithost-gmbh/pdfviewhelpers/issues/3)
- Disables TYPO3 frontend caching by default, [#5](https://github.com/bithost-gmbh/pdfviewhelpers/issues/5)
- Fixes Bug with generalText alignment and paragraphSpacing settings, [#6](https://github.com/bithost-gmbh/pdfviewhelpers/issues/6)
- Adds composer.json, [#4](https://github.com/bithost-gmbh/pdfviewhelpers/issues/4)

## 1.0.1 - August 17, 2016
- Initial version of pdfviewhelpers. Updated documentation.

## 1.0.0 - August 16, 2016
- Initial version of pdfviewhelpers. This is an extension that provides various Fluid ViewHelpers to generate PDF documents.
