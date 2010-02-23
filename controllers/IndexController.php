<?php

class EadImporter_IndexController extends Omeka_Controller_Action
{
    public function indexAction() 
    {
		$form = $this->getUpdateForm();
		$this->view->form = $form;      
        
      /*  if (isset($_POST['ead_import_submit'])) {
            //make sure the user selected a file
            if (empty($_POST['ead_import_file_name'])) {
                $view->err = 'Please select a file to import.';
            } else {
                
                // make sure the file is correctly formatted
               // $eadImportFile = new EadImport_File($_POST['ead_import_file_name']);
                
                
               // if (!$eadImportFile->isValid(2)) {                    
               //     $view->err = "Your file is incorrectly formatted.  Please select a valid EAD file.";
                //} else {                    
                     //redirect to column mapping page
                    $this->redirect->goto('select-extracts');   
              //  }                
            }
        }*/

    }
    
    public function updateAction()
    {
		
    	$form = $this->getUpdateForm();
    	
    	if($_POST){
    		if($form->isValid($_POST)){
    			//Save the file
    			$uploadedData = $form->getValues();
    			$filename = $uploadedData["eaddoc"];
    			$this->view->filename = $filename;						
    			$form->eaddoc->receive();
    			$process = $this->processEad($filename);
    		}
    		else
    		{
    			$this->view->form = $form;
    		}
    	}
    	else {
    		$this->view->form = $form;
    	}
	}
	
	private function processEad($filename, $stylesheet=EAD_IMPORT_DOC_EXTRACTOR, $tmpdir=EAD_IMPORT_TMP_LOCATION){
		$xp = new XsltProcessor();
		$file = $tmpdir . DIRECTORY_SEPARATOR . $filename;

		 // create a DOM document and load the XSL stylesheet
		$xsl = new DomDocument;
		$xsl->load($stylesheet);
  
		// import the XSL styelsheet into the XSLT process
		$xp->importStylesheet($xsl);
		
		// create a DOM document and load the XML data
		$xml_doc = new DomDocument;
		$xml_doc->load($file);
		
		  // dump text to screen
		if ($doc = $xp->transformToXML($xml_doc)) {
			echo $doc;
			#$doc->save('temp.csv');
		} else {
			trigger_error('XSL transformation failed.', E_USER_ERROR);
		} // if 
		
		
		echo $file . '<br/>';
		echo $stylesheet;
		
	}
	
	private function getUpdateForm($tmpdir=EAD_IMPORT_TMP_LOCATION)
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
		
    	
    	//Submit button
    	$form->addElement('submit','submit');
    	$submitElement=$form->getElement('submit');
    	$submitElement->setLabel('Upload EAD Document');
    	
    	//$this->view->form = $form;
    	//echo $tmpdir;
    	return $form;
	}
    
   /* public function selectExtractsAction()
    {
        // get the session and view
        $eadImportSession = new Zend_Session_Namespace('EadImport');
        $itemsArePublic = $eadImportSession->eadImportItemsArePublic;
        $itemsAreFeatured = $eadImportSession->eadImportItemsAreFeatured;
        $collectionId = $eadImportSession->eadImportCollectionId;
        $stopImportIfFileDownloadError = $eadImportSession->eadImportStopImportIfFileDownloadError;
        
        $view = $this->view;

        // get the ead file to import
        $eadImportFile = $eadImportSession->eadImportFile;
                
        // pass the ead file to the view
        $view->err = '';
        $view->eadImportFile = $eadImportFile;      
                
        // process submitted column mappings
        if (isset($_POST['csv_import_submit'])) {
            
          
            }           
            
            // make sure the user select at least one type of data to be extracted
            if (count($extractSelections) == 0) {
                $view->err = 'Please select at least one type of data to be extracted.';
            }
            
            // if there are no errors with the extraction selctions, then run the import and goto the status page
            if (empty($view->err)) {
                
                // do the import in the background
                $eadImport = new EadImport_Import();
                $eadImport->initialize($eadImportFile->getFileName(), $collectionId, $itemsArePublic, $itemsAreFeatured, $stopImportIfFileDownloadError, $extractSelections);
                $eadImport->transform();
                foreach ($eadImport->getCsvImports() as $csvImport) {
                	$this->_backgroundImport($csvImport);
                }
                //redirect to column mapping page
                $this->flashSuccess("Successfully started import. Reload this page for status updates.");
                $this->redirect->goto('status');
                
            }  
        }   */
    
    public function statusAction() 
    {
        // get the session and view
        $eadImportSession = new Zend_Session_Namespace('EadImport');
        $view = $this->view;
        
        //get the imports
        $view->eadImports =  EadImport_Import::getImports();
    }
    
}