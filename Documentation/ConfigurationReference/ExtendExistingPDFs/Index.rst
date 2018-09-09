.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

Extend Existing PDFs
--------------------

It is possible to use existing PDFs as template and extend the PDF where needed. You can either load the template PDF
within the TypoScript settings or within the Fluid template itself. When you want to use that feature your PDF class should
extend the class ``Bithost\Pdfviewhelpers\Model\BasePDF``. It is however also possible to use the class ``\FPDI``, although
not recommend because it does not support all ``EXT:pdfviewhelpers`` features.

TypoScript
""""""""""

::

	plugin.tx_pdfviewhelpers.settings {
		config {
			class = Bithost\Pdfviewhelpers\Model\BasePDF
		}
		document {
			sourceFile = EXT:pdfviewhelpers/Resources/Public/Examples/ExtendExistingPDFs/pdf_template.pdf
		}
		page {
			importPage = 1
		}
	}


Fluid Template
""""""""""""""

::

	{namespace pdf=Bithost\Pdfviewhelpers\ViewHelpers}

	<pdf:document sourceFile="EXT:pdfviewhelpers/Resources/Public/Examples/ExtendExistingPDFs/pdf_template.pdf">
		<pdf:page importPage="1" margin="{top: 80, right: 20, bottom: 40, left: 20}">
			<pdf:text>Your own text is shown here.</pdf:text>
		</pdf:page>
	</pdf:document>

