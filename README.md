# TYPO3 CMS Extension pdfviewhelpers

## Introduction
This is a TYPO3 CMS extension that provides various Fluid ViewHelpers to generate PDF documents.
Using the ViewHelpers from this extension you can make any Fluid template into a PDF document.
The extension pdfviewhelpers is using [TCPDF](https://tcpdf.org/) for the PDF generation.

## Example

### Fluid Template
```
{namespace pdf=Bithost\Pdfviewhelpers\ViewHelpers}

<pdf:document outputDestination="I" title="Bithost Example">
	<pdf:page>
		<pdf:text color="#8C8C8C">
			Zurich, <f:format.date format="d.m.Y" >now</f:format.date>
		</pdf:text>
		<pdf:headline>Welcome to the extension pdfviewhelpers</pdf:headline>
		<pdf:text>Lorem ipsum  vero [..] ipsum dolor sit amet.</pdf:text>
		
		<pdf:headline>Some more information</pdf:headline>
		<pdf:multiColumn>
			<pdf:column>
				<pdf:text>Lorem ipsum [..] sed diam voluptua:</pdf:text>
				<pdf:list listElements="{0: 'Websites using TYPO3', 1: 'Application Development', 2: 'Mobile Apps', 3: 'Hosting'}"/>
				<pdf:text>Lorem ipsum dolor [..] sit amet.</pdf:text>
			</pdf:column>
			<pdf:column>
				<pdf:image src="typo3conf/ext/pdfviewhelpers/Resources/Public/Images/example.jpg" width="200" />
				<pdf:text padding="{top:1, right:0, bottom:0, left:0}" color="#8C8C8C">Esteban Marín, Markus Mächler</pdf:text>
			</pdf:column>
		</pdf:multiColumn>

		<pdf:text>Lorem ipsum [..] sit amet.</pdf:text>
	</pdf:page>
</pdf:document>
```

### PDF Output
![Example PDF output](Resources/Public/images/example_pdf.png)

## Bug Tracker

https://github.com/bithost-gmbh/pdfviewhelpers/issues

## Git Repository

https://github.com/bithost-gmbh/pdfviewhelpers

## Contact

* [@maechler](https://github.com/maechler) 
* [@macjohnny](https://github.com/macjohnny)
* https://www.bithost.ch/kontakt/