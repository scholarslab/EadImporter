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

define('EAD_IMPORT_DIRECTORY', dirname(__FILE__));
define('EAD_IMPORT_DOC_EXTRACTOR', EAD_IMPORT_DIRECTORY . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'ead-import-documents.xsl');
define('EAD_IMPORT_PERSONS_EXTRACTOR', EAD_IMPORT_DIRECTORY . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'ead-import-persons.xsl');


add_plugin_hook('install', 'ead_import_install');
add_plugin_hook('uninstall', 'ead_import_uninstall');
add_plugin_hook('config_form', 'ead_import_config_form');
add_plugin_hook('config', 'ead_import_config');
//add_plugin_hook('define_acl', 'ead_import_define_acl');

function ead_import_install()
{
	// we require the CsvImport plugin for support
	try {
		// check for CSV_IMPORT_DIRECTORY;
	}
	catch (Exception $e) {
		throw new Zend_Exception("This plugin requires the CsvImport plugin");
	}
	try {
		$xh = xslt_create(); // we check for the ability to use XSLT
	}
	catch (Exception $e) {
		throw new Zend_Exception("This plugin requires XSLT support");
	}
	define('EAD_FILES_DIRECTORY', CSV_IMPORT_CSV_FILES_DIRECTORY);
}




?>