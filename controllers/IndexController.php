<?php

class EadImport_IndexController extends CsvImport_IndexController
{
    public function indexAction() 
    {
        // get the session and view
        $eadImportSession = new Zend_Session_Namespace('EadImport');
        $view = $this->view;
        
        // check the form submit button
        $view->err = '';
        if (isset($_POST['ead_import_submit'])) {
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
        }
        
    }
    
    public function selectExtractsAction()
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
        }   
    
    public function statusAction() 
    {
        // get the session and view
        $eadImportSession = new Zend_Session_Namespace('EadImport');
        $view = $this->view;
        
        //get the imports
        $view->eadImports =  EadImport_Import::getImports();
    }
    
}