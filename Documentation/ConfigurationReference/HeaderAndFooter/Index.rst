.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

Header and Footer
-----------------

It is possible to render header and footer for each page using the header and footer ViewHelpers. You can define
document wide headers and overwrite them on page level if desired. It is possible to use the ``MultiColumnViewHelper``
in the header and footer sections. However it is recommended to use absolute positioning using the ``posY`` attribute as
in the :ref:`Basic Usage example <basicusage>` for performance reasons.
Please also see :ref:`the ViewHelper documentation <viewhelpers-header-footer>` as well as the :ref:`example of header and footer usage <headerandfooter>`.

In addition to using these ViewHelpers you may :ref:`provide your own PDF class <viewhelpers-header-footer>` and
overwrite the methods ``Header`` / ``Footer`` or ``basePdfHeader`` / ``basePdfFooter``. Overwriting ``Header`` / ``Footer``
disables the usage of the header and footer ViewHelpers completely. Overwriting ``basePdfHeader`` / ``basePdfFooter``
allows to use the header and footer ViewHelpers while also be able to define custom headers in the PDF class.