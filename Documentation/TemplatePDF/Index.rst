.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _templatepdf:

Template PDF
============


.. _intro:

Intro
-----

There is the possibility to load existing PDF documents and use them as template. Under the hood pdfviewhelpers
uses FPDI_ to import PDF documents as template. You must include the static TypoScript template of the extension
in order to make this example work.

.. _FPDI: https://www.setasign.com/

.. _typoscript:

TypoScript
----------

::

	page = PAGE
	page {
		10 = FLUIDTEMPLATE
		10 {
			file = EXT:pdfviewhelpers/Resources/Public/Example/PDFTemplate.html
		}
	}

	plugin.tx_pdfviewhelpers.settings {
		config {
			class = Bithost\Pdfviewhelpers\Model\EmptyFPDI
		}
	}

.. _fluid:

Fluid Template
--------------

::

	{namespace pdf=Bithost\Pdfviewhelpers\ViewHelpers}

	<pdf:document sourceFile="typo3conf/ext/pdfviewhelpers/Resources/Public/Example/pdf_template.pdf">
		<pdf:page importPage="1" margins="{top: 80, right: 20, bottom: 40, left: 20}">
			<pdf:headline>Here is your header</pdf:headline>
			<pdf:text>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt.</pdf:text>
		</pdf:page>
	</pdf:document>
