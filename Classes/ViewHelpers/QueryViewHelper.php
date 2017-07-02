<?php

namespace ADWLM\CobjXpath\ViewHelpers;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017 Torsten Schrade <Torsten.Schrade@adwmainz.de>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use ADWLM\CobjXpath\ContentObject\XpathContentObject;

/**
 * Usage:
 *
 * <xpath:query source="path/to/source.xml" expression="XPATH" return="count|boolean|string|array|json|xml" />
 *
 */

class QueryViewHelper extends AbstractViewHelper
{

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
     */
    protected $contentObject;

    /**
     * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
     *
     * @return void
     */
    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager) {
        $this->configurationManager = $configurationManager;
        $this->contentObject = $this->configurationManager->getContentObject();
        $this->contentObject->start(array(), '');
        $this->contentObject->cObjHookObjectsArr['XPATH'] = GeneralUtility::makeInstance(XpathContentObject::class);
    }

    /**
     * Fluid view helper wrapper for the XPATH content object. Calls the content object class directly. This makes it possible to
     * return multi value results (arrays) directly from the Fluid template. To achieve a result not automatically converted to string
     * "returnRaw" is always set to 1. Also see the inject method above and the direct instantiation of the XPATH cobj there.
     *
     * @param mixed $source
     * @param string $expression
     * @param string $return
     *
     * @return mixed
     */
    public function render($source = null, $expression = '', $return = 'string')
    {

        if ($source === null) {
            $source = $this->renderChildren();
        }

        $configuration = array(
            'source' => $source,
            'expression' => $expression,
            'return' => $return,
            'returnRaw' => 1
        );

        $content = $this->contentObject->cObjHookObjectsArr['XPATH']->cObjGetSingleExt('XPATH', $configuration, '', $this->contentObject);

        return $content;
    }
}
