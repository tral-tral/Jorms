<?php
if(!defined('ABSPATH')) exit; // Exit if accessed directly
if( !$value ) $value='';
?>
<?php if( $label ): ?>
    <label for='<?php echo $field_id ?>'><?php echo $label ?></label>
<?php endif; ?>
<textarea rows="6"
         <?php if( $maxlength ) echo "maxlength='" . $maxlength . "'"; if( $minlength ) echo "minlength='" . $minlength . "'"; ?>
        name='<?php echo $name ?>' id='<?php echo $field_id?>' <?php echo $required?'required':'' ?>><?php echo $value ?>
</textarea>
<?php if( $maxlength ): ?>
    <span class="jorms-counter"><?php echo '<span class="jorms-current-count">' . ( $value ? strlen( $value ) : 0 ) . '</span>/' . $maxlength; ?></span>
<?php endif; ?>