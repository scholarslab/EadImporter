<?php
    head(array('title' => 'EAD Import', 'bodyclass' => 'primary', 'content_class' => 'horizontal-nav'));
?>
<h1>EAD Import</h1>

<div id="primary">
  <p><?php echo $filename ?> successfully uploaded!</p>
  <p><?php if (isset($error)) { echo $error; } ?></p>
</div>

<?php 
    foot(); 
?>
