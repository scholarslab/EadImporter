<?php

class EadImporter_IndexController extends Omeka_Controller_Action
{
    public function indexAction() 
    {
		$form = $this->importForm();
		$this->view->form = $form;
    }
    
    public function updateAction($tmpdir=EAD_IMPORT_TMP_LOCATION)
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
    			
    			$file = $tmpdir . DIRECTORY_SEPARATOR . $filename;
    			$basename = basename($file, '.xml');
    			$xml_doc = new DomDocument;
				$xml_doc->load($file);				
				
				try { 
					if (simplexml_import_dom($xml_doc)){
						$args = array();
						$args['filename'] = $filename; 			
    					ProcessDispatcher::startProcess('EadImporter_ProcessEad', null, $args);
    					$process = $this->initializeCsvImport($basename);
					} else {
						$this->flashError("Error parsing EAD document.");
					}		
				} catch (Exception $e){
					$this->view->error = $e->getMessage();
				}
					
				

    		}
    		else
    		{
    			$this->view->form = $form;
    			$this->flashError("Error receiving file.");
    		}
    	}
    	else {
    		$this->view->form = $form;
    		$this->flashError('Error receiving file or no file selected--verify that it is an XML document.');
    	}
	}
	
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
                
			//redirect to column mapping page
			$this->flashSuccess("Successfully started the import. Check the CSV Import status page for updates.");
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