.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _introduction:

Introduction
============

.. _what-it-does:

What does it do?
----------------

This is a TYPO3 CMS extension that provides various Fluid ViewHelpers to generate PDF documents.
Using the ViewHelpers from this extension you can make any Fluid template into a PDF document.
The extension ``pdfviewhelpers`` is using TCPDF_ and FPDI_ for the PDF generation.

.. _TCPDF: https://tcpdf.org/
.. _FPDI: https://www.setasign.com/products/fpdi

.. _features:

Key features
------------

- ViewHelpers to render text and lists
- ViewHelper to render images (supporting FAL)
- ViewHelpers to repeatedly render header and footer
- ViewHelper to render HTML / rich-text content
- ViewHelpers to create a multi column layout
- Load existing PDF documents as template
- Fully customizable by writing your own ViewHelpers
- Rich inheritance based TypoScript settings
- Supported output destinations: string, inline, download and file
- Usable both in frontend and backend