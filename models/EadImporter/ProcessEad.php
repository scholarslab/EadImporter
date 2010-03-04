<?php

class EadImporter_ProcessEad extends ProcessAbstract { 

	public function run($args, $stylesheet=EAD_IMPORT_DOC_EXTRACTOR, $tmpdir=EAD_IMPORT_TMP_LOCATION, $csvfilesdir=CSV_IMPORT_CSV_FILES_DIRECTORY) {		
       //get the xml file using $args['filename'] and process away...
		$xp = new XsltProcessor();
		$filename = $args['filename'];
		$file = $tmpdir . DIRECTORY_SEPARATOR . $filename;
		$basename = basename($file, '.xml');

		 // create a DOM document and load the XSL stylesheet
		$xsl = new DomDocument;
		$xsl->load($stylesheet);
  
		// import the XSL styelsheet into the XSLT process
		$xp->importStylesheet($xsl);
		
		// create a DOM document and load the XML data
		$xml_doc = new DomDocument;
		$xml_doc->load($file);
		
		// write transformed csv file to the csv file folder in the csvImport directory
		try { 
		if (simplexml_import_dom($xml_doc)){
			if ($doc = $xp->transformToXML($xml_doc)) {			
				$csvFilename = $csvfilesdir . DIRECTORY_SEPARATOR . $basename . '.csv';
				$documentFile = fopen($csvFilename, 'w');
				fwrite($documentFile, $doc);
				fclose($documentFile);
				$this->flashSuccess("Successfully generated CSV File");				
				//execute first step of the CSV import workflow
				$process = $this->initializeCsvImport($basename);
							} else {
				$this->flashError("Could not transform XML file.  Be sure your EAD document is valid.");
			} // if 
		} else {
			$this->flashError("Error parsing EAD document.");
		}		
		} catch (Exception $e){
			$this->view->error = $e->getMessage();
			
		}
	}
}