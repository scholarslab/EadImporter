<?php
    head(array('title' => 'EAD Import', 'bodyclass' => 'primary', 'content_class' => 'horizontal-nav'));
?>
<h1>EAD Import</h1>

<div id="primary">
	<?php echo flash(); ?>
 	<?php
            if (!empty($err)) {
                echo '<p class="error">' . html_escape($err) . '</p>';
            }
        ?>
        <p><a href="../index/">Return to form</a>.</p>
</div>

<?php 
    foot(); 
?>
