<?php
if(!defined('ABSPATH')) exit; // Exit if accessed directly

?>

<div class="jorms-form-checkradio <?php echo $type ?>">
    <label for='<?php   echo $field_id  ?>'><?php echo $label ?></label>
    <input type="hidden" value='<?php echo $value ?>'
           name='<?php  echo $name  ?>'
           id='<?php echo $field_id ?>'
        <?php echo $required?'required':'' ?>
    />
    <div class="jorms-form-checkradio-options">
        <?php foreach($options as $option):
            ?>
            <a href="#" class="jorms-form-checkradio-option <?php echo ( ( $value == $option['value'] || in_array( $option['value'], $value_array) )  )?'checked':'';?>" data-value="<?php echo $option['value']?>">
                <i class="jorms-form-checkradio-box icon"></i>
                <span class="jorms-form-checkradio-label">
				<?php echo $option['label']; ?>
			</span>
            </a>
        <?php endforeach; ?>
    </div>
</div>
