<?php

namespace Digicademy\CobjXpath\ViewHelpers;

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

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Digicademy\CobjXpath\ContentObject\XpathContentObject;

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
        $this->contentObject->cObjHookObjectsArr['XPATH'] = GeneralUtility::makeInstance(XpathContentObject::class, $this->contentObject);
    }

    /**
     * Initialize ViewHelper arguments
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('source', 'null', 'source', false, null);
        $this->registerArgument('expression', 'string', 'expression', false, '');
        $this->registerArgument('return', 'string', 'return', false, 'string');
        $this->registerArgument('namespace', 'string', 'Name Spaces', false, null);
    }

    /**
     * Fluid view helper wrapper for the XPATH content object. Calls the content object class directly. This makes it possible to
     * return multi value results (arrays) directly from the Fluid template. To achieve a result not automatically converted to string
     * "returnRaw" is always set to 1. Also see the inject method above and the direct instantiation of the XPATH cobj there.
     *
     * @return mixed
     */
    public function render()
    {
        $source = $this->arguments['source'];
        $expression = $this->arguments['expression'];
        $return = $this->arguments['return'];
        $namespace = $this->arguments['namespace'];

        if ($source === null) {
            $source = $this->renderChildren();
        }

        $configuration = array(
            'source' => $source,
            'expression' => $expression,
            'return' => $return,
            'registerNamespace' => $namespace,
            'returnRaw' => 1
        );

        return $this->contentObject->cObjHookObjectsArr['XPATH']->render($configuration);
    }
}
