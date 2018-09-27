.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


.. _fullfeatureshowcase:

Full Feature Show Case
======================


.. _fullfeatureshowcase_intro:

Intro
-----

This example is showing some of the features of the extension pdfviewhelpers including typography, custom fonts, header and footer, lists, images, html, layout and settings inheritance.

.. _fullfeatureshowcase_typoscript:

TypoScript
----------

::

	pdfpage = PAGE
	pdfpage {
		10 = FLUIDTEMPLATE
		10 {
			file = EXT:pdfviewhelpers/Resources/Public/Examples/FullFeatureShowCase/Template.html
		}
		# ensure there is no other output apart from the pdf
		# take a look at the generated pdf file (end!) in a text editor to verify there is no other output
		# like warnings, error messages or html code
		config {
			disableAllHeaderCode = 1
			xhtml_cleaning = 0
			admPanel = 0
		}
	}

	plugin.tx_pdfviewhelpers.settings {
		config {
			class = Bithost\Pdfviewhelpers\Model\FullFeatureShowCase
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
			title = Full Feature Show Case Title
			subject = No Subject
			keywords = full, feature, show, case
			outputDestination = inline
			outputPath = fullfeatureshowcase.pdf
		}
		header {
			posY = 10
		}
		page {
			margin {
				top = 25
				right = 15
				bottom = 25
				left = 15
			}
		}
		generalText {
			color = #555
		}
		text {
			types {
				header {
					color = #8C8C8C
				}
				quote {
					fontStyle = italic
					alignment = center
					padding {
						top = 10
						right = 50
						bottom = 10
						left = 50
					}
				}
			}
		}
		headline {
			fontFamily = courier
			fontStyle = bold
			types {
				h1 {
					fontSize = 28
					color = #ff642c
				}
				h2 {
					fontSize = 22
					color = #ff642c
				}
				h3 {
					fontSize = 14
					fontFamily = helvetica
					fontStyle = bold
				}
			}
		}
		list {
			color = #000
			fontStyle = bold
			bulletColor = #BEDB39
			padding {
				left = 2
			}
		}
	}

	module.tx_pdfviewhelpers < plugin.tx_pdfviewhelpers



.. _fullfeatureshowcase_php:

PHP
---

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

	/**
	 * FullFeatureShowCase
	 *
	 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Marin <esteban.marin@bithost.ch>
	 */
	class FullFeatureShowCase extends BasePDF
	{
		/**
		 * @return void
		 */
		public function basePdfHeader()
		{
		}

		/**
		 * @return void
		 */
		public function basePdfFooter()
		{
			$this->SetY(-20);
			$this->SetDrawColor(140, 140, 140);
			$this->Line(15, $this->y, $this->w - 15, $this->y);

			$this->SetY(-17);
			$this->SetTextColor(140, 140, 140);
			$this->SetFontSize(11);
			$this->Cell($this->w - 15, 10, 'Page ' . $this->getAliasNumPage() . ' of ' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 1, false, 'T', 'M');
		}
	}


.. _fullfeatureshowcase_fluid:

Fluid Template
--------------

::

	<html xmlns="http://www.w3.org/1999/xhtml"
		  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
		  xmlns:f="http://typo3.org/ns/TYPO3/CMS/Fluid/ViewHelpers"
		  xmlns:pdf="http://typo3.org/ns/Bithost/Pdfviewhelpers/ViewHelpers"
		  xsi:schemaLocation="http://typo3.org/ns/Bithost/Pdfviewhelpers/ViewHelpers https://pdfviewhelpers.bithost.ch/schema/2.0.xsd"
		  data-namespace-typo3-fluid="true">

	<pdf:document outputPath="overwritten_name.pdf" title="Full Feature Show Case">

		<pdf:header>
			<pdf:text type="header">Bithost GmbH - Milchbuckstrasse 83 CH-8057 Zürich</pdf:text>
			<pdf:text type="header" posY="10" alignment="right">hallo@bithost.ch - www.bithost.ch</pdf:text>
			<pdf:graphics.line style="{color: '#8C8C8C'}"/>
		</pdf:header>

		<pdf:page>
			<pdf:headline fontSize="20">Full Feature Show Case</pdf:headline>
			<pdf:text>Showing some features of pdfviewhelpers.</pdf:text>

			<pdf:headline>Typography</pdf:headline>
			<pdf:text fontStyle="bold">Bold text</pdf:text>
			<pdf:text fontStyle="italic">Italic text</pdf:text>
			<pdf:text fontStyle="underline">Underlined text</pdf:text>
			<pdf:text color="#ff642c">Colored text</pdf:text>
			<pdf:text alignment="left">Alignment Left</pdf:text>
			<pdf:text alignment="center">Alignment Center</pdf:text>
			<pdf:text alignment="right">Alignment Right</pdf:text>

			<pdf:text color="#ff642c" padding="{top:6, right:80, bottom:6, left:20}">
				Text with special padding. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et dolores et ea rebum.
			</pdf:text>

			<pdf:multiColumn>
				<pdf:column>
					<pdf:text>
						Text with normal paragraph spacing.
						Shown here.
					</pdf:text>
				</pdf:column>
				<pdf:column>
					<pdf:text paragraphSpacing="2">
						Text with extended paragraph spacing.
						Shown here.
					</pdf:text>
				</pdf:column>
				<pdf:column>
					<pdf:text paragraphSpacing="4">
						Text with even more extended paragraph spacing.
						Shown here.
					</pdf:text>
				</pdf:column>
			</pdf:multiColumn>

			<pdf:multiColumn>
				<pdf:column>
					<pdf:text autoHyphenation="0" padding="{right: 2}">
						ThisisalongtextWITHOUTautomatichyphenationbeingactivated.
					</pdf:text>
				</pdf:column>
				<pdf:column>
					<pdf:text autoHyphenation="1" padding="{left: 2}">
						ThisisalongtextWITHautomatichyphenationbeingactivated.
					</pdf:text>
				</pdf:column>
			</pdf:multiColumn>

			<pdf:text lineHeight="2" characterSpacing="1.5">
				This text has increased lineHeight as well as increased characterSpacing.
			</pdf:text>

			<pdf:headline>Custom fonts</pdf:headline>
			<pdf:text fontFamily="opensans">Custom font Open Sans automatically converted from TTF file.</pdf:text>
			<pdf:text fontFamily="roboto">Custom font Roboto automatically converted from TTF file.</pdf:text>
			<pdf:text fontFamily="roboto" color="#ff642c">Custom font Roboto even colored!</pdf:text>

			<pdf:pageBreak />

			<pdf:headline>Styled lists</pdf:headline>
			<pdf:list
					padding="{left: 1.75}"
					bulletSize="2.5"
					bulletImageSrc="EXT:pdfviewhelpers/Resources/Public/Examples/FullFeatureShowCase/bullet_image.png"
					listElements="{0: 'Websites using TYPO3', 1: 'Application Development', 2: 'Mobile Apps', 3: 'Hosting'}" />
			<pdf:list
					bulletSize="2"
					bulletColor="#ff642c"
					fontStyle="italic"
					listElements="{0: 'Lorem ipsum', 1: 'dolor sit', 2: 'Lorem dipsum', 3: 'dolor sit amet'}" />

			<pdf:headline>Text types</pdf:headline>
			<pdf:text>It is possible to create an arbitrary amount of default text settings and easily apply them using the type attribute.</pdf:text>
			<pdf:headline type="h1">This is a h1 headline</pdf:headline>
			<pdf:headline type="h2">This is a h2 headline</pdf:headline>
			<pdf:headline type="h3">This is a h3 headline</pdf:headline>

			<pdf:text type="quote">This could be a quote style. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.</pdf:text>
		</pdf:page>

		<pdf:page orientation="landscape" format="A4" margin="{top: 15}">
			<pdf:header scope="thisPage">
				<pdf:text type="header">Only this page will have a different header</pdf:text>
			</pdf:header>

			<pdf:multiColumn>
				<pdf:column width="50%">
					<pdf:headline>Images in different sizes</pdf:headline>
					<pdf:image src="EXT:pdfviewhelpers/Resources/Public/Examples/FullFeatureShowCase/Bithost.jpg" width="100" link="https://www.bithost.ch" />
					<pdf:image src="EXT:pdfviewhelpers/Resources/Public/Examples/FullFeatureShowCase/Bithost.jpg" width="70" link="https://www.bithost.ch" />
					<pdf:image src="EXT:pdfviewhelpers/Resources/Public/Examples/FullFeatureShowCase/Bithost.jpg" width="50" link="https://www.bithost.ch" />
				</pdf:column>
				<pdf:column width="50%">
					<pdf:headline>Images with different alignment</pdf:headline>
					<pdf:image src="EXT:pdfviewhelpers/Resources/Public/Examples/FullFeatureShowCase/Bithost.jpg" width="50%" alignment="left" />
					<pdf:image src="EXT:pdfviewhelpers/Resources/Public/Examples/FullFeatureShowCase/Bithost.jpg" width="50%" alignment="center" />
					<pdf:image src="EXT:pdfviewhelpers/Resources/Public/Examples/FullFeatureShowCase/Bithost.jpg" width="50%" alignment="right" />
				</pdf:column>
			</pdf:multiColumn>
		</pdf:page>

		<pdf:page>
			<pdf:headline>HTML content being styled externally</pdf:headline>
			<pdf:html styleSheet="EXT:pdfviewhelpers/Resources/Public/Examples/FullFeatureShowCase/styles.css">
				<h1>Headline 1</h1>
				<h2>Headline 2</h2>
				<h3>Headline 3</h3>

				<a href="https://www.bithost.ch">A Link to click</a>

				<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.</p>
				<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.</p>

				<h3 style="color: #333;">Table</h3>
				<table cellpadding="4" cellspacing="4">
					<thead>
						<tr>
							<th>Head 1</th>
							<th>Head 2</th>
							<th>Head 3</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>Content 1</td>
							<td>Content 2</td>
							<td>Content 3</td>
						</tr>
						<tr>
							<td>More Content 1</td>
							<td>More Content 2</td>
							<td>More Content 3</td>
						</tr>
						<tr>
							<td>Content 1</td>
							<td>Content 2</td>
							<td>Content 3</td>
						</tr>
					</tbody>
				</table>
			</pdf:html>

			<pdf:headline>Position ViewHelpers</pdf:headline>
			<pdf:text>This text will be rendered at position x={pdf:getPosX()} and y={pdf:getPosY()}</pdf:text>

			<pdf:headline>PageNumber ViewHelpers</pdf:headline>
			<pdf:text>We are on page {pdf:getPageNumberAlias()} of {pdf:getTotalNumberOfPagesAlias()} pages.</pdf:text>

			<pdf:avoidPageBreakInside>
				<pdf:headline>Avoid page break inside</pdf:headline>
				<pdf:text>EXT:pdfviewhelpers tries to avoid page breaks inside ViewHelpers that are wrapped with an AvoidPageBreakInsideViewHelper.</pdf:text>
				<pdf:text>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</pdf:text>
				<pdf:text>That is why all these elements are rendered on the next page.</pdf:text>
			</pdf:avoidPageBreakInside>
		</pdf:page>

	</pdf:document>

	</html>

.. _fullfeatureshowcase_output:

PDF Output
----------

.. figure:: _assets/output_1.png
   :width: 500px
   :align: left
   :alt: Full Feature Show Case rendered PDF

|

.. figure:: _assets/output_2.png
   :width: 500px
   :align: left
   :alt: Full Feature Show Case rendered PDF

|

.. figure:: _assets/output_3.png
   :width: 700px
   :align: left
   :alt: Full Feature Show Case rendered PDF

|

.. figure:: _assets/output_4.png
   :width: 500px
   :align: left
   :alt: Full Feature Show Case rendered PDF

|

.. figure:: _assets/output_5.png
   :width: 500px
   :align: left
   :alt: Full Feature Show Case rendered PDF

|
