.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

.. _text-types:

Text Types
----------

It is possible to define multiple default text styles and then apply them to a ViewHelper using the ``type`` attribute.
This is for instance useful if you have different types of headlines. It is possible to define types for the
``TextViewHelper``, ``HeadlineViewHelper`` and ``ListViewHelper``. In addition you can define types in the ``generalText`` section,
these types are available for all ViewHelpers mentioned.

Text types take part in the settings inheritance, with the following priority (higher priority overwrites lower priority):

1. ``settings.generalText``
2. ``settings.text`` | ``settings.headline`` | ``settings.list``
3. ``settings.text.types`` | ``settings.headline.types`` | ``settings.list.types`` | ``settings.generalText.types``
4. ViewHelper arguments

TypoScript
""""""""""

::

	plugin.tx_pdfviewhelpers.settings {
		headline {
			types {
				h1 {
					fontSize = 24
					padding {
						top = 6
						bottom = 3
					}
				}
				h2 {
					fontsize = 18
					padding {
						top = 4
						bottom = 2
					}
				}
				h3 {
					fontSize = 11
					fontStyle = bold
					padding {
						top = 2
						bottom = 1
					}
				}
			}
		}
	}


Fluid
"""""


::

	<pdf:headline type="h1">Rendered as h1</pdf:headline>
	<pdf:headline type="h2">Rendered as h2</pdf:headline>
	<pdf:headline type="h3">Rendered as h3</pdf:headline>