.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


.. _pdfashowcase:

PDF/a Show Case
======================


.. _pdfashowcase_intro:

Intro
-----

Its's possible to render the output as a valid PDF/a document.

.. _pdfashowcase_typoscript:

TypoScript
----------

Be sure to disable all header data, to ensure that all HTML header output is disabled. Some variables on page properties are used to fill the meta data.

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
		}
		page {
			margins {
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


.. _pdfafeatureshowcase_php:

PHP
---

A custom PHP class is needed to override the TCPDF constructor, to be able to set the pdfa parameter to true.

::

   <?php
   namespace Bithost\Pdfviewhelpers\Model;

   /***
    *
    * This file is part of the "PDF ViewHelpers" Extension for TYPO3 CMS.
    *
    *  (c) 2016 Markus Mächler <markus.maechler@bithost.ch>, Bithost GmbH
    *           Esteban Marin <esteban.marin@bithost.ch>, Bithost GmbH
    *
    *  All rights reserved
    *
    *  This script is part of the TYPO3 project. The TYPO3 project is
    *  free software; you can redistribute it and/or modify
    *  it under the terms of the GNU General Public License as published by
    *  the Free Software Foundation; either version 3 of the License, or
    *  (at your option) any later version.
    *
    *  The GNU General Public License can be found at
    *  http://www.gnu.org/copyleft/gpl.html.
    *
    *  This script is distributed in the hope that it will be useful,
    *  but WITHOUT ANY WARRANTY; without even the implied warranty of
    *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    *  GNU General Public License for more details.
    *
    *  This copyright notice MUST APPEAR in all copies of the script!
    ***/

   use Bithost\Pdfviewhelpers\Model\TCPDF;

   /**
    * PdfaTCPDF, overrides constructor to set pdfa param as true
    *
    * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Marin <esteban.marin@bithost.ch>
    */

   class PdfaTCPDF extends \TCPDF
   {

        /**
        * @return void
        */
        public function __construct($orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false, $pdfa=true) {
            // Set pdfa parameter to true
            parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, true);
        }

        /**
        * @return void
        */
        public function Header()
        {

        }

        /**
        * @return void
        */
        public function Footer()
        {

        }
   }



.. _pdfashowcase_fluid:

Fluid Template
--------------

::

	<html xmlns="http://www.w3.org/1999/xhtml"
		  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
		  xmlns:f="http://typo3.org/ns/TYPO3/CMS/Fluid/ViewHelpers"
		  xmlns:pdf="http://typo3.org/ns/Bithost/Pdfviewhelpers/ViewHelpers"
		  xsi:schemaLocation="http://typo3.org/ns/Bithost/Pdfviewhelpers/ViewHelpers https://pdfviewhelpers.bithost.ch/schema/2.0.xsd"
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