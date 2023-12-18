jQuery(document).ready(function($){

    let jorms_forms_admin = $('#jorms_forms_admin');

    jorms_forms_admin.on( 'click', 'a.jorms_forms_admin-add', function( event ){
        event.preventDefault();jorms_form_add();});

    jorms_forms_admin.on( 'click', 'a.jorms_forms_admin-remove', function( event ){
        event.preventDefault();jorms_form_remove( $(this ) );});

    jorms_forms_admin.on( 'click', 'a.jorms_forms_admin-save', function( event ){
        event.preventDefault();jorms_form_save();});

    jorms_forms_admin.on('change','input[name="handle"]',function(event){
       let current_input = $(this);
       let all_inputs = jorms_forms_admin.find('input[name="handle"]');
       let duplicate_found = false;
       all_inputs.each( function(){
           if( this.val === current_input ){
               duplicate_found = true;
           }
       });
       if( duplicate_found )
           jorms_forms_admin.addClass('invalid');
       else
           jorms_form_admin.removeClass('invalid');
    });



    function jorms_form_add(){
        console.log('adding');
        jorms_forms_admin.append('<div class="jorms_form_admin-form"><div><label for="jorms_forms_admin-handle">Form handle</label><input type = "text" name="jorms_form-handle" value="" /></div><div><textarea name="jorms_form-json"></textarea></div><label for="jorms_forms_admin-json">Form JSON</label><a href="#" class="jorms_forms_admin-remove button">Remove</a></div>');
    }

    function jorms_form_remove(ele){
        ele.addClass('remove');
        ele.append('<a href="#" class="jorms_forms_admin-remove button">Undo</a>');
    }


    function jorms_form_save(){
        if( jorms_forms_admin.hasClass('invalid') ) return;

        $.ajax({
            type:	'POST',
            url: jorms_admin.ajaxurl,
            data: data,
            timeout: 10000
        })
            .always(function(response) {

            });
    }


});