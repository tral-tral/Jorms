<?php
if(!defined('ABSPATH')) exit; // Exit if accessed directly
if( $value && !is_array( $value ) )
    $value = array( $value );

?>
<?php if( $label ): ?>
    <label for='<?php echo $field_id ?>'><?php echo $label ?></label>
<?php endif; ?>
<select name='<?php echo $name  ?>'
        id='<?php echo $field_id ?>'
    <?php echo $required?'required':'' ?>>
    <?php $this->print_options( $options, $value )?>
</select>

