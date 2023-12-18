<?php
if(!defined('ABSPATH')) exit; // Exit if accessed directly

$repeater_fields = $field['fields'] ?? [];
if( $value && !is_array( $value ) )
    $value = array( $value );
?>

<div class="jorms-form-repeater" <?php if( $max ) echo "data-max='" . $max . "'"; if( $min ) echo "data-min='" . $min . "'"; ?> data-fields-num="<?echo count( $repeater_fields ) ?>">
    <?php if( $label ): ?>
    <label for='<?php echo $field_id ?>'><?php echo $label ?></label>
    <?php endif; ?>

    <script type="text/html" class="jorms-form-repeater-fields"><?php
        foreach( $repeater_fields as $field){
            $field['counter'] = $counter;
            $field_object = Jorms()->get_field( $field['type'] ?? false );
            if( $field_object ) $field_object->render( $field );
        } ?>
    </script>

    <div class="jorms-form-repeater-inner">
        <div class="jorms-form-repeater-inner-fields">
        <?php
        if ($value) {
            $is_single_field = count( $repeater_fields ) === 1;
            foreach ($value as $repeated_value){
                echo '<div class="jorms-form-repeater-group">';
                echo '<div class="jorms-form-repeater-group-fields' . ($is_single_field ? '' : ' grouped'). '">';
                foreach ($repeater_fields as $field) {
                    if( $is_single_field ) $field['value'] = $repeated_value;
                    else $field['value'] = $repeated_value[ $field['name'] ];
                    $field['counter'] = $counter;
                    $field_object = Jorms()->get_field($field['type'] ?? false);
                    if ($field_object) $field_object->render($field);
                }
                echo '</div>';

            //    echo '<a href=# role="button" icon="cancel" class="jorms-form-repeater-remove"><i role="img" aria-hidden="true" class="jorms-form-repeater-remove-icon"></i></a>';
                echo '</div>';
            }
            } ?>

        </div>
        <a class="jorms-form-repeater-add" href="#" type="button">Add</a>
    </div>


</div>
<?php if( $max ): ?>
    <span class="jorms-counter"><?php echo '<span class="jorms-current-count">' . ( $value ? count( $value ) : 0 ) . '</span>/' . $max; ?></span>
<?php endif; ?>