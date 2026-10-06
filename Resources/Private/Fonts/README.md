# Fonts

The core fonts (`core/`) and their embeddable PDF/A variants (`pdfa/`) converted for tc-lib-pdf.
They are used by `Bithost\Pdfviewhelpers\Model\BasePDF`, see the `README` and `LICENSE` files in the folders for their origin.

The files have been generated with tecnickcom/tc-lib-pdf-font 4.5.1, to regenerate them run
`php util/build_fonts.php` within the tc-lib-pdf-font package and copy `target/fonts/core` and `target/fonts/pdfa`.
