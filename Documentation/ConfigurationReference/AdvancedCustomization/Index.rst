.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

.. _advanced-customization:

Advanced Customization
----------------------

To completely customize the PDF creation you have the options to write your own ViewHelper or provide your own PDF class.
If you feel like your custom ViewHelper might be useful for everybody, feel free to create a pull request!

Create your own ViewHelper
^^^^^^^^^^^^^^^^^^^^^^^^^^

Creating your own ViewHelper might be necessary if you need a feature of tc-lib-pdf that is not yet available through the implemented ViewHelpers
or if you have a complex layout to realise.

When writing your own ViewHelper you have the options to extend ``AbstractContentElementViewHelper`` or ``AbstractPDFViewHelper``. If your ViewHelper
will add some content to the PDF (e.g. ``TextViewHelper`` or ``LineViewHelper``), you should extend ``AbstractContentElementViewHelper``
in order to inherit its position properties and allow the header and footer ViewHelpers to work properly.
If your ViewHelper does not add content (e.g. ``GetPosXViewHelper`` or ``PageBreakViewHelper``) you can directly extend ``AbstractPDFViewHelper``.

Within your ViewHelper you have access to the PDF object using ``$this->getPDF()``. It is an instance of ``Bithost\Pdfviewhelpers\Model\BasePDF``
which extends ``Com\Tecnick\Pdf\Tcpdf`` of tc-lib-pdf_. tc-lib-pdf places content at absolute positions, ``BasePDF`` adds a cursor
and automatic page breaks on top of it. The most important methods of ``BasePDF`` are:

	==================================================== =====================================================================================
	Method                                               Description
	==================================================== =====================================================================================
	getX(), getY(), setX(), setY(), setXY()              The cursor position, content is usually rendered at the cursor. A negative y is measured from the bottom of the page.
	getPageId(), setPageId()                             The current page, page ids start at 0.
	getMargins(), getPageWidth(), getPageHeight()        The dimensions of the current page.
	setFont(), setFontSize(), setTextColor()             The text style, colors are given in HEX format e.g. ``#000``.
	setLineHeight(), setCharacterSpacing()               More text style settings.
	writeText(), writeHtml()                             Writes text or HTML, breaks pages automatically and moves the cursor below the content.
	drawImage(), drawLine(), drawRect()                  Draws images and graphics.
	addPage(), checkPageBreak()                          Adds a page or adds a page if an element does not fit on the current page.
	==================================================== =====================================================================================

Everything else of tc-lib-pdf can be used as well, please see the tc-lib-pdf examples in order to see what you can do with it:
https://github.com/tecnickcom/tc-lib-pdf/tree/main/examples

.. _tc-lib-pdf: https://github.com/tecnickcom/tc-lib-pdf

The following example shows a ViewHelper that could be used to render a barcode.

*PHP*

::

    <?php

    namespace Vendor\YourExtension\ViewHelpers;

    use Bithost\Pdfviewhelpers\ViewHelpers\AbstractContentElementViewHelper;

    class BarcodeViewHelper extends AbstractContentElementViewHelper
    {
        public function initializeArguments(): void
        {
            parent::initializeArguments();

            $this->registerArgument('code', 'string', 'The content of the barcode.', true);
            $this->registerArgument('type', 'string', 'The type of the barcode, e.g. C128 or QRCODE.', false, 'C128');
        }

        public function render(): void
        {
            $pdf = $this->getPDF();
            $height = 15;

            // add a new page if the barcode does not fit on the current page
            $pdf->setXY($this->arguments['posX'], $this->arguments['posY']);
            $pdf->checkPageBreak($height);

            $pdf->page->addContent($pdf->getBarcode($this->arguments['type'], $this->arguments['code'], $pdf->getX(), $pdf->getY(), -1, $height));
            $pdf->setY($pdf->getY() + $height);
        }
    }

*Fluid*

::

	{namespace pdf=Bithost\Pdfviewhelpers\ViewHelpers}
	{namespace you=Vendor\YourExtension\ViewHelpers}

	<pdf:document>
		<pdf:page>
			<you:barcode code="CODE 128" />
		</pdf:page>
	</pdf:document>


Extend BasePDF class
^^^^^^^^^^^^^^^^^^^^
You can provide your own PDF class in the TypoScript settings in order to customize its behaviour as you want.
Your own PDF class is required to extend ``Bithost\Pdfviewhelpers\Model\BasePDF``.
The methods ``basePdfHeader`` and ``basePdfFooter`` are called for every page and can be used to render a header and footer.
``$this->page::PAGE_NUM`` and ``$this->page::PAGE_TOT`` are replaced with the page number and the total number of pages.

*TypoScript*

::

	plugin.tx_pdfviewhelpers.settings {
		config {
			class = Vendor\YourExtension\Model\MyPDF
		}
	}

*PHP*

::

    <?php

    namespace Vendor\YourExtension\Model;

    use Bithost\Pdfviewhelpers\Model\BasePDF;
    use TYPO3\CMS\Core\Utility\GeneralUtility;

    class MyPDF extends BasePDF
    {
        protected function basePdfHeader(): void
        {
            $logo = file_get_contents(GeneralUtility::getFileAbsFileName('EXT:pdfviewhelpers/Resources/Public/Examples/BasicUsage/logo.png'));

            $this->drawImage($logo, 'image', 15, 15, 56);

            $this->setTextColor('#8c8c8c');
            $this->setFontSize(11);
            $this->writeText("Bithost GmbH\nMilchbuckstrasse 83\n8057 Zürich", 15, 15, $this->getPageWidth() - 30, 'R');
        }

        protected function basePdfFooter(): void
        {
            $posY = $this->getPageHeight() - 20;

            $this->drawLine(15, $posY, $this->getPageWidth() - 15, $posY, ['lineWidth' => 0.2, 'lineColor' => '#8c8c8c']);

            $this->setTextColor('#8c8c8c');
            $this->setFontSize(11);
            $this->writeText('Page ' . $this->page::PAGE_NUM . ' of ' . $this->page::PAGE_TOT, 15, $posY + 3, $this->getPageWidth() - 30, 'C');
        }
    }
