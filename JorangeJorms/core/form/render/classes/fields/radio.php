<?php
if(!defined('ABSPATH')) exit; // Exit if accessed directly
?>
<div class="jorms-form-checkbox jorms-form-checkradio-wrapper">
    <div class="jorms-form-label"><?php echo $label ?></div>
    <fieldset>
        <legend><?php echo $label ?></legend>

        <?php foreach($options as $option):
            $option_value = $option['value'] ?? 'true';
            $option_id    = $option['id'] ?? $option['value'];
            $option_label = $option['label'] ?? $value;
            ?>
            <div class="jorms-form-radio-option">
                <input type="radio" id="<? echo ( $option_id ) ?>" value="<? echo $option_value ?>" name="<? echo $name ?>" <?php echo $value===$option_value?'checked':'' ?> >
                <label for="<? echo ( $option_id ) ?>"><?echo $option_label ?></label>
            </div>
        <?php endforeach; ?>
    </fieldset>
</div>