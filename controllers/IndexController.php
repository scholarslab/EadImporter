<?php

class EadImporter_IndexController extends Omeka_Controller_Action
{
    public function indexAction() 
    {
		$form = $this->importForm();
		$this->view->form = $form;
    }
    
    public function updateAction($tmpdir = EAD_IMPORT_TMP_LOCATION)
    {
    	$form = $this->importForm();
    	
    	//echo var_dump($filter_string);
    	//$itemsArePublic = $uploadedData["ead_importer_items_are_public"];
    	
    	if ($_POST) {
    		if ($form->isValid($this->_request->getPost() )) {
				$uploadedData = $form->getValues();
				$filename = $uploadedData['eaddoc'];
    			
    			//Save the file    			
    			$this->view->filename = $filename;		
    			$form->eaddoc->receive();    			
    			
    			$file = $tmpdir . DIRECTORY_SEPARATOR . $filename;
    			$basename = basename($file, '.xml');
    			$xml_doc = new DomDocument;
				$xml_doc->load($file);
				
				try { 
					if (simplexml_import_dom($xml_doc)){
						$args = array();
						$args['filename'] = $filename;
						$args['ead_importer_items_are_public'] = $uploadedData['ead_importer_items_are_public'];
						$args['ead_importer_items_are_featured'] = $uploadedData['ead_importer_items_are_featured'];
						$args['ead_importer_collection_id'] = $uploadedData['ead_importer_collection_id'];
						$args['filterstring'] = $uploadedData['filterstring'];
						
    					ProcessDispatcher::startProcess('EadImporter_ProcessEad', null, $args);
					    $this->flashSuccess("Received file " . $filename . " Check the CSV Import status page for updates.  Note that large EAD files may take a minute or two to appear in the status page.");
					   
					} else {
						$this->flashError("Error parsing EAD document or no file selected.");
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
	
	function get_collections($params = array(), $limit = 10)
	{
    	return get_db()->getTable('Collection')->findBy($params, $limit);
	}
	
	private function importForm($tmpdir=EAD_IMPORT_TMP_LOCATION)
	{
	    require "Zend/Form/Element.php";	

	    //Get collections table and load into array
	    $collections = array();
		$collectionObjects = get_db()->getTable('Collection')->findAll();
		foreach($collectionObjects as $collectionObject) {
			$collections[$collectionObject->id] = $collectionObject->name;
		}
	    
    	$form = new Zend_Form();
    	$form->setAction('update');
    	$form->setMethod('post');
    	$form->setAttrib('enctype', 'multipart/form-data');

    	//EAD file upload controls
    	$fileUploadElement = new Zend_Form_Element_File('eaddoc');
    	$fileUploadElement->setLabel('Select EAD file:');
    	$fileUploadElement->setDestination($tmpdir);
    	$fileUploadElement->addValidator('count', false, 1); 
    	$fileUploadElement->addValidator('extension', false, 'xml');        	
    	$form->addElement($fileUploadElement);
    	
    	//Collection
    	 $collectionId = new Zend_Form_Element_Select('ead_importer_collection_id');
    	 $collectionId->setLabel('Collection')
    	 		->addMultiOptions($collections);				
    	 $form->addElement($collectionId);
    	
    	//Items are Public?
    	$itemsArePublic = new Zend_Form_Element_Checkbox('ead_importer_items_are_public');
    	$itemsArePublic->setLabel('Items Are Public?');
    	$form->addElement($itemsArePublic);
    	
    	//Items are Featured?
    	$itemsAreFeatured = new Zend_Form_Element_Checkbox('ead_importer_items_are_featured');
    	$itemsAreFeatured->setLabel('Items Are Featured?');
    	$form->addElement($itemsAreFeatured);

    	//Filter Text
    	$textElement = new Zend_Form_Element_Text('filterstring');
    	$textElement->addFilter('StringToLower');
    	$textElement->setLabel('Filter by String:');
    	$form->addElement($textElement);    	
    	  	
    	//Submit button
    	$form->addElement('submit','submit');
    	$submitElement=$form->getElement('submit');
    	$submitElement->setLabel('Upload EAD Document');
    	
    	return $form;
	}
}