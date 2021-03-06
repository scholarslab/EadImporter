<?php

/**
 * EadImport plugin
 *
 * @copyright  Adam Soroka, 2009
 * @license    http://www.apache.org/licenses/LICENSE-2.0.html
 * @version    $Id:$
 * @package EadImport
 * @author ajs6f
 **/

if (!defined('EAD_IMPORT_DIRECTORY')) {
    define('EAD_IMPORT_DIRECTORY', dirname(__FILE__));
}

if (!defined('EAD_IMPORT_TMP_LOCATION')) {
    define('EAD_IMPORT_TMP_LOCATION', EAD_IMPORT_DIRECTORY . DIRECTORY_SEPARATOR . 'xmldump');
}

if (!defined('EAD_IMPORT_DOC_EXTRACTOR')) {
    define('EAD_IMPORT_DOC_EXTRACTOR', EAD_IMPORT_DIRECTORY . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'ead-import-documents.xsl');
}

if (!defined('EAD_IMPORT_PERSONS_EXTRACTOR')) {
    define('EAD_IMPORT_PERSONS_EXTRACTOR', EAD_IMPORT_DIRECTORY . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'ead-import-persons.xsl');
}

add_plugin_hook('install', 'ead_import_install');
add_plugin_hook('define_acl', 'ead_import_define_acl');
add_plugin_hook('admin_theme_header', 'ead_importer_admin_header');
add_filter('admin_navigation_main', 'ead_import_admin_navigation');

function ead_import_install()
{
    try {
        $xh = new XSLTProcessor; // we check for the ability to use XSLT
    } catch (Exception $exception) {
        throw new Zend_Exception("This plugin requires XSLT support");
    }
}

/**
 * Add the admin navigation for the plugin.
 * 
 * @return array
 */
function ead_import_admin_navigation($tabs)
{
    if (get_acl()->checkUserPermission('EadImporter_Index', 'index')) {
        $tabs['EAD Import'] = uri('ead-importer/index/');        
    }
    return $tabs;
}

function ead_import_define_acl($acl)
{
    $acl->loadResourceList(array('EadImporter_Index' => array('index', 'status')));
}

function ead_importer_admin_header($request)
{
    if ($request->getModuleName() == 'ead-importer') {
        //echo js('jquery-1.4.2.min');
        //echo js('ead_importer_main');
        echo '<link rel="stylesheet" href="' . html_escape(css('ead_importer_main')) . '" />';
    }
}
