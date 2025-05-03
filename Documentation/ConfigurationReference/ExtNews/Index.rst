.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

EXT:news integration
--------------------

The extension ``pdfviewhelpers`` ships with a PDF template for ``EXT:news``. You can use the default template provided or customize it in any way.

Enable default template
"""""""""""""""""""""""

To enable the default template you have to include the static TypoScript template ``pdfviewhelpers - EXT:news``.
All the TypoScript settings that are needed are limited to the special page type ``28032013``. In order to link to the PDF view
you have to include this page type in the link generation:

::

	<n:link newsItem="{newsItem}" settings="{settings}" title="{newsItem.title}" configuration="{additionalParams: '&type=28032013'}">
		Download as PDF
	</n:link>

Customising the PDF
"""""""""""""""""""

Most of the typography aspects can be changed solely through TypoScript configuration by changing the existing text types (see also :ref:`text types <text-types>`).
Please make sure that all your configuration is limited to the page type ``28032013``.

::

	[request && traverse(request.getQueryParams(), 'type') == 28032013]
		plugin.tx_pdfviewhelpers.settings {
			text {
				types {
					teaser {
						fontStyle = regular
						fontSize = 16
					}
					footer {
						alignment = center
					}
				}
			}
			headline {
				types {
					title {
						color = #ff642c
					}
				}
			}
		}

		module.tx_pdfviewhelpers < plugin.tx_pdfviewhelpers
	[global]

If you need to change the layout or extend the header and footer content, you can provide your own Fluid template
that is located in ``EXT:yourext/Resources/Private/Templates/Extensions/News/Templates/News/Detail.html``:

::

	[request && traverse(request.getQueryParams(), 'type') == 28032013]
		plugin.tx_news {
			view {
				templateRootPaths {
					5 = EXT:yourext/Resources/Private/Templates/Extensions/News/Templates/
				}
			}
		}
	[global]