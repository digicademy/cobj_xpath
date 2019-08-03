<?php
if (!defined('TYPO3_MODE')) {
    die('Not in Typo3');
}

// defines content object XPATH
$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_content.php']['cObjTypeAndClass'][] = array(
    0 => 'XPATH',
    1 => 'Digicademy\CobjXpath\ContentObject\XpathContentObject',
);

// define example RTE preset for XPATH TypoTag in TYPO3 8.7
$GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['cobj_xpath'] = 'EXT:cobj_xpath/Configuration/RTE/Default.yaml';
