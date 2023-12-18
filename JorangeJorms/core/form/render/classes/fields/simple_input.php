<?php
if(!defined('ABSPATH')) exit; // Exit if accessed directly
if( !$value ) $value='';
?>
<?php if( $label ): ?>
    <label for='<?php echo $field_id ?>'><?php echo $label ?></label>
<?php endif; ?>
<input value='<?php echo $value ?>' type='<?php echo $type  ?>' id='<?php echo $field_id  ?>' name='<?php echo $name ?>'
    <?php if( $maxlength ) echo "maxlength='" . $maxlength . "'"; if( $minlength ) echo "minlength='" . $minlength . "'"; ?>
    <?php if( $max ) echo "max='" . $max . "'"; if( $min ) echo "min='" . $min . "'"; ?>
    <?php if( $step ) echo "step='" . $step . "'"; ?>
    <?php echo $required?'required':'' ?>
    <?php echo $readonly?'readonly':'' ?>
/>
<?php if( $maxlength ): ?>
    <span class="jorms-counter"><?php echo '<span class="jorms-current-count">' . ( $value ? strlen( $value ) : 0 ) . '</span>/' . $maxlength; ?></span>
<?php endif; ?>
