.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

.. _upgrade-guides-2-to-3:

Upgrading from 2.x.x to 3.x.x
=============================

Removed TCPDF and FPDI libraries from source code
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
The libraries TCPDF and FPDI have been removed from ``Resources\Private\PHP`` for composer installations.
In case you are referencing paths in that folder, you might have to change them to the vendor folder.

Removed classes EmptyFPDI and EmptyTCPDF
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
The deprecated classes PDF ``Bithost\Pdfviewhelpers\Model\EmptyFPDI`` and ``Bithost\Pdfviewhelpers\Model\EmptyTCPDF`` have been removed.
Please use ``Bithost\Pdfviewhelpers\Model\BasePDF`` as an alternative.

Type hints and strict typing
^^^^^^^^^^^^^^^^^^^^^^^^^^^^
The PHP files now use strict types and have been extended with type hinting.
This might require changes to classes that inherit or use classes from this extension.

pdfviewhelpers - EXT:news
^^^^^^^^^^^^^^^^^^^^^^^^^
The TypoScript template ``pdfviewhelpers - EXT:news`` does no longer copy the plugin settings to ``module.tx_pdfviewhelpers``
and the page config has been moved from ``config`` to ``pageNewsPDF.config``.

TypoScript file extensions
^^^^^^^^^^^^^^^^^^^^^^^^^^
All TypoScript file extensions have been changed from ``.txt`` to ``.typoscript``.
