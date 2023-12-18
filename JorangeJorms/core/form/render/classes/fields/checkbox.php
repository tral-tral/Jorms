<?php
if(!defined('ABSPATH')) exit; // Exit if accessed directly
if( $value && !is_array( $value ) )
    $value = array( $value );
?>
<div class="jorms-form-checkbox jorms-form-checkradio-wrapper">
    <div class="jorms-form-label"><?php echo $label ?></div>
    <fieldset>
        <legend><?php echo $label ?></legend>

    <?php foreach($options as $option):
        $option_value = $option['value'] ?? 'true';
        $option_id    = $option['id'] ?? $option['value'];
        $option_label = $option['label'] ?? $option_value;
        ?>
    <div class="jorms-form-check-option">
        <input type="checkbox" id="<? echo ( $option_id ) ?>" value="<? echo $option_value ?>" name="<? echo $name ?>" <?php if( in_array( $option_value, $value ) ) echo ' checked'; ?>>
        <label for="<? echo ( $option_id ) ?>"><?echo $option_label ?></label>
    </div>
    <?php endforeach; ?>
    </fieldset>
    <?php if( $max ): ?>
        <span class="jorms-counter"><?php echo '<span class="jorms-current-count">' . ( $value ? length( $value ) : 0 ) . '</span>/' . $max; ?></span>
    <?php endif; ?>
</div>