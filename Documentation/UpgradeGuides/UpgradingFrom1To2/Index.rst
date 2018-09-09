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

Provided class must inherit from BasePDF
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
The PDF class you can provide in ``plugin.tx_pdfviewhelpers.settings.config.class`` is now required to inherit from ``Bithost\Pdfviewhelpers\Model\BasePDF``.

Removed example class BithostTCPDF
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
The example class ``BithostTCPDF`` has been removed without a replacement.
Please extend ``BasePDF``, and see the :ref:`basic usage example <basicusage>` on how to render a similar header.

Introduces ValidationService
^^^^^^^^^^^^^^^^^^^^^^^^^^^^
All utility methods that start with ``isValid`` have been moved to a separate class ``ValidationService``.
If you implemented custom ViewHelpers you have to change these method calls.

Renamed page.margins to page.margin
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
The TypoScript setting ``plugin.tx_pdfviewhelpers.settings.page.margins`` has been renamed to ``plugin.tx_pdfviewhelpers.settings.page.margin``.
Also the Fluid PageViewHelper attribute has been renamed from ``page.margins`` to ``page.margin``.