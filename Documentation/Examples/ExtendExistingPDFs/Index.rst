.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


.. _extendexistingpdfs:

Extend Existing PDFs
====================


.. _extendexistingpdfs_intro:

Intro
-----

There is the possibility to load existing PDF documents and use them as template. Under the hood pdfviewhelpers
uses FPDI_ to import PDF documents as template. You must include the static TypoScript template of the extension
in order to make this example work.

.. _FPDI: https://www.setasign.com/

.. _extendexistingpdfs_typoscript:

TypoScript
----------

::

	page = PAGE
	page {
		10 = FLUIDTEMPLATE
		10 {
			file = EXT:pdfviewhelpers/Resources/Public/Examples/ExtendExistingPDFs/Template.html
		}
	}

.. _extendexistingpdfs_fluid:

Fluid Template
--------------

::

	{namespace pdf=Bithost\Pdfviewhelpers\ViewHelpers}

	<pdf:document sourceFile="typo3conf/ext/pdfviewhelpers/Resources/Public/Examples/ExtendExistingPDFs/pdf_template.pdf">
		<pdf:page importPage="1" margins="{top: 80, right: 20, bottom: 40, left: 20}">
			<pdf:headline>Here is your header</pdf:headline>
			<pdf:text>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt.</pdf:text>
			<pdf:html>
				<h1 style="font-weight: normal; font-size: 16px;">Here is the HTML header</h1>
				<p>Lorem ipsum dolor sit amet, consetetur sadipscing
					elitr, sed diam nonumy eirmod tempor invidunt ut
					labore et dolore magna aliquyam erat, sed diam
					voluptua. At vero eos et dolores et ea rebum. Stet
					clita kasd gubergren, no sea takimata sanctus est
					Lorem ipsum dolor sit amet. Lorem ipsum dolor sit
					diam nonumy eirmod tempor invidunt ut labore et
					dolore magna aliquyam erat, sed diam voluptua. At
					vero eos et accusam et justo duo dolores et ea
					rebum. Stet clita kasd gubergren est Lorem ipsum
					dolor sit amet.</p>
				<p>Lorem ipsum dolor sit amet, consetetur sadipscing
					elitr, sed diam nonumy eirmod tempor invidunt ut
					labore et dolore magna aliquyam erat, sed diam
					voluptua. At vero eos et dolores et ea rebum. Stet
					clita kasd gubergren, no sea takimata sanctus est
					Lorem ipsum dolor sit amet. Lorem ipsum dolor sit
					diam nonumy eirmod tempor invidunt ut labore et
					dolore magna aliquyam erat, sed diam voluptua. At
					vero eos et accusam et justo duo dolores et ea
					rebum. Stet clita kasd gubergren est Lorem ipsum
					dolor sit amet.</p>
			</pdf:html>
		</pdf:page>
	</pdf:document>

.. _extendexistingpdfs_output:

PDF Output
----------

.. figure:: ../../../Resources/Public/Examples/ExtendExistingPDFs/output.png
   :width: 600px
   :align: left
   :alt: Extend Existing PDFs Example

   Rendered PDF document
