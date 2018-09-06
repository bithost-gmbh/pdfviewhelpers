.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

.. _upgrade-guides-1-to-2:

Upgrading from 1.x.x to 2.x.x
=============================

Replaced EmptyFPDI and EmptyTCPDF by BasePDF
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
The classes ``EmptyFPDI`` and ``EmptyTCPDF`` have been replaced by ``BasePDF``. If you have any TypoScript configuration or PHP Code using these
classes you should replace them by ``Bithost\Pdfviewhelpers\Model\BasePDF``. The class ``BasePDF`` offers the same functionality as the other classes before
while adding the possibility to use header and footer ViewHelpers.

Removed example class BithostTCPDF
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
The example class ``BithostTCPDF`` has been removed without a replacement.
Please extend ``BasePDF``, and see the :ref:`basic usage example <basicusage>` on how to render a similar header.