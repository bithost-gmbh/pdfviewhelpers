.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt

Troubleshooting
---------------

Please look carefully at the examples being shipped with this extension, you will find the code in **Resources/Public/Examples**.
Be aware that the static TypoScript template must be included for the examples to work!

Typical Problems
^^^^^^^^^^^^^^^^

Headers already sent
""""""""""""""""""""
Sometimes the following error message is found in the php log or in the output:

Warning::

    PHP Warning: Cannot modify header information - headers already sent by...

This usually occurs when there has already been sent content to the output buffer before the pdf fluid template is rendered.
Since we need to set some headers in order to allow the browser to interpret the content as pdf file for inline display / download,
pdfviewhelpers need to be able to set headers, and this can only be done if there was no output at all on that page before the pdf is rendered.

PDF does not validate
"""""""""""""""""""""

Sometimes, the generated pdf does not correctly validate e.g. in `https://www.pdf-online.com/osa/validate.aspx <https://www.pdf-online.com/osa/validate.aspx>`_
If this is the case, check the generated file in a text editor, especially the end of the file.
Ensure there is no content after the ``%%EOF``.

If you want to have a valid `PDF/A` document, validate the xml in the metadata in the

Code::

    << /Type /Metadata /Subtype /XML /Length 4505 >> stream
    <?xpacket begin="Ôªø" id="W5M0MpCehiHzreSzNTczkc9d"?>
    <x:xmpmeta xmlns:x="adobe:ns:meta/" x:xmptk="Adobe XMP Core 4.2.1-c043 52.372728, 2009/01/18-15:08:04">
        <rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
            ...
            </rdf:Description>
        </rdf:RDF>
    </x:xmpmeta>
    <?xpacket end="w"?>

Section with e.g. here: https://www.w3schools.com/xml/xml_validator.asp


Also ensure you have disabled all html header output:

::

	pdfpage = PAGE
	pdfpage {
		10 = FLUIDTEMPLATE
		10 {
			file = EXT:pdfviewhelpers/Resources/Public/Examples/BasicUsage/Bithost.html
		}
		# ensure there is no other output apart from the pdf
		# take a look at the generated pdf file (end!) in a text editor to verify there is no other output
		# like warnings, error messages or html code
		config {
			disableAllHeaderCode = 1
			xhtml_cleaning = 0
			admPanel = 0
		}
	}