<?php
if(!defined('ABSPATH')) exit; // Exit if accessed directly

//TODO: Add separate JS for single image or multiple images.  No need to open gallery selection for one image.

$amount = $field['amount'] ?? 1;
$original_amount = $amount;

$nonce = wp_create_nonce( 'jorms_upload_nonce_' . $field_id );
if( $value ) {
    if (!is_array($value))
        $value = array($value);

}

if( $value ){
    $values_array = [];
    foreach( $value as $img ){
        $amount--;
        if( $amount<0 )break;
        $src = wp_get_attachment_image_src( $img );
        if( empty( $src) ) continue;
        //	echo 'TEST IMAGE ' .  get_post_meta( $img, 'mspas_parent',true ) ;
        $path =  ABSPATH . parse_url($src[0], PHP_URL_PATH);
        $file_name = basename( $path );
        $file_size = filesize( $path );
        $file_type = mime_content_type( $path );

        $values_array[] = [ 'name' => $file_name, 'size'=> $file_size, 'type' => $file_type, 'url' => $src[0], 'attachment_id' => $img ];

    }

    $json = json_encode( $values_array , JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
    $value_json = htmlspecialchars($json, ENT_QUOTES, 'UTF-8');
} else $value_json = 'false';

//\Jorms\dropzone_script();
//<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-upload">
//  <path d="M12 16v-4m0 0-3 3m3-3 3 3M4 9a8 8 0 0 1 8-8c.5 0 1 .04 1.5.12A5.5 5.5 0 0 1 17 9h2a7.98 7.98 0 0 0-7.5-6.97A7.98 7.98 0 0 0 4 9zm0 0v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9"/>
//</svg>
?>

<div class="jorms-form-upload-wrapper">
    <?php if( $label ): ?>
        <div class="jorms-form-label"><?php echo $label ?></div>
    <?php endif; ?>
    <div class="jorms-form-upload-image-container dropzone" id="<?php echo $field_id ?>" data-amount="<?php echo $original_amount; ?>" data-nonce="<?php echo $nonce; ?>" data-default="<?php echo $value_json ?>">
        <div class="dz-message"><button class="dz-button" type="button"><svg xmlns="http://www.w3.org/2000/svg" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-upload">
                      <path d="M12 16v-4m0 0-3 3m3-3 3 3M4 9a8 8 0 0 1 8-8c.5 0 1 .04 1.5.12A5.5 5.5 0 0 1 17 9h2a7.98 7.98 0 0 0-7.5-6.97A7.98 7.98 0 0 0 4 9zm0 0v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9"/>
                    </svg><div class="jorms-form-instructions">Drag to or click here</div>
            </button></div>
        <?php

        while( $original_amount-- > 0 ){
            echo '<div class="empty dz-button"></div>';
        } ?>
    </div>
    <span class="jorms-form-instructions">Upload PNG/JPG. Max file size 2MB.</span>
    <div class="error-message"></div>
</div>

