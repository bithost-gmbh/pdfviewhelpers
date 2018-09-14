.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

Advanced Customization
----------------------

To completely customize the PDF creation you have the options to provide your own PDF class or write your own ViewHelper.
Your own PDF class is required to extend ``Bithost\Pdfviewhelpers\Model\BasePDF``.
If you feel like your custom ViewHelper might be useful for everybody, feel free to create a pull request!

Extend BasePDF class
^^^^^^^^^^^^^^^^^^^^
You can provide your own PDF class in order to customize its behaviour as you want. This is for instance needed if you want to change constructor arguments or
you want to render header and footer without the ViewHelpers provided.

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

    use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

    /**
     * MyPDF
     *
     * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Marin <esteban.marin@bithost.ch>
     */
    class MyPDF extends \Bithost\Pdfviewhelpers\Model\BasePDF
    {
        /**
         * @return void
         */
        public function __construct($orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false, $pdfa=false)
        {
            $myFormat = 'A3';
            parent::__construct($orientation, $unit, $myFormat, $unicode, $encoding, $diskcache, $pdfa);
        }

        /**
         * @return void
         */
        public function basePdfHeader()
        {
            $extPath = ExtensionManagementUtility::extPath('pdfviewhelpers');
            $address = "Bithost GmbH \nMilchubckstrasse 83 \nCH-8057 Zürich \n\nhallo@bithost.ch \n044 585 28 20 \n\nwww.bithost.ch";

            $this->SetTextColor(140, 140, 140);
            $this->SetFontSize(11);

            $this->Image($extPath . 'Resources/Public/Examples/BasicUsage/logo.png', 15, 15, 56, 24, '', '', '', FALSE, 300, '', FALSE, FALSE, 0, FALSE, FALSE, FALSE, FALSE);
            $this->MultiCell(null, null, $address, 0, 'R', FALSE, 1, 0, 45, TRUE, 0, FALSE, TRUE, 0, 'T', FALSE);
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
            $this->Cell($this->w - 15, 10, 'Page '.$this->getAliasNumPage() . ' of '.$this->getAliasNbPages(), 0, false, 'C', 0, '', 1, false, 'T', 'M');
        }
    }


Create your own ViewHelper
^^^^^^^^^^^^^^^^^^^^^^^^^^

When writing your own ViewHelper you have the options to extend ``AbstractContentElementViewHelper`` or ``AbstractPDFViewHelper``. If your ViewHelper
will add some content to the PDF, you should extend ``AbstractContentElementViewHelper`` in order to inherit its position properties and allow the
header and footer ViewHelpers to work properly.
If your ViewHelper does not add content (e.g. ``GetPosXViewHelper`` or ``PageBreakViewHelper``) you can directly extend ``AbstractPDFViewHelper``.

Within your ViewHelper you have full access to the public API of TCPDF using ``$this->getPDF()``. Please see the TCPDF examples in order to see what
you can do with it: https://tcpdf.org/examples/

*PHP*

::

    <?php

    namespace Vendor\YourExtension\ViewHelpers;

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

    use Bithost\Pdfviewhelpers\ViewHelpers\AbstractContentElementViewHelper;

    /**
     * BarcodeViewHelper
     *
     * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Marin <esteban.marin@bithost.ch>
     */
    class BarcodeViewHelper extends AbstractContentElementViewHelper {
        /**
         * @param string $title
         *
         * @return void
         */
        public function render($title)
        {
            $style = [
                'position' => '',
                'align' => 'C',
                'stretch' => FALSE,
                'fitwidth' => TRUE,
                'cellfitalign' => '',
                'border' => TRUE,
                'hpadding' => 'auto',
                'vpadding' => 'auto',
                'fgcolor' => [0,0,0],
                'bgcolor' => FALSE,
                'text' => TRUE,
                'font' => 'helvetica',
                'fontsize' => 8,
                'stretchtext' => 4
            ];

            $this->getPDF()->SetFontSize(12);
            $this->getPDF()->Cell(0, 0, $title, 0, 1);

            $this->getPDF()->write1DBarcode('CODE 39', 'C39', '', '', '', 18, 0.4, $style, 'N');
        }
    }

*Fluid*

::

	{namespace pdf=Bithost\Pdfviewhelpers\ViewHelpers}
	{namespace you=Vendor\YourExtension\ViewHelpers}

	<pdf:document>
		<pdf:page>
			<you:barcode title="Some Title" />
		</pdf:page>
	</pdf:document>