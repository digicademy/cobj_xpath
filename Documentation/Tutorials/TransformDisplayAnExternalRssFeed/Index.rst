.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


Transform & display an external RSS feed
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

It is quite common to display content from an external XML feed. There
are many good extensions in TER for this. Most of them take the approach
to import the external source into the TYPO3 database. The XPATH content
object can serve as "TypoScript only" solution.

Say we want to display the official TYPO3 newsfeed from
http://news.typo3.org/rss.xml on our website. Using the XPATH object,
we can retrieve the XML feed, select it's items and format them with
TypoScript's parseFunc. Let's start with the basics:

::

   page.10 = XPATH
   page.10 {

      # first we set the source to the newsfeed url
      source = http://news.typo3.org/rss.xml

      # each news entry is wrapped in <item> tags, fetch them with XPATH expression
      expression = //item

      # return the <item>s as XML which is going to be formatted with parseFunc later on
      return = xml

      # before we do the parseFunc stuff, just return the content to see what we've got
      resultObj {
         cObjNum = 1
         1.current = 1
      }

      # let's us display the output on the website for analysis
      stdWrap.htmlSpecialChars = 1
   }

When we reload the page we can see the items matched by our XPATH
query:

.. code-block:: xml

   <item>
      <title>FLOW3 1.0.3 has been released</title>
      <link>
         http://news.typo3.org/news/article/flow3-103-has-been-released/
      </link>
      <description>
         FLOW3 1.0.3, the third patch release of the PHP application framework has been released.
      </description>
      <category>Development</category>
      <category>FLOW3</category>
      <category>www.typo3.org</category>
      <pubDate>Sat, 25 Feb 2012 21:30:00 +0100</pubDate>
   </item>

We want to translate this to the following HTML

.. code-block:: html

   <div>
      <h1>FLOW3 1.0.3 has been released</h1>
      <p>Tags: 
         <span class="category">Development</span>,
         <span class="category">FLOW3</span>, 
         <span class="category">www.typo3.org</span>
      </p>
      <p>
         FLOW3 1.0.3, the third patch release of the PHP application framework has been released.
      </p>
      <p>
         <a href="http://news.typo3.org/news/article/flow3-103-has-been-released/">
         Read more...
      </a>
      </p>
   </div>

Several things need to be considered. First the easy ones: The <item>,
<title> and <description> tags need to be transformed to <div>, <h1>
and <p> respectively. That shouldn't be too hard. The <link> tag needs
to be transformed to an <a> tag where the href attribute has to be set
to the former tag's content. The content for the <a> tag needs to be
set to “Read more”. Finally, there are several <category> tags that
need to be collected within one <p> and transformed to <span> tags
with a class “category” assigned. Let's see what TSRef and parseFunc
have to offer for this scenario.

parseFunc's “externalBlocks” property comes to our help. In
tt\_content, “externalBlocks” is used to pre-split bodytext content
and parse <table> and <blockquote> tags and their according children.
In our case, we can use it to replace the incoming <item> tags and
pass the content once again into parseFunc to do the <category>
collection and process the <link> tag.

The next step shows you the finished setup:

::

   page.10 = XPATH
   page.10 {

      # first we set the source to the newsfeed url
      source = http://news.typo3.org/rss.xml

      # each news entry is wrapped in <item> tags, fetch them with XPATH expression
      expression = //item

      # return the <item>s as XML which is going to be formatted with parseFunc later on
      return = xml

      # configure the resultObj
      resultObj {

         cObjNum = 1

         1.current = 1
         1.parseFunc {

            # use externalBlocks to select the <item> tags
            externalBlocks = item
            externalBlocks.item {

               # and send their content once more into parsFunc
               callRecursive  = 1
               # take out <item> tag
               callRecursive.dontWrapSelf = 1

               # use stdWrap to wrap with <div>
               stdWrap {

                  wrap = <div> | </div>

                  # and now load a COA to work on the rest of the XML content
                  cObject = COA
                  cObject {

                     # get the current XML data first
                     5 = LOAD_REGISTER
                     5.item.data = current:1

                     # and now use some XPATH cobj to select the content; <title> first
                     10 = XPATH
                     10 {
                        # item register from .5
                        source.data = register:item
                        return = string
                        expression = //title
                        resultObj {
                           cObjNum = 1
                           1.wrap = <h1>|</h1>
                           1.current = 1
                        }
                     }

                     # <category> collection next
                     15 < .10
                     15 {
                        expression = //category

                        resultObj {
                           # use option split, so the last <category> doesn't get a ,
                           cObjNum = |*|1|*|2

                           1.wrap >
                           1.noTrimWrap = |<span class="category">|</span>, |

                           2.current = 1
                           2.wrap = <span class="category">|</span>

                           stdWrap.noTrimWrap = |<p>Tags: |</p>|
                        }
                     }

                     # next select the <description> and wrap in <p>
                     20 < .10
                     20 {
                        expression = //description
                        resultObj.1.wrap = <p>|</p>
                     }

                     # and finally select the <link> and wrap this in an <a> tag
                     30 < .10
                     30 {
                        expression = //link
                        resultObj.1.wrap = <p><a href="|">Read more...</a></p>
                     }
                  }
               }
            }
         }
      }
   }

Admittedly, this is quite a bit of TypoScript. On the other hand it only
uses standard functionality and at the same time demonstrates how you
can “chain” XPATH objects to flexibly work on your XML data.

The transformation could have been achieved much simpler using an XSL
stylesheet. This is precisely what the `XSLT content object
<http://typo3.org/extensions/repository/view/cobj_xslt>`_ is all
about. Check it out in TER, you'll find a tutorial very similar to
this one where the transformation is done with an XSL stylesheet.
