<?php
namespace Jorms;
if(!defined('ABSPATH')) exit; // Exit if accessed directly


/*
 * NOT IN USE
 */

$forms = $this->get_form_data();
style_( 'jorms_admin');
script_( ['jorms_admin',[] ] );
?>

<h1>Jorms Forms</h1>
<div id="jorms_forms_admin">
    <a href="#" class="jorms_forms_admin-add button">Add new</a>
    <?php foreach( $forms as $form ): ?>
        <div class="jorms_form_admin-form" data-id="<?php echo $form->ID; ?>">
            <div>
                <label for="jorms_forms_admin-handle">Form handle</label>
                <input type="text" name="jorms_form_admin-handle" value="<?php echo $form->handle; ?>"/>
            </div>
            <div>
                <label for="jorms_forms_admin-json">Form JSON</label>
                <textarea name="jorms_form-json"><?php echo $form->json; ?></textarea>
            </div>
            <a href="#" class="jorms_forms_admin-remove button">Remove</a>
        </div>
    <?php endforeach; ?>
    <a href="#" class="jorms_forms_admin-save button" data-nonce="<?php echo wp_create_nonce('jorms_forms_admin-save'); ?>">Save</a>
</div>