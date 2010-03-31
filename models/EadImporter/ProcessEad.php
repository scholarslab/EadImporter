<?php

class EadImporter_ProcessEad extends ProcessAbstract 
{ 
	public function run($args, $stylesheet=EAD_IMPORT_DOC_EXTRACTOR, $tmpdir=EAD_IMPORT_TMP_LOCATION, $csvfilesdir=CSV_IMPORT_CSV_FILES_DIRECTORY, $csvImportDirectory = CSV_IMPORT_DIRECTORY) 
	{		
		$xp = new XsltProcessor();	

		//Get variables from args array passed into detached process
		$filename = $args['filename'];
		$itemsArePublic = $args['ead_importer_items_are_public'];
		$itemsAreFeatured = $args['ead_importer_items_are_featured'];
		$collectionId = $args['ead_importer_collection_id'];
		$filter_string = $args['filterstring'];
		
		//set query parameter to pass into stylesheet
		$xp->setParameter( '', 'query', $filter_string);
		
		//set path to xml file in order to load it
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
			if ($doc = $xp->transformToXML($xml_doc)) {			
				$csvFilename = $csvfilesdir . DIRECTORY_SEPARATOR . $basename . '.csv';
				$documentFile = fopen($csvFilename, 'w');
				fwrite($documentFile, $doc);
				fclose($documentFile);
				$this->_initializeCsvImport($basename, $itemsArePublic, $itemsAreFeatured, $collectionId);
				$this->flashSuccess("Successfully generated CSV File");
			} else {
				$this->flashError("Could not transform XML file.  Be sure your EAD document is valid.");
			}
		} catch (Exception $e){
			$this->view->error = $e->getMessage();
		}
	}
	
	private function _initializeCsvImport($basename, $itemsArePublic, $itemsAreFeatured, $collectionId, $csvImportDirectory = CSV_IMPORT_DIRECTORY)
	{	    
	
			/* this function does automatic column mapping and sets attributes necessary for the csvImport plugin,
			* such as defining whether or not the items are public and what collection they belong to
			*/
	
			$csvImportFile = $basename . '.csv';
	        $stopImportIfFileDownloadError = '1';
	        $csvImportItemTypeId = '1';
	        $columnMaps = array();
	        
	        //Title
			$columnMap = new CsvImport_ColumnMap('0', CsvImport_ColumnMap::TARGET_TYPE_ELEMENT);
			$columnMap->addElementId('50');
			$columnMaps[] = $columnMap;
			
			//Date
			$columnMap = new CsvImport_ColumnMap('1', CsvImport_ColumnMap::TARGET_TYPE_ELEMENT);
			$columnMap->addElementId('40');
			$columnMaps[] = $columnMap;
			
			//Creator
			$columnMap = new CsvImport_ColumnMap('2', CsvImport_ColumnMap::TARGET_TYPE_ELEMENT);
			$columnMap->addElementId('39');
			$columnMaps[] = $columnMap;
			
			//Publisher
			$columnMap = new CsvImport_ColumnMap('3', CsvImport_ColumnMap::TARGET_TYPE_ELEMENT);
			$columnMap->addElementId('45');
			$columnMaps[] = $columnMap;
			
			//Format
			$columnMap = new CsvImport_ColumnMap('4', CsvImport_ColumnMap::TARGET_TYPE_ELEMENT);
			$columnMap->addElementId('42');
			$columnMaps[] = $columnMap;
			
			//Identifier
			$columnMap = new CsvImport_ColumnMap('5', CsvImport_ColumnMap::TARGET_TYPE_ELEMENT);
			$columnMap->addElementId('43');
			$columnMaps[] = $columnMap;
			
			//Coverage
			$columnMap = new CsvImport_ColumnMap('6', CsvImport_ColumnMap::TARGET_TYPE_ELEMENT);
			$columnMap->addElementId('38');
			$columnMaps[] = $columnMap;
			
			//Description
			$columnMap = new CsvImport_ColumnMap('7', CsvImport_ColumnMap::TARGET_TYPE_ELEMENT);
			$columnMap->addElementId('41');
			$columnMaps[] = $columnMap;
			
			//Language
			$columnMap = new CsvImport_ColumnMap('8', CsvImport_ColumnMap::TARGET_TYPE_ELEMENT);
			$columnMap->addElementId('44');
			$columnMaps[] = $columnMap;
			
			//Type
			$columnMap = new CsvImport_ColumnMap('9', CsvImport_ColumnMap::TARGET_TYPE_ELEMENT);
			$columnMap->addElementId('51');
			$columnMaps[] = $columnMap;
			
			//Subject
			$columnMap = new CsvImport_ColumnMap('10', CsvImport_ColumnMap::TARGET_TYPE_ELEMENT);
			$columnMap->addElementId('49');
			$columnMaps[] = $columnMap;
			
			//Rights
			$columnMap = new CsvImport_ColumnMap('11', CsvImport_ColumnMap::TARGET_TYPE_ELEMENT);
			$columnMap->addElementId('47');
			$columnMaps[] = $columnMap;
        
			// do the import in the background
			$csvImport = new CsvImport_Import();
			$csvImport->initialize($csvImportFile, $csvImportItemTypeId, $collectionId, $itemsArePublic, $itemsAreFeatured, $stopImportIfFileDownloadError, $columnMaps);
			$csvImport->status = CsvImport_Import::STATUS_IN_PROGRESS_IMPORT;
			$csvImport->save();
                
            // dispatch the background process to import the items
			$user = current_user();
			$args = array();
			$args['import_id'] = $csvImport->id;
			ProcessDispatcher::startProcess('CsvImport_ImportProcess', $user, $args);                
	}
}