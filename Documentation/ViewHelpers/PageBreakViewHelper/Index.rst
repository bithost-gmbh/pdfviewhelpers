.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

PageBreakViewHelper
-------------------

Adds a page break within a single page. Can be used for conditional page breaks for instance.

::

	<pdf:page>
		Page 1
		<pdf:pageBreak />
		Page 2

		Conditional page break
		<f:if condition="{someCondition}">
			<pdf:pageBreak />
		</f:if>
	</pdf:page>
