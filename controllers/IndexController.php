<?php

class EadImporter_IndexController extends Omeka_Controller_Action
{
    public function indexAction() 
    {
		$form = $this->importForm();
		$this->view->form = $form;
    }
    
    public function updateAction()
    {
		
    	$form = $this->importForm();
    	$uploadedData = $form->getValues();
    	$filename = $uploadedData["eaddoc"];
    	
    	if($_POST && $filename != null){
    		if($form->isValid($_POST)){
    			//Save the file    			
    			$this->view->filename = $filename;		
    			$form->eaddoc->receive();    			
    			$this->flashSuccess("Received file " . $filename);			
    			//$process = $this->processEad($filename);
				$args = array();
				$args['filename'] = $filename;
    			
    			ProcessDispatcher::startProcess('EadImporter_ProcessEad', null, $args);	
    		}
    		else
    		{
    			$this->view->form = $form;
    			$this->flashError("Error receiving file.");
    		}
    	}
    	else {
    		$this->view->form = $form;
    		$this->flashError('Error receiving file or no file selected.');
    	}
	}
	
	/*private function processEad($filename, $stylesheet=EAD_IMPORT_DOC_EXTRACTOR, $tmpdir=EAD_IMPORT_TMP_LOCATION, $csvfilesdir=CSV_IMPORT_CSV_FILES_DIRECTORY){
		
		$xp = new XsltProcessor();
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
				//$process = $this->initializeCsvImport($basename);
							} else {
				$this->flashError("Could not transform XML file.  Be sure your EAD document is valid.");
			} // if 
		} else {
			$this->flashError("Error parsing EAD document.");
		}		
		} catch (Exception $e){
			$this->view->error = $e->getMessage();
			
		}
		
	}*/
	
	private function initializeCsvImport($basename, $csvImportDirectory = CSV_IMPORT_DIRECTORY){
		// get the session and view
        $csvImportSession = new Zend_Session_Namespace('CsvImport');
        $view = $this->view;
        
        $csvImportFile = $basename . '.csv';
        $itemsArePublic = '';
        $itemsAreFeatured = '';
        $collectionId = '';
        $stopImportIfFileDownloadError = '1';
        $csvImportItemTypeId = '1';
        $columnMaps = array();
        $columnMaps[0] = '50';
        $columnMaps[1] = '40';
        
		/*if (!$csvImportFile->isValid($maxRowsToValidate)) {                    
			$this->flashError('Your file is incorrectly formatted.  Please select a valid CSV file.');
		} else {                    
			// save csv file and item type to the session
			$csvImportSession->csvImportFile = $csvImportFile;                    
			$csvImportSession->csvImportItemTypeId = '1';
			$csvImportSession->csvImportItemsArePublic = '';
			$csvImportSession->csvImportItemsAreFeatured = '';
			$csvImportSession->csvImportCollectionId = '';
			$csvImportSession->csvImportStopImportIfFileDownloadError = '1';
			//redirect to column mapping page
			//$this->redirect->goto('/omeka/admin/plugins/csv-import/index/map-columns');
                }
        //$this->redirect->goto('../../csv-import/index/map-columns');   */
        
				// do the import in the background
                $csvImport = new CsvImport_Import();
                $csvImport->initialize($csvImportFile, $csvImportItemTypeId, $collectionId, $itemsArePublic, $itemsAreFeatured, $stopImportIfFileDownloadError, $columnMaps);
                $csvImport->status = CsvImport_Import::STATUS_IN_PROGRESS_IMPORT;
                $csvImport->save();
                
                // dispatch the background process to import the items
				/*$user = current_user();
				$args = array();
				$args['import_id'] = $csvImport->id;
				ProcessDispatcher::startProcess('CsvImport_ImportProcess', $user, $args);*/
                
                //redirect to column mapping page
                //$this->flashSuccess("Successfully started the import. Reload this page for status updates.");
                //$this->redirect->goto('status');
	}
	
	private function importForm($tmpdir=EAD_IMPORT_TMP_LOCATION)
	{
	    require "Zend/Form/Element.php";
    
	    //$path = EAD_IMPORT_TMP_LOCATION;
    	$form = new Zend_Form();
    	$form->setAction('update');
    	$form->setMethod('post');
    	$form->setAttrib('enctype', 'multipart/form-data');

    	$fileUploadElement = new Zend_Form_Element_File('eaddoc');
    	$fileUploadElement->setLabel('Select EAD file:');
    	$fileUploadElement->setDestination($tmpdir);
    	$fileUploadElement->addValidator('count', false, 1); 
    	$fileUploadElement->addValidator('extension', false, 'xml');
    	$form->addElement($fileUploadElement);
    	
    	//CSV import inputs
		//echo csv_import_get_item_types_drop_down('csv_import_item_type_id', 'Item Type');
		
		
		/*echo csv_import_get_collections_drop_down('csv_import_collection_id', 'Collection');
		echo csv_import_checkbox('csv_import_items_are_public', 'Items Are Public?', 'field');
		echo csv_import_checkbox('csv_import_items_are_featured', 'Items Are Featured?', 'field');
		echo csv_import_checkbox('csv_import_stop_import_if_file_download_error', 'Stop Import If A File For An Item Cannot Be Downloaded?', 'field', true);*/
    	
    	//Submit button
    	$form->addElement('submit','submit');
    	$submitElement=$form->getElement('submit');
    	$submitElement->setLabel('Upload EAD Document');
    	
    	//$this->view->form = $form;
    	//echo $tmpdir;
    	return $form;
	}
	
    /*public function statusAction() 
    {
        // get the session and view
        $eadImportSession = new Zend_Session_Namespace('EadImporter');
        $view = $this->view;
        
        //get the imports
        $view->eadImports =  EadImporter_Import::getImports();
    }*/
    
}