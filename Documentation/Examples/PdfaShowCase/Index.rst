.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


.. _pdfashowcase:

PDF/A Show Case
======================


.. _pdfashowcase_intro:

Intro
-----

Its's possible to render the output as a valid PDF/A document.

.. _pdfashowcase_typoscript:

TypoScript
----------

Be sure to disable all header data, to ensure that all HTML header output is disabled. Some variables on page properties are used to fill the meta data.
The most important setting is ``plugin.tx_pdfviewhelpers.settings.document.pdfa = 1`` to enable PDF/A mode.

::

	pdfpage = PAGE
	pdfpage {

		config {
			disableAllHeaderCode = 1
			xhtml_cleaning = 0
			admPanel = 0
		}

		10 = FLUIDTEMPLATE
		10 {
			file = EXT:pdfviewhelpers/Resources/Public/Examples/PdfaShowCase/Template.html
			variables {
				fileTitle = TEXT
				fileTitle {
					field = title
					wrap = |.pdf
				}

				docTitle = TEXT
				docTitle {
					field = title
					wrap = |
				}

				docAuthor = TEXT
				docAuthor {
					field = author
					wrap = |
				}

				docDate = TEXT
				docDate {
					field = starttime
					date = d.m.Y
					wrap = |
				}

				docAbstract = TEXT
				docAbstract {
					field = abstract
					wrap = |
				}

				docKeywords = TEXT
				docKeywords {
					field = keywords
					wrap = |
				}
			}
		}
	}

	plugin.tx_pdfviewhelpers.settings {
		config {
			class = Bithost\Pdfviewhelpers\Model\PdfaShowCase
			jpgQuality = 80
			fonts {
				addTTFFont {
					roboto {
						path = EXT:pdfviewhelpers/Resources/Public/Examples/FullFeatureShowCase/Roboto.ttf
					}
					opensans {
						path = EXT:pdfviewhelpers/Resources/Public/Examples/FullFeatureShowCase/OpenSans.ttf
					}
				}
			}
		}
		document {
			title = PDFa Show Case Title
			subject = No Subject
			keywords = full, feature, show, case
			outputDestination = I
			outputPath = pdfa.pdf
			pdfa = 1
		}
		page {
			margin {
				top = 20
				right = 15
				bottom = 20
				left = 15
			}
		}
		generalText {
			color = #555
		}
		headline {
			fontFamily = courier
			fontStyle = B
		}
		list {
			color = #555
			fontStyle = I
			bulletColor = #555
		}
	}

	module.tx_pdfviewhelpers < plugin.tx_pdfviewhelpers

.. _pdfashowcase_fluid:

Fluid Template
--------------

::

	<html xmlns="http://www.w3.org/1999/xhtml"
		  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
		  xmlns:f="http://typo3.org/ns/TYPO3/CMS/Fluid/ViewHelpers"
		  xmlns:pdf="http://typo3.org/ns/Bithost/Pdfviewhelpers/ViewHelpers"
		  xsi:schemaLocation="http://typo3.org/ns/Bithost/Pdfviewhelpers/ViewHelpers https://pdfviewhelpers.bithost.ch/schema/2.1.xsd"
		  data-namespace-typo3-fluid="true">

	<pdf:document outputDestination="inline" outputPath="{fileTitle}" author="{docAuthor}" title="{docTitle}" creator="PDF View Helpers" keywords="{docKeywords}" subject="{docAbstract}">
		<pdf:page autoPageBreak="0">
			<pdf:headline trim="0" color="#333" fontSize="18" fontStyle="regular" alignment="left" >Lorem ipsum dolor sit amet</pdf:headline>
			<pdf:text>
				Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et dolores et ea rebum.
			</pdf:text>
		</pdf:page>
	</pdf:document>

	</html>