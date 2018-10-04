.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

AvoidPageBreakInsideViewHelper
------------------------------

This ViewHelper may wrap any composition of other ViewHelpers. It tries its best to avoid a page break within its children elements.
Note that this ViewHelper needs to render its children two times to determine whether a page break is needed or not.
This has a negative impact on the performance as well as might create other undesired side effects.

::

	<pdf:document>
		<pdf:page>
			<pdf:headline>Welcome to the extension pdfviewhelpers</pdf:headline>
			<pdf:text>Lorem ipsum.</pdf:text>

			<pdf:avoidPageBreakInside>
				<pdf:headline>Some more information</pdf:headline>
				<pdf:multiColumn>
					<pdf:column>
						<pdf:text>Lorem ipsum.</pdf:text>
					</pdf:column>
					<pdf:column>
						<pdf:image src="EXT:pdfviewhelpers/Resources/Public/Examples/BasicUsage/Bithost.jpg"/>
						<pdf:text padding="{top: 1}" color="#8C8C8C">Esteban Marín, Markus Mächler</pdf:text>
					</pdf:column>
				</pdf:multiColumn>
			</pdf:avoidPageBreakInside>
		</pdf:page>
	</pdf:document>

