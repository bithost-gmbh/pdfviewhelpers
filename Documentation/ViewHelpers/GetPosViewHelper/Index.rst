.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

GetPosXViewHelper / GetPosYViewHelper
-------------------------------------

These ViewHelpers provide the possibility to read the current position within the PDF document.

::

	<pdf:text>This text will be rendered at position x={pdf:getPosX()} and y={pdf:getPosY()}</pdf:text>

It is possible to use these ViewHelpers to position elements relatively when used together with a math ViewHelper.
The following example requires the extension vhs to be installed.

::

	{namespace v=FluidTYPO3\Vhs\ViewHelpers}

	<pdf:text posX="{v:math.subtract(a: '{pdf:getPosX()}', b: 15)}" posY="{v:math.sum(a: '{pdf:getPosY()}', b: 30)}">This text element is moved relatively</pdf:text>
