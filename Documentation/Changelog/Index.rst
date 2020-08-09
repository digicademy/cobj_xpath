.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


ChangeLog
---------

+----------------+---------------------------------------------------------------+
| Version        | Changes                                                       |
+================+===============================================================+
| 1.10.0         | - TYPO3 compatibility: 9.5.0-10.4.99                          |
|                |                                                               |
|                | - Remove namespace compatibility layer                        |
|                |                                                               |
|                | - Refactoring for TYPO3 v10 (thanks to contributors!)         |
|                |                                                               |
|                | - Update manual                                               |
+----------------+---------------------------------------------------------------+
| 1.9.0          | - TYPO3 compatibility: 8.7.0-9.5.99                           |
|                |                                                               |
|                | - **Important:** Extension namespace switched from            |
|                |   adwlm/cobj_xpath to digicademy/cobj_xpath. Please adapt     |
|                |   your Fluid templates to the new namespace.                  |
|                |   Example: {namespace xslt=Digicademy\\CobjXpath\\ViewHelpers}|
|                |   . A compatibility layer is in place that will be removed in |
|                |   TYPO3 LTS version 10                                        |
|                |                                                               |
|                | - Refactoring QueryViewHelper for TYPO3 v9                    |
|                |                                                               |
|                | - Update manual                                               |
+----------------+---------------------------------------------------------------+
| 1.8.0          | - This version was only released on GitHub                    |
|                |                                                               |
|                | - TYPO3 compatibility: 7.6.0-8.7.99                           |
|                |                                                               |
|                | - Usage of old standalone classname "cobj_xpath" via migration|
|                |   is now removed; please use the namespaced version of the    |
|                |   class from now on                                           |
|                |                                                               |
|                | - CKEditor plugin for <xpath> TypoTag in TYPO3 8.7+           |
|                |                                                               |
|                | - Update manual                                               |
|                |                                                               |
|                | - PSR refactoring and code compliance                         |
+----------------+---------------------------------------------------------------+
| 1.7.0          | - Version compatibility set to 6.2.0-7.9.99                   |
|                |                                                               |
|                | - Namespace and class refactoring                             |
+----------------+---------------------------------------------------------------+
| 1.6.0          | - Version compatibility set to 4.5.0-6.2.99                   |
|                |                                                               |
|                | - Some corrections in manual                                  |
+----------------+---------------------------------------------------------------+
| 1.5.0          | - Version compatibility set to 4.5.0-6.1.99                   |
|                |                                                               |
|                | - ReST based manual                                           |
+----------------+---------------------------------------------------------------+
| 1.4.0          | - Skipped (problem with TER upload)                           |
+----------------+---------------------------------------------------------------+
| 1.3.0          | - Skipped (problem with TER upload)                           |
+----------------+---------------------------------------------------------------+
| 1.2.0          | - New XPATH view helper for Fluid templates                   |
|                |                                                               |
|                | - New TypoScript property implodeResult                       |
|                |                                                               |
|                | - New tutorial about XPATH, FLUIDTEMPLATE and XSLT            |
|                |                                                               |
|                | - New tutorial about <xpath> TypoTag                          |
+----------------+---------------------------------------------------------------+
| 1.1.1          | - Loading XML files from a path could fail sometimes          |
+----------------+---------------------------------------------------------------+
| 1.1.0          | - source.url property fused into parent property source       |
+----------------+---------------------------------------------------------------+
| 1.0.0          | - First public version                                        |
+----------------+---------------------------------------------------------------+