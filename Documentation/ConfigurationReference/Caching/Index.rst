.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

Caching
-------

The extension ``pdfviewhelpers`` does not provide any caching mechanism for the generated PDF documents. In fact caching
is disabled by default because it makes not much sense to save a PDF document to the TYPO3 frontend cache.
Since generating PDF documents is quite time consuming you should implement your own caching strategy by saving
the generated PDF files to the filesystem and only generate them when necessary.

It is not possible to cache the Fluid template parsing when using the ``MultiColumnViewHelper`` because it implements the ``ChildNodeAccessInterface``.
This may lead to a significant loss in performance for that view.