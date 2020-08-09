.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


Reference
---------

This section gives an overview on all TypoScript properties of the XPATH content object.

.. attention::

   In TYPO3 10.4 the FILE TypoScript object was removed. You can use a FLUIDTEMPLATE
   cObject instead. In respect to TYPO3 9.5 the deprecated FILE cObject is still supported.

.. container:: table-row

   Property
         source

   Data type
         string\/stdWrap

   Description
         This fetches the XML data from a source. Can be a XML string, a field
         in the database, a file (path or via TypoScript FLUIDTEMPLATE cObject) or an
         external resource.

         **Example (field):** ::

            page.10 = XPATH
            page.10 {
               source.data = page : my_xml_field
               [...]
            }

         Fetches the XML from the field 'my\_xml\_field' of the current page
         record.

         **Example (stdWrap / FUIDTEMPLATE):** ::

            page.10 = XPATH
            page.10 {
               source.cObject = FUIDTEMPLATE
               source.cObject.file = fileadmin/myfile.xml
               [...]
            }

         This fetches the XML from a file included by TypoScript's FLUIDTEMPLATE content
         object.

         **Example (external):** ::

            page.10 = XPATH
            page.10 {
               source = http://news.typo3.org/rss.xml
               [...]
            }

         This draws the XML from an external source. It can be an URL like
         above or an external file resource of any size.


.. container:: table-row

   Property
         registerNamespace

   Data type
         string\/\+ subproperties

   Description
         Registers a namespace for use with the XPATH expression. Syntax is
         **prefix\|namespace** . The namespace must match a namespace in the
         source, otherwise the XPATH query will return false.

         **Example:** ::

            page.10 = XPATH
            page.10 {
               registerNamespace = c|http://example.org/chapter-title
               expression = //c:title
               [...]
            }

         Its possible to extract the namespaces of the XML source with the
         following subproperties:

         **Subproperties:** ::

            .getFromSource [boolean]

            .getFromSource.debug [boolean]

            .getFromSource.listNum [integer]

         **getFromSource** will retrieve the namespaces from the source rather
         than taking the string given in the parent property. With **debug**
         it's possible to see what namespaces are returned. **listNum** is a
         TypoScript listNum with which you can select any of the (possibly
         several) namespaces returned from the XML source.


.. container:: table-row

   Property
         expression

   Data type
         string\/stdWrap

   Description
         XPATH expression.

         **Example:** ::

            page.10 = XPATH
            page.10 {
               expression = //item
               [...]
            }

         Gets all <item> nodes from the XML source.

         **Example (with stdWrap):** ::

            page.10 = XPATH
            page.10 {
               expression = //item[{register:count}]
               expression.insertData = 1
               [...]
            }

         Fetches the item by the number found in the TypoScript register.


.. container:: table-row

   Property
         return

   Data type
         keyword\/stdWrap

   Description
         This sets the return value for the XPATH query. Can be one of the
         following keywords:

         **count**

         Returns the number of the nodes/attributes matched by the XPATH
         expression

         **boolean**

         Returns true or false depending if the XPATH expression matched any
         nodes/attributes

         **xml**

         Returns all matched nodes and their child nodes as XML.

         **array**

         Converts and returns all nodes matched by the XPATH expression in an
         array structure.

         **json**

         Converts and returns all nodes matched by the XPATH expression in json
         format.

         **string**

         Converts and returns all items matched by the XPATH expression as
         strings (atomic node values).

         **Example:** ::

            page.10 = XPATH
            page.10 {
               source.data = page : my_xml_field
               expression = //title
               return = string
               [...]
            }

   Default
         string

.. container:: table-row

   Property
         resultObj

   Data type
         → `see TSref split <http://docs.typo3.org/typo3cms/TyposcriptReference/Functions/Split/Index.html>`_

   Description
         As the name says, the result object contains the result of the XPATH
         query (i.e. all matched nodes, attributes, etc). The resultObj works
         similar to the well known TypoScript split property. This makes the
         handling of the returned items very flexible. You can use option
         split, stdWrap, parseFunc and all the other nice stuff from TSref

         **Example:** ::

            page.10 = XPATH
            page.10 {

               source.data = page : my_xml_field
               expression = //title
               return = string

               resultObj {
                  cObjNum = 1 || 2

                  1.current = 1
                  1.wrap = <h1 style="color:red">|</h1>

                  2.current = 1
                  2.wrap = <h1 style="color:green">|</h1>
               }
            }


.. container:: table-row

   Property
         implodeResult
 
   Data type
         boolean\/\+token

   Description
         Instead of processing the XPATH result set with resultObj, this
         setting directly returns the whole set imploded around a token. This
         way you can split or explode the result yourself and do further
         processing, depending on your usecase. Can be useful for passing on
         result arrays to a FLUIDTEMPLATE for example.

         **token** (string/stdWrap)

         Sets the token around which the result set is imploded.

   Default
         ###COBJ\_XPATH###

.. container:: table-row

   Property
         stdWrap

   Data type
         stdWrap

   Description
         stdWrap properties for the XPATH cObject

         **Example:** ::

            page.10 = XPATH
            page.10 {

               [...]

               stdWrap {
                  outerWrap = <code>|</code>
                  htmlSpecialChars = 1
               }
            }

Next is an example for all TS configuration options with their according data types

::

    my.object = XPATH
    my.object {

        source [URL / PATH / STRING / stdWrap]

        registerNamespace = [STRING prefix|ns]
        registerNamespace {
            getFromSource = [BOOLEAN]
            getFromSource.debug = 1
            getFromSource.listNum [TypoScript listNum]
        }

        expression [STRING + stdWrap]

        return = count|boolean|xml|array|json|string [stdWrap]

        resultObj [TypoScript split]
        resultObj {
            cObjNum = 1
            1.current = 1
        }

        implodeResult [boolean]
        implodeResult.token [string + stdWrap]

        stdWrap [stdWrap]
    }
