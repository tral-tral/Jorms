<?php
if(!defined('ABSPATH')) exit; // Exit if accessed directly

?>
<?php if( $label ): ?>
    <label for='<?php echo $field_id ?>'><?php echo $label ?></label>
<?php endif; ?>
<div class="jorms-form-place noplace">
    <input placeholder="Enter a business name" type='text' id='<?php echo $field_id  ?>' name='<?php echo $name ?>'/>
    <div class="jorms-form-place-wrapper">
        <div class="jorms-form-place-name jorms-form-label"></div>
        <img src alt="no photo found">
    </div>
</div>