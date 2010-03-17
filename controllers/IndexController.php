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
    	$uploadedData = $form->getValues();
    	$filename = $uploadedData["eaddoc"];
    	
    	if ($_POST && $filename != null) {
    		if ($form->isValid($_POST)) {
    			
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
    					ProcessDispatcher::startProcess('EadImporter_ProcessEad', null, $args);
					    $this->flashSuccess("Received file " . $filename);
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
    	  	
    	//Submit button
    	$form->addElement('submit','submit');
    	$submitElement=$form->getElement('submit');
    	$submitElement->setLabel('Upload EAD Document');
    	
    	return $form;
	}
}