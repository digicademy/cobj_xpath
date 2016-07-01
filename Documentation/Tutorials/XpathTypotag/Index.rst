.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


<xpath> TypoTag
^^^^^^^^^^^^^^^

From a developers point of view using the XPATH content object in a
TypoScript template or in a FLUIDTEMPLATE is perfectly ok. But imagine
you have some power users that want to display values from XML files
they upload themselves. How could the cObject be made “reusable” when
we have to deal with XML a lot? Writing a small extension comes to
mind immediately. Or maybe introducing a new content element. In this
tutorial, we will look at another possibility that is quite reusable,
flexible and convenient: a <xpath> TypoTag. It will look like this:

.. figure:: ../../Images/manual_html_1fc5a198.png
   :alt: <xpath> TypoTag

This is the source when the RTE is disabled:

::

   <xpath expression="//item" return="string">fileadmin/xpath/poe.xml</xpath>

Optionally for our editors we could provide a userElement:

.. figure:: ../../Images/manual_html_60d7b4cd.png
   :alt: userElement dialogue

The obvious advantage of a TypoTag in comparison to the other
approaches is that it can be used everywhere in the system. You could
also use it in a news record or an address element. Only an input
field is needed that is treated with the good old
lib.parseFunc/lib.parseFunc\_RTE. But first things first. Lets
configure the RTE with PageTSConfig for the XPATH custom tag:

::

   RTE.default {

      showButtons := addToList(user)
      hideButtons := removeFromList(user)

      userElements {
         747 = XML Functions
         747 {
            10 = XPATH
            10.description = Executes a XPath query
            10.mode = wrap
            10.content = <xpath>|</xpath>

            20 = XSLT
            20.description = Executes a XSLT transformation
            20.mode = wrap
            20.content = <xslt>|</xslt>
         }
      }

      proc {
         allowTagsOutside := addToList(xpath)
         allowTags := addToList(xpath)
         entryHTMLparser_db {
            htmlSpecialChars = -1
            allowTags := addToList(xpath)
         }
      }

   }

We add the custom <xpath> tag to the various allowedTag lists in the
default configuration of the RTE. This makes it possible to enter the
tag directly without switching off the editor. The configuration of a
XML section in the userElements is optional and included here just for
completeness. Notice: If you use this, you will have to implement the
parsing of the custom tag slightly different than shown below, because
its not possible to set tag attributes in the userElements dialogue.

Next we need to configure lib.parseFunc and lib.parseFunc\_RTE for FE
rendering of our tag:

::

   lib.parseFunc {
      allowTags := addToList(xpath)
   }

   lib.parseFunc_RTE {
      allowTags := addToList(xpath)
   }

   # add typotag to parseFunc
   lib.parseFunc.tags.xpath = XPATH
   lib.parseFunc.tags.xpath {

      # tag is breaking up nonTypoTag content, content after must be re-wrapped
      breakoutTypoTagContent = 1

      # strip new lines before and after the tag
      stripNL = 1

      # get current content of tag as source (either XML or a path)
      source.data = current : 1

      # get the Xpath expression from the expression attribute of the tag
      expression.data = parameters : expression

      # get the return format from the format attribute of the tag
      return.data = parameters : return

      # configuration of the result
      resultObj = 1
      resultObj.cObjNum = 1
      resultObj 1.current = 1
   }

   lib.parseFunc_RTE.tags.xpath < lib.parseFunc.tags.xpath

First we added the <xpath> tag to the allowTags lists of both parsing
libraries. Then we configured the tag itself. Notice that its
important to set the breakoutTypoTagContent property, otherwise you
will have <p>s wrapped around your result. Another thing to remember
is that it is possible to get the attribute values of custom tags with
getText from the $cobj->parameters array. We can set them directly in
the corresponding properties of the XPATH content object by using
stdWrap. That's it. Now you can enter XPATH queries in the RTE and
display the results on your website.

All that is left is to improve the display of the tag in the RTE like
in the screenshot above. This is of course optional. For the example
above we inserted the following CSS rule in a custom RTE stylesheet:

.. code-block:: css

   xpath:before {
      content: "XPATH ["attr(expression)"] ["attr(return)"] :";
      display: inline-block;
      padding: 0 0.5em 0 0;
      font-family: monospace;
      font-weight: bold;
   }

The RTE normally will not display any tag attributes. But in our case
it can be helpful to see which expression is set. This can be achieved
with pure CSS using the :before pseudo-selector and the content
property in combination with CSS's attr() function. Nice :)
