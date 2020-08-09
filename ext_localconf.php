<?php
if (!defined('TYPO3_MODE')) {
    die('Not in Typo3');
}

// defines content object XPATH
$GLOBALS['TYPO3_CONF_VARS']['FE']['ContentObjects'] = array_merge($GLOBALS['TYPO3_CONF_VARS']['FE']['ContentObjects'], [
    'XPATH' =>  Digicademy\CobjXpath\ContentObject\XpathContentObject::class
]);

// define example RTE preset for XPATH TypoTag
$GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['cobj_xpath'] = 'EXT:cobj_xpath/Configuration/RTE/Default.yaml';
