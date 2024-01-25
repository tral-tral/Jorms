<?php
if(!defined('ABSPATH')) exit; // Exit if accessed directly


$max = $field['max'] ?? false;
if( $value && !is_array( $value ) )
    $value = array( $value );
?>


<?php if ($label): ?>
    <div class="jorms-form-label"><?php echo $label ?></div>
<?php endif; ?>

<div class="jorms-form-video-container" id="<?php echo $field_id ?>" data-amount="<? echo $amount; ?>">
    <?php
    if ($value) {
        foreach ($value as $video_id) {
            // $src = wp_get_attachment_image_src( $img );
            echo '<div class="jorms-form-embedded-video" data-id="' . $video_id . '"><iframe class="youtube_iframe" src="https://youtube.com/embed/' . $video_id . '?enablejsapi=1" frameborder="0" allowfullscreen="true" allowscriptaccess="always"></iframe><a class="jorms-form-video-remove" href="#">Remove url</a></div>';

        }
    }

    echo '<div class="jorms-form-video-add-box">
<div class="jorms-form-video-add-box-input"><input type="url" /><a role="button" class="jorms-form-video-add-box-copyurl" href="#">
<svg width="36px" height="36px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#333333"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round"></g><g> <path d="M8 5.00005C7.01165 5.00082 6.49359 5.01338 6.09202 5.21799C5.71569 5.40973 5.40973 5.71569 5.21799 6.09202C5 6.51984 5 7.07989 5 8.2V17.8C5 18.9201 5 19.4802 5.21799 19.908C5.40973 20.2843 5.71569 20.5903 6.09202 20.782C6.51984 21 7.07989 21 8.2 21H15.8C16.9201 21 17.4802 21 17.908 20.782C18.2843 20.5903 18.5903 20.2843 18.782 19.908C19 19.4802 19 18.9201 19 17.8V8.2C19 7.07989 19 6.51984 18.782 6.09202C18.5903 5.71569 18.2843 5.40973 17.908 5.21799C17.5064 5.01338 16.9884 5.00082 16 5.00005M8 5.00005V7H16V5.00005M8 5.00005V4.70711C8 4.25435 8.17986 3.82014 8.5 3.5C8.82014 3.17986 9.25435 3 9.70711 3H14.2929C14.7456 3 15.1799 3.17986 15.5 3.5C15.8201 3.82014 16 4.25435 16 4.70711V5.00005M12 11H9M15 15H9" stroke="#333333" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"></path></g></svg>
</a></div>
<a href="#" role="button" icon="add" class="jorms-form-video-add"><i role="img" aria-hidden="true" class="jorms-form-video-add-icon"></i></a>
</div>';

    ?>

</div>

<span class="jorms-form-instructions"><?php _e('Enter a YouTube URL.','jorms') ?></span>
<span class="jorms-counter inline"><?php echo '<span class="jorms-current-count">' . ($value ? count($value) : 0) . '</span>/' . $amount; ?></span>
<div class="error-message"></div>


