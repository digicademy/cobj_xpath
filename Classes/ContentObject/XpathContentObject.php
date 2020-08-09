<?php

namespace Digicademy\CobjXpath\ContentObject;

/***************************************************************
 *  Copyright notice
 *
 *  Torsten Schrade <Torsten.Schrade@adwmainz.de>, Academy of Sciences and Literature | Mainz
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Frontend\ContentObject\AbstractContentObject;
use TYPO3\CMS\Core\Utility\DebugUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\TimeTracker\TimeTracker;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

class XpathContentObject extends AbstractContentObject
{

    /**
     * Renders the XPATH content object
     *
     * @param array $conf TypoScript configuration of the cObj
     *
     * @return string
     */
    public function render($conf = [])
    {
        if (!is_array($conf)) {
            return '';
        }

        $ContentObjectRenderer = $this->getContentObjectRenderer();

        $content = '';

        // TimeTracker object is gone in TYPO3 8 but needed to set TS log messages; instantiate in versions >= 8.7
        if (VersionNumberUtility::convertVersionNumberToInteger(TYPO3_branch) >= 8007000 && !is_object($GLOBALS['TT'])) {
            $GLOBALS['TT'] = GeneralUtility::makeInstance(TimeTracker::class);
        }

        // Check if the SimpleXML extension is loaded
        if (!extension_loaded('SimpleXML') || !extension_loaded('libxml')) {
            $GLOBALS['TT']->setTSlogMessage('The PHP extensions SimpleXML and libxml must be loaded.', 3);

            return $ContentObjectRenderer->stdWrap($content, $conf['stdWrap.']);
        }

        // Fetch XML data - if source is neither a valid url nor a path, its considered a XML string
        if (isset($conf['source']) || is_array($conf['source.'])) {
            // First process the source string with stdWrap
            $xmlsource = $ContentObjectRenderer->stdWrap($conf['source'], $conf['source.']);
            // Fetch by (possible) path
            $path = GeneralUtility::getFileAbsFileName($xmlsource);
            if (@is_file($path) === true) {
                $xmlsource = GeneralUtility::getURL($path, 0, false);
                // Fetch by (possible) URL
            } elseif (GeneralUtility::isValidUrl($xmlsource) === true) {
                $xmlsource = GeneralUtility::getURL($xmlsource, 0, false);
            }
        } else {
            $GLOBALS['TT']->setTSlogMessage('Source for XML is not configured.', 3);
        }

        // XPATH expression - stdWrap capable
        if (isset($conf['expression']) || is_array($conf['expression.'])) {
            $expression = $ContentObjectRenderer->stdWrap($conf['expression'], $conf['expression.']);
        } else {
            $GLOBALS['TT']->setTSlogMessage('No XPath expression set.', 3);
        }

        // return type - stdWrap capable
        if (isset($conf['return']) || is_array($conf['return.'])) {
            $return = $ContentObjectRenderer->stdWrap($conf['return'], $conf['return.']);
        } else {
            $return = 'string';
            $GLOBALS['TT']->setTSlogMessage('No return type for XPATH is set - using string as default.', 2);
        }

        if (!empty($xmlsource) && !empty($expression)) {

            // Load a simpleXML object
            libxml_use_internal_errors(true);
            $xml = simplexml_load_string($xmlsource);

            if ($xml instanceof \SimpleXMLElement) {

                // Possible namespaces for query
                if (isset($conf['registerNamespace.']['getFromSource'])
                    && (boolean)$conf['registerNamespace.']['getFromSource'] === true
                ) {
                    $namespaces = array_merge($xml->getDocNamespaces(), $xml->getNamespaces());
                    // Print namespaces
                    if (isset($conf['registerNamespace.']['getFromSource.']['debug'])
                        && (boolean)$conf['registerNamespace.']['getFromSource.']['debug'] === true
                    ) {
                        DebugUtility::debug($namespaces);
                    }
                    if (count($namespaces) > 0
                        && isset($conf['registerNamespace.']['getFromSource.']['listNum'])
                        && is_array($conf['registerNamespace.']['getFromSource.']['listNum.'])
                    ) {
                        $listNumData = array();
                        foreach ($namespaces as $prefix => $ns) {
                            $listNumData[] = $prefix . '|' . $ns;
                        }
                        $listNumConf['listNum'] = $conf['registerNamespace.']['getFromSource.']['listNum'];
                        if (is_array($conf['registerNamespace.']['getFromSource.']['listNum.'])) {
                            $listNumConf['listNum.'] = $conf['registerNamespace.']['getFromSource.']['listNum.'];
                        }
                        $listNumConf['listNum.']['splitChar'] = ',';
                        $conf['registerNamespace'] = $ContentObjectRenderer->stdWrap_listNum(implode(',', $listNumData), $listNumConf);
                    } else {
                        $conf['registerNamespace'] = '';
                    }
                }

                if (isset($conf['registerNamespace'])) {
                    $namespace = GeneralUtility::trimExplode('|', $conf['registerNamespace'], 1);
                    if (count($namespace) == 2 && GeneralUtility::isValidUrl($namespace[1])) {
                        $xml->registerXPathNamespace($namespace[0], $namespace[1]);
                    }
                }

                // Perform XPATH query
                $result = $xml->xpath($expression);

                // If there was a result
                if (is_array($result) && count($result) > 0) {

                    // Switch return type
                    switch ($return) {

                        case 'count':
                            $result = count($result);
                            break;

                        case 'boolean':
                            $result = true;
                            break;

                        case 'xml':
                            foreach ($result as $key => $value) {
                                $result[$key] = $value->asXML();
                            }
                            break;

                        case 'array':
                            foreach ($result as $key => $value) {
                                // convert to real PHP array; idea from soloman at http://www.php.net/manual/en/book.simplexml.php
                                $json = json_encode($value);
                                $result[$key] = json_decode($json, true);
                            }
                            break;

                        case 'json':
                            foreach ($result as $key => $value) {
                                $result[$key] = json_encode($value);
                            }
                            break;

                        case 'string':
                        default:
                            foreach ($result as $key => $value) {
                                $result[$key] = (string)$value;
                            }
                            break;
                    }

                    // Possibility to return the result unprocessed (for example to a Fluid view helper or other calls from outside TypoScript)
                    if ($conf['returnRaw'] == 1) {
                        return $result;
                    }

                    // in case of a multi value result, provide further TypoScript processing with resultObj or implodeResult
                    if ($return !== 'count' && $return !== 'boolean') {

                        // resultObj
                        if (is_array($conf['resultObj.']) && !$conf['implodeResult']) {
                            // write the result array to this cObj's data and TSFE (for array access with TSFE:cObj|data)
                            $originalRecord = $ContentObjectRenderer->data;
                            $originalTSFERecord = $GLOBALS['TSFE']->cObj->data;
                            $ContentObjectRenderer->data = $result;
                            $GLOBALS['TSFE']->cObj->data = $result;
                            // use split for TypoScript iteration through the result
                            $conf['resultObj.']['token'] = '###COBJ_XPATH###';
                            $content = $ContentObjectRenderer->splitObj(implode('###COBJ_XPATH###', $result), $conf['resultObj.']);
                            // restore original data
                            $ContentObjectRenderer->data = $originalRecord;
                            $GLOBALS['TSFE']->cObj->data = $originalTSFERecord;

                            // implodeResult
                        } elseif ($conf['implodeResult'] == 1) {

                            if (is_array($conf['implodeResult.'])) {
                                $token = $ContentObjectRenderer->stdWrap($conf['implodeResult.']['token'],
                                    $conf['implodeResult.']['token.']);
                            } else {
                                $token = '###COBJ_XPATH###';
                            }
                            $content = implode($token, $result);

                        } else {
                            $GLOBALS['TT']->setTSlogMessage('Handling of multivalue result not configured. Please use resultObj or implodeResult', 2);
                        }
                        // all other cases
                    } else {
                        $content = $result;
                    }

                } else {
                    $GLOBALS['TT']->setTSlogMessage('The XPath query returned no results.', 2);
                }

            } else {
                $errors = libxml_get_errors();
                foreach ($errors as $error) {
                    $GLOBALS['TT']->setTSlogMessage('XML exception: ' . $this->getXmlErrorCode($error), 3);
                }
                libxml_clear_errors();
            }

        } else {
            $GLOBALS['TT']->setTSlogMessage('The configured XML source did not return any data or no XPATH expression was set.', 3);
        }

        return $ContentObjectRenderer->stdWrap($content, $conf['stdWrap.']);
    }

    /**
     * Returns XML error codes for the TSFE admin panel.
     * Function inspired by http://www.php.net/manual/en/function.libxml-get-errors.php
     *
     * @param \LibXMLError $error
     *
     * @return string
     */
    private function getXmlErrorCode(\LibXMLError $error)
    {
        $errormessage = '';

        switch ($error->level) {
            case LIBXML_ERR_WARNING:
                $errormessage .= 'Warning ' . $error->code . ': ';
                break;
            case LIBXML_ERR_ERROR:
                $errormessage .= 'Error ' . $error->code . ': ';
                break;
            case LIBXML_ERR_FATAL:
                $errormessage .= 'Fatal error ' . $error->code . ': ';
                break;
        }

        $errormessage .= trim($error->message) . ' - Line: ' . $error->line . ', Column:' . $error->column;

        if ($error->file) {
            $errormessage .= ' - File: ' . $error->file;
        }

        return $errormessage;
    }
}
