<?php
    head(array('title' => 'EAD Import', 'bodyclass' => 'primary', 'content_class' => 'horizontal-nav'));
?>
<h1>EAD Import</h1>

<div id="primary">
    <h2>Select File and Item Settings</h2>
    <p>Select an EAD file to upload and item settings for import into the Omeka database.  
    Items are imported as Documents and the column mapping is predefined based on 
    the EAD-to-Dublin Core crosswalk.  Simple filtering by string matching is available.</p>
    <?php echo $form; ?>
</div>

<?php 
    foot(); 
?>
