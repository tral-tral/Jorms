class Jorms_Field {

    constructor( element, field_container ) {

        this.name      = element.data('name');
        this.element   = element;
        this.parent    = field_container;
        this.hidden    = false;
        this.required  = element.attr('required') !==undefined;
        this.counter =  this.element.find('>.jorms-counter');
        this.rules = {};
        this.init();
        this.validated = false;

        this.load_defaults();
      //  this.updated();

        if( this.required )
            this.rules = jQuery.extend( { required: this.required }, this.rules );
    }

    value() {throw new Error('value() method must be implemented in the subclass.')}

    valid() {
        const value = this.value();
        this.validated = true;
        if ( this.required || Jorms_Rules.methods.required( value ) ){
            for (const rule in this.rules) {
                const parameters = this.rules[rule];
                if (parameters === undefined) continue;
                const result = Jorms_Rules.methods[rule].call(null, value, parameters);
                if (!result) {
                    const message = Jorms_Rules.format(Jorms_Rules.messages[rule], parameters);
                    this.showError(message);
                    return false;
                }
            }
        }
        this.hideErrors();
        return true;
    }

    hide(){ this.hidden = true;this.element.hide()}
    show(){ this.hidden = false;this.element.show()}

    init(){}

    showError( message ){
        this.element.addClass('invalid');
        if( !this.error ){
            this.error = jQuery('<div></div>').addClass( Jorms_Rules.errorClass );
            this.element.append( this.error );
        }
        this.error.text( message );
    }

    load_defaults(){}

    hideErrors(){
        this.element.removeClass('invalid');
    }

    updated(){
        this.updateCounter();
        if( this.validated ) this.valid();
    }

    updateCounter(){
        if (this.counter.length) {
            let current_counter = this.counter.find('.jorms-current-count');
            if (current_counter.length) {
                let length = this.value().length;
                current_counter.text(length);
            }
        }
    }

}

class Jorms_RepeaterField extends Jorms_Field {

    init(){
        this.container = this.element.find('.jorms-form-repeater');
        const addButton = this.container.find('.jorms-form-repeater-add');
        const this_repeater = this;

        addButton.on('click', function(e){
            e.preventDefault();
            this_repeater.addFields();
        });

        this.template = this.container.find('.jorms-form-repeater-fields').html();

        this.inner_container = this.container.children('.jorms-form-repeater-inner').children('.jorms-form-repeater-inner-fields');

        this.min = ~~this.container.attr('data-min');
        this.max = ~~this.container.attr('data-max');

        this.field_containers = [];
        const inn_con = this.container;
       this.inner_container.sortable(
            {
                containment: inn_con,
                axis: 'y',
                opacity: 0.5,
                revert: 200,
                items: '.jorms-form-repeater-group',
                handle: '> a',
                forceHelperSize: false,
                help: 'clone',

            }
        );


     //   this.rules = { min: this.min, max: this.max };
    }

    load_defaults(){

        const default_containers = this.inner_container.find('> .jorms-form-repeater-group');
        const this_field = this;
        const num_groups = this.inner_container.children().length;

        default_containers.each(function () {
                const new_fields_container = jQuery(this).find('> .jorms-form-repeater-group-fields');
                const new_field_container  = new Jorms_form_field_container( new_fields_container, '.' + this_field.element[0].classList[0] + '-repeater');

                if( num_groups > this_field.min ){
                    const new_cancel_button = jQuery('<a href=# role="button" icon="cancel" class="jorms-form-repeater-remove"><i role="img" aria-hidden="true" class="jorms-form-repeater-remove-icon"></i></a>');
                    new_cancel_button.on('click', function(e){e.preventDefault();this_field.removeField( new_field_container );});
                    jQuery(this).append( new_cancel_button );
                }


                this_field.field_containers.push(new_field_container);
            }
        );

        this.updated();
    }

    value() {
        let values = [];
        for( const container of this.field_containers ){
            values.push( container.value() );
        }
        return values;
    }

    valid() {
        let result = super.valid();

        for (const container of this.field_containers){
            result = container.valid() && result;
        }
        return result;
    }


    addFields(){
        if( this.max && this.field_containers.length >= this.max ) return;
        const new_fields = Jorms_Rules.format( this.template, Date.now() );

        //   new_fields.attr('class', 'jorms-form-repeater-group-fields');

        const new_group = jQuery('<div class="jorms-form-repeater-group"></div>');
        const new_fields_container = jQuery('<div class="jorms-form-repeater-group-fields"></div>');
        new_fields_container.append( new_fields );

        if( jQuery(new_fields).length > 1 )
            new_group.addClass('grouped');

        new_group.append( new_fields_container );

        this.inner_container.append( new_group );
        const num_groups = this.inner_container.children().length;
        const this_object = this;

        if( num_groups > this.min ){
            const new_cancel_button = jQuery('<a href=# role="button" icon="cancel" class="jorms-form-repeater-remove"><i role="img" aria-hidden="true" class="jorms-form-repeater-remove-icon"></i></a>');
            new_cancel_button.on('click', function(e){e.preventDefault();this_object.removeField( new_field_container );});
            new_group.append( new_cancel_button );
        }

        const new_field_container = new Jorms_form_field_container( new_fields_container, '.' + this.element[0].classList[0] + '-repeater' );
        this.field_containers.push( new_field_container );

        this.updated();

    }

    removeField( field_container ){

        const index = this.field_containers.indexOf( field_container );
        if (index !== -1) {
            this.field_containers.splice(index, 1);
            jQuery(field_container.container).parent().remove();

            this.updated();
        }


    }
    updated(){
        if( this.max && this.field_containers.length >= this.max )
            this.element.addClass('jorms-full');
        else
            this.element.removeClass('jorms-full');
        super.updated();
    }

}


class Jorms_SimpleField extends Jorms_Field {
    init() {
        this.input = this.element.find('input');
        const this_field = this;

        this.input.on('keyup', function(event){
            var excludedKeys = [
                16, 17, 18, 20, 35, 36, 37,
                38, 39, 40, 45, 144, 225
            ];
            if ( event.which === 9 && this_field.input.val()=== "" || jQuery.inArray( event.keyCode, excludedKeys ) !== -1 )
                return;
            this_field.updated();
        });

        this.input.on('focusout', function(){this_field.updated() });

    }

    value(){return this.input.val();}


}

class Jorms_TextField extends Jorms_SimpleField{

    init(){
        super.init();
        this.minlength = this.input.attr('minlength');
        this.maxlength = this.input.attr('maxlength');
        this.rules = { minlength: this.minlength, maxlength: this.maxlength };
    }

}


class Jorms_NumberField extends Jorms_SimpleField{

    value() {
        const value = super.value();
        if( value === '' ) return value;
        return Number( super.value() );
    }

    init(){
        super.init();
        const this_field = this;
        this.input.on('keyup', function(event){  if(  event.keyCode === 38 || event.keyCode === 40) this_field.updated(); });
        this.min = this.input.attr('min');
        this.max = this.input.attr('max');
        this.step = this.input.attr('step');
        this.rules = { min: this.min, max: this.max, number: true, step: this.step };
    }
}


class Jorms_EmailField extends Jorms_SimpleField{
    init(){
        super.init();
        this.rules = { email: true };
    }
}


class Jorms_DateField extends Jorms_SimpleField{
    init(){
        super.init();
        this.min = this.input.attr('min');
        this.max = this.input.attr('max');

        this.rules = {  datetimemin: this.min, datetimemax: this.max, date: true };
    }
}

class Jorms_TimeField extends Jorms_SimpleField{
    init(){
        super.init();
        this.min = this.input.attr('min');
        this.max = this.input.attr('max');
        this.rules = {  datetimemin: this.min, datetimemax: this.max, time: true };
    }
}



class Jorms_CheckboxField extends Jorms_Field{

    init(){
        this.min = this.element.data('min');
        this.max = this.element.data('max');
        this.inputs = this.element.find('input[type="checkbox"]');
        const this_field = this;
     //   this.rules = { maxlength: this.max, minlength: this.min };
        this.inputs.on('change', function (){
            this_field.updated();
        });
    }

    value(){
        let values = [];
        const checked_inputs = this.inputs.filter(':checked')
        checked_inputs.each(function(){
            values.push( jQuery(this).val() );
        });
        return values;
    }

}

class Jorms_RadioField extends Jorms_Field{
    init(){
        this.inputs = this.element.find('input[type="radio"]');
        const this_field = this;
        this.inputs.on('change', function (){
            this_field.updated();
        });
    }

    value(){
        const checked_input = this.inputs.filter(':checked');
        return checked_input.val();
    }

}



class Jorms_TextareaField extends Jorms_Field{
    init(){
        this.input = this.element.find('textarea');
        this.minlength = this.input.attr('minlength');
        this.maxlength = this.input.attr('maxlength');

        this.rules = { minlength: this.minlength, maxlength: this.maxlength, required: true };
        const this_field = this;
        this.input.on('keyup', function(event){
            var excludedKeys = [
                16, 17, 18, 20, 35, 36, 37,
                38, 39, 40, 45, 144, 225
            ];
            if ( event.which === 9 && this_field.input.val()=== "" || jQuery.inArray( event.keyCode, excludedKeys ) !== -1 )
                return;
            this_field.updated();
        });
        this.input.on('focusout', function(){this_field.updated();});


    }
    value(){
        return this.input.val();
    }
}


class Jorms_ImageField extends Jorms_Field{
    init(){
        this.upload_container = this.element.find('.jorms-form-upload-image-container');
        const image_field = this;
        const buttons  = this.upload_container.find('.dz-button').get();
        const nonce    = this.upload_container.data('nonce');
        this.amount    = this.upload_container.data('amount');
        const field_id = this.upload_container.attr('id');
        const upload_container = this.upload_container;
        const parent           = this.upload_container.parent();

        if( this.amount > 1 ) {
            upload_container.sortable(
                {
                    containment: parent,
                    opacity: 0.5,
                    revert: 200,
                    items: '.dz-preview',
                    handle: '> div',
                    forceHelperSize: false,
                    helper: 'clone',
                }
            );
        }


        this.rules = { count: this.amount }
        this.dropzone_object = new Dropzone( this.upload_container[0], {
            maxFilesize: 2, // MB
            maxFiles: this.amount,
            url: jorms_vars.ajaxurl + '?action=jorms_handle_dropped_media&nonce=' + nonce + "&field_id=" + field_id,

            acceptFiles: 'image/jpeg,image/png,image/jpg',
            acceptedMimeTypes: 'image/jpeg,image/png,image/jpg',
            clickable: buttons,
            dictDefaultMessage: "",
            success: function (file, response) {
                file.previewElement.classList.add("dz-success");
                file['attachment_id'] = response; // push the id for future reference
                jQuery(file.previewElement).attr("data-attachment_id", file.attachment_id);
                image_field.updated();
            },
            error: function (file, response) {
                file.previewElement.classList.add("dz-error");
            },

            init: function() {
                this.on("processing", function(file) {

                    const time = 'file-' + Date.now();
                    jQuery(file.previewElement).attr("data-time_id", time );
                    file['time_id'] = time; // push the id for future reference
                    if (this.files.length >= this.options.maxFiles){
                        upload_container.siblings('.jorms-form-upload-button').prop('disabled', true );
                    }
                    image_field.update_empty_blocks();
                });

                this.on("removedfile", function(file) {
                    if (this.files.length < this.options.maxFiles){
                        upload_container.siblings('.jorms-form-upload-button').prop('disabled', false );
                    }
                    image_field.update_empty_blocks();
                    image_field.updated();
                });


                this.on("error", function(file, errorMessage) {
                    this.removeFile(file);
                    parent.addClass('error');
                    upload_container.siblings('.error-message').text( errorMessage );
                    setTimeout(function(){
                        parent.removeClass('error');
                    },3600);
                });
            },
            addRemoveLinks: true,
        });
    }

    load_defaults(){

        const defaults = this.upload_container.data('default');

        if( defaults === 'false' ) return;

        const this_field = this;

        for( let key in defaults ) {
            const image = defaults[key];
            const mockFile = { name: image.name, size: image.size, type: image.type };
            this_field.dropzone_object.emit("addedfile", mockFile );

            this_field.dropzone_object.emit("thumbnail", mockFile, image.url );
            this_field.dropzone_object.emit("complete", mockFile );

            const time = 'file-' + Date.now();

            jQuery( mockFile.previewElement).attr("data-time_id", time );
            jQuery( mockFile.previewElement ).attr("data-attachment_id", image.attachment_id);
            jQuery( mockFile.previewElement ).addClass('dz-success');

        }

        this.update_empty_blocks();
        this.updated();

    }

    update_empty_blocks(){
     //   this.updated();
        const empty_containers = this.upload_container.find('.empty');
        this.upload_container.append( empty_containers );
        let count = this.getCount();
        empty_containers.each( function() {
            const container = jQuery(this);
            if( count-- > 0 )
                container.removeClass('hidden');
            else container.addClass('hidden');
        })
    }

    getCount(){

        return this.amount - this.upload_container.find('.dz-preview').length;
    }

    value(){
        let filtered_values;
        if( this.amount > 1 ) {
            filtered_values = [];
            const values = this.upload_container.sortable("toArray", {attribute: "data-attachment_id"})
            filtered_values = values.filter(function (v) {
                return v !== ''
            });
        } else filtered_values = this.upload_container.find('.dz-success').attr('data-attachment_id') || '';
        return filtered_values;
    }
}

class Jorms_SelectField extends Jorms_Field{

    init(){
        this.input = this.element.find('select');
        const this_field = this;
        this.input.on('change', function (){
            this_field.updated();
        });


    }
    value(){return this.input.val() }
}

class Jorms_SwitchField extends Jorms_Field{
    init(){
        this.input = this.element.find('input[type="radio"');
        const this_field = this;
        this.input.on('change', function (){
            this_field.updated();
        });
    }
    value(){ return this.input.val() }
}


class Jorms_Select2Field extends Jorms_SelectField{
    init(){
        super.init();
       // console.log(this.input); // should log a jQuery object representing your select element
       // console.log( this.parent.container.parent() );
        this.input.select2(
            {  dropdownParent: this.parent.container.parent() ,}
        );
    }
}

class Jorms_VideoField extends Jorms_Field{
    init(){
      //  this.video_wrapper   = this.element.find('.jorms-form-video-wrapper');
        this.video_container = this.element.find('.jorms-form-video-container');
        this.amount          = this.video_container.data('amount');
        this.inputBox        = this.video_container.find('input');
        this.addButton       = this.video_container.find('.jorms-form-video-add');
        this.copyButton      = this.video_container.find('.jorms-form-video-add-box-copyurl');

        this.videos          = [];

        const video_field = this;


        this.inputBox.on('keydown', function(event) {
            if (event.key === 'Enter') {
                video_field.addButton.trigger('click');
            }
        });

        this.addButton.on('click', function(event ){

            event.preventDefault();
            let url = video_field.inputBox.val();
            const regex = /^(?:https?:\/\/)?(?:www\.)?(?:m\.)?(?:youtube\.com|youtu\.be)\/(?:watch\?v=|embed\/|v\/|\.be\/|\/watch\?v%3D|watch\?feature=player_embedded&v=|embed%\?v=|embed\?feature=player_embedded&v=)?([a-zA-Z0-9\-_]+)/;

            if( url==='' ){ video_field.inputBox.focus();return;}
            const match = url.match(regex);

            if (match) {
                const videoId = match[1];
                video_field.inputBox.val('');
                if( !video_field.videos.includes( videoId ) )
                    video_field.add_video( videoId );
                else video_field.set_error("Duplicate URLs not allowed");

            } else {
                video_field.set_error("Invalid YouTube video URL");
            }


        });

        this.copyButton.on('click', async function(event){
            event.preventDefault();
            try {
                const text = await navigator.clipboard.readText();
                video_field.inputBox.val( text );
            } catch (err) {
                console.error('Failed to read clipboard:', err);
            }
        });

    }

    load_defaults(){
        const videos_list =this.video_container.find('.jorms-form-embedded-video');
        const this_field = this;
        videos_list.each(
            function(){
                this_field.videos.push(this.data('id'));
            }
        );
    }

    set_error( errorMessage ){
        const error = this.element.find('.error-message');
        this.inputBox.focus();
        error.text( errorMessage );
        error.parent().addClass('error');
        setTimeout(function(){
            error.parent().removeClass('error');
        },3600);
    }

    add_video( video_id ){

        if( this.videos.length >= this.amount ) return;

        const video_field = this;
        const new_video = jQuery(
            '<div class="jorms-form-embedded-video" data-id="'+video_id+'"><iframe class="youtube_iframe" src="https://youtube.com/embed/' + video_id + '?enablejsapi=1" frameborder="0" allowfullscreen="true" allowscriptaccess="always"></iframe><a class="jorms-form-video-remove" href="#">Remove url</a></div>');
        this.video_container.prepend( new_video );


        new_video.find('a').on('click',function(event){
            event.preventDefault();
            const video = jQuery(this).closest('.jorms-form-embedded-video');
            video_field.remove_video( video );
        });

        this.videos.push( video_id );
        this.updated();

    }

    remove_video( ele ){
        const id = ele.data('id');
        this.videos = this.videos.filter((value) => value !== id);
        ele.remove();
        this.updated();
    }

    updated(){
        if( this.videos.length >= this.amount )
            this.element.addClass('jorms-full');
        else
            this.element.removeClass('jorms-full');
         super.updated();
    }

    value(){
        return this.videos;
    }


}


class Jorms_PlaceField extends Jorms_Field{
    init(){
        this.placeField = this.element.find('.jorms-form-place');
        this.searchInput = this.element.find('input');
        this.resultsList = this.element.find('.results-list');
        const this_field = this;
        const searchInput = this.searchInput[0];
        this.img = this.element.find('img');
        this.placename = this.element.find('.jorms-form-place-name');
        this.searchInput.on('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
            }
        });

        const autocomplete = new google.maps.places.Autocomplete( searchInput, {
            language: 'ja',
            types: ['bar','night_club'],
            fields: ['name','photos','place_id'],
            bounds: {
                east:130.4949485,
                west:130.0329944,
                north:33.8749852,
                south:33.4249613,
            },
            strictBounds: true,

        });

        // Listen for place selection event
        autocomplete.addListener('place_changed', function() {
            const selectedPlace = autocomplete.getPlace();
          //  console.log( selectedPlace );
            if (selectedPlace) {
                this_field.placeField.removeClass('noplace');
                this_field.place = selectedPlace;
                this_field.placename.text( selectedPlace.name );
                if ( selectedPlace.photos && selectedPlace.photos.length > 0) {
                    const photoReference = selectedPlace.photos[0].getUrl();
                    this_field.img.attr('src', photoReference);
                } else this_field.img.attr('src','');
            } else {
                this_field.placeField.addClass('noplace');
                this_field.place = '';
            }
            this_field.updated();
        });


    }
    value(){
        if( !this.place ) return '';
        return this.place.place_id;
    }
}






class Jorms_FieldFactory {
    static fieldTypes = {};
    static registerFieldClass(fieldType, fieldClass) {Jorms_FieldFactory.fieldTypes[ fieldType.toLowerCase() ] = fieldClass;}
    static createField( element, field_container ) {
        element = jQuery( element );
        const fieldType = element.data('type');
        const FieldClass = Jorms_FieldFactory.fieldTypes[ fieldType ];
        if (FieldClass) { return new FieldClass( element, field_container ); }
        throw new Error('Unsupported field type.');
    }
}


class Jorms_Rules{
    static errorClass = 'jorms-form-field-error';
    static messages = {
        required: "This field is required.",
        remote: "Please fix this field.",
        email: "Please enter a valid email address.",
        url: "Please enter a valid URL.",
        date: "Please enter a valid date.",
        time: "Please enter a valid time.",
        number: "Please enter a valid number.",
        digits: "Please enter only digits.",
        equalTo: "Please enter the same value again.",
        maxlength: "Please enter no more than {0} characters.",
        minlength: "Please enter at least {0} characters.",
        rangelength: "Please enter a value between {0} and {1} characters long.",
        range: "Please enter a value between {0} and {1}.",
        max: "Please enter a value less than or equal to {0}.",
        min: "Please enter a value greater than or equal to {0}.",
        datetimemax: "Please enter a date/time less than or equal to {0}.",
        datetimemin: "Please enter a date/time greater than or equal to {0}.",
        step: "Please enter a multiple of {0}.",
        count: "Please fill all slots ({0}).",
    };



    static methods = {


        required: function( value ) {
            if( typeof value === 'object' && !Array.isArray(value) && value !== null )
                return Object.keys(value).length > 0;
            else return value !== undefined && value !== null && String(value).trim().length > 0;
        },

        email: function( email ) {


            if (email.length < 6) {
                return false;
            }

            if (email.indexOf('@') < 1) {
                return false;
            }

            const [local, domain] = email.split('@');

            const localPattern = new RegExp('^[a-zA-Z0-9!#$%&\'*+/=?^_`{|}~.-]+$');
            if (!localPattern.test(local)) {
                return false;
            }

            const domainPattern = new RegExp('\\.{2,}');
            if (domainPattern.test(domain)) {
                return false;
            }

            if (domain.trim(" .") !== domain) {
                return false;
            }

            const subs = domain.split('.');

            if (subs.length < 2) {
                return false;
            }

            for (let i = 0; i < subs.length; i++) {
                if (subs[i].trim(" -") !== subs[i]) {
                    return false;
                }

                const subPattern = new RegExp('^[a-z0-9-]+$', 'i');
                if (!subPattern.test(subs[i])) {
                    return false;
                }
            }
            return true;
        },

        url: function( value  ) {
            return /^(?:(?:(?:https?|ftp):)?\/\/)(?:(?:[^\]\[?\/<~#`!@$^&*()+=}|:";',>{ ]|%[0-9A-Fa-f]{2})+(?::(?:[^\]\[?\/<~#`!@$^&*()+=}|:";',>{ ]|%[0-9A-Fa-f]{2})*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z0-9\u00a1-\uffff][a-z0-9\u00a1-\uffff_-]{0,62})?[a-z0-9\u00a1-\uffff]\.)+(?:[a-z\u00a1-\uffff]{2,}\.?))(?::\d{2,5})?(?:[/?#]\S*)?$/i.test( value );
        },

        date: function( value ) {
         return /^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$/.test( value );
        },

        time: function( value ){
            return /^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/.test( value );
        },

        number: function( value ) {
          return /^(?:-?\d+|-?\d{1,3}(?:,\d{3})+)?(?:\.\d+)?$/.test( value );
        },

        digits: function( value ) {
           return /^\d+$/.test( value );
        },

        minlength: function( value, param ) {
            return value.length >= param;
        },

        maxlength: function( value, param ) {
            return value.length <= param;
        },

        rangelength: function( value, param ) {
            return  ( value.length >= param[ 0 ] && value.length <= param[ 1 ] );
        },

        min: function( value, param ) {
           return value >= param;
        },

        max: function( value, param ) {
           return value <= param;
        },

        datetimemin: function( value, param ) {
            return (new Date(value) ) >= ( new Date(param) );
        },

        datetimemax: function( value, param ) {
            return (new Date(value) ) <= ( new Date(param) );
        },



        range: function( value, param ) {
            return ( value >= param[ 0 ] && value <= param[ 1 ] );
        },

        count: function(value, param){
            if( !Array.isArray( value ) ) value = [ value ];
            return value.length === param;
        },


        step: function (value, param) {
            return (value % param === 0 || Math.abs(value % param) < 1e-9);
        },

    }

    static addMethod( name, method, message ) {
        this.methods[ name ] = method;
        this.messages[ name ] = message !== undefined ? message : this.messages[ name ];
    }

    static format( source, params ) {
        if ( arguments.length === 1 ) {
            return function() {
                var args = jQuery.makeArray( arguments );
                args.unshift( source );
                return Jorms_Rules.format.apply( this, args );
            };
        }
        if ( params === undefined ) {
            return source;
        }
        if ( arguments.length > 2 && params.constructor !== Array  ) {
            params = jQuery.makeArray( arguments ).slice( 1 );
        }
        if ( params.constructor !== Array ) {
            params = [ params ];
        }
        jQuery.each( params, function( i, n ) {
            source = source.replace( new RegExp( "\\{" + i + "\\}", "g" ), function() {
                return n;
            } );
        } );
        return source;
    };

}




class Jorms_form_field_container{

    constructor( container, selector ) {

        this.selector   = selector || '.jorms-form-field';
        this.container  = container;
        this.conditions = container.attr('conditions');
        this.fields = [];

        this.elements = container.find( selector );
        for ( const element of this.elements ) {
            this.fields.push( Jorms_FieldFactory.createField( element, this ) );
        }

        this.checkConditions();
    }

    checkConditions(){}

    value(){
        let values = {};
        if( this.fields.length === 1 ) return this.fields[0].value();
        for( const field of this.fields ) {
            if (field.hidden) return;
            let value = field.value();
            const name = field.name;
            if( value.length === 0 )
                value = '';
            values[name] = value;
        }

        return values;
    }

    valid(){
        let valid = true;
        for( const field of this.fields ){
            valid = field.valid() && valid;
        };

        return valid;
    }

}


class Jorms_form{

    constructor( container, config ) {

        this.container = container;
        if( config == undefined ) config = {};
        this.selector         = config.selector || '.jorms-form-field';

        const submitSelector  = config.submitSelector || '[type="submit"]';
        const messageSelector = config.messageSelector || '.jorms-form-message';

        this.submitElement    = container.find( submitSelector );
        this.messagecontainer = this.container.find( messageSelector );

        this.handle = this.container.data('jorms-form-handle');
        this.nonce  = this.container.data('jorms-form-nonce');
        this.protected_id = this.container.data('jorms-form-nonce-suffix');
        this.action = this.container.data('jorms-form-action');

        this.otherData = this.container.data('jorms');

        if (!this.container) throw new Error('You must pass an element to be the container of the form.');

        this.field_container = new Jorms_form_field_container( this.container, this.selector );
        const this_form = this;

        this.submitElement.on('click', function(e){
            e.preventDefault();
            this_form.submit();
        } );

    }



    valid(){
        return this.field_container.valid();
    }

    value(){
        return this.field_container.value();
    }

    submit(){

        if( !this.valid() )
            return;

      //  const values = this.getValues();
        this.ajax();

    }


    ajax(){
        if( this.loading ) return;
        this.loading = true;
        this.container.addClass('loading');

        const values = this.value();


        const data = {
            input: values,
            handle: this.handle,
            action: 'jorms_form',
            nonce: this.nonce,
            protected_id: this.protected_id,
        };


        this.messagecontainer.html('<i class="jorms-loading fas fa-circle-notch fa-spin"></i>');
        const action = this.action;
        const messagecontainer = this.messagecontainer;


        const this_form = this;

        const otherData = this.otherData;
        const container = this.container;


     //  console.log( values );


        jQuery.ajax({
            type:   'POST',
            url: jorms_vars.ajaxurl,
            data: data,
            timeout: 10000
        })
            .done(function(response) {
                messagecontainer.addClass('success');
                messagecontainer.html( response.data );
                setTimeout(function(){
                    if(  action == 'reload' ) location.reload();
                    else if( action == 'redirect' ) window.location.href = otherData;
                },800);
            })
            .fail(function(response){
                console.log( response );
                messagecontainer.addClass('fail');
                if( response.responseJSON )
                    messagecontainer.text( response.responseJSON.data	);
                else messagecontainer.text('Something went wrong.  Please try again.'	);
                setTimeout(function(){
                    messagecontainer.text(''); messagecontainer.removeClass('success');messagecontainer.removeClass('fail');container.removeClass('loading');
                    this_form.loading = false;
                },2000);
            })
    }

}


jQuery.fn.jorms        = function ( selector ) { return this.each(function () {return new Jorms_form( jQuery(this), selector ); }); };
jQuery.fn.jorms_fields = function ( selector ) { return this.each(function () {return new Jorms_form_field_container( jQuery(this), selector );});};

jQuery(document).ready(function($){
    Jorms_FieldFactory.registerFieldClass('place', Jorms_PlaceField);
    Jorms_FieldFactory.registerFieldClass('checkbox', Jorms_CheckboxField);
    Jorms_FieldFactory.registerFieldClass('date', Jorms_DateField);
    Jorms_FieldFactory.registerFieldClass('video', Jorms_VideoField);
    Jorms_FieldFactory.registerFieldClass('time', Jorms_TimeField);
    Jorms_FieldFactory.registerFieldClass('email', Jorms_EmailField);
    Jorms_FieldFactory.registerFieldClass('text', Jorms_TextField);
    Jorms_FieldFactory.registerFieldClass('password', Jorms_TextField);
    Jorms_FieldFactory.registerFieldClass('tel', Jorms_TextField);
    Jorms_FieldFactory.registerFieldClass('url', Jorms_TextField);
    Jorms_FieldFactory.registerFieldClass('number', Jorms_NumberField);
    Jorms_FieldFactory.registerFieldClass('textarea', Jorms_TextareaField);
    Jorms_FieldFactory.registerFieldClass('image', Jorms_ImageField);
    Jorms_FieldFactory.registerFieldClass('radio', Jorms_RadioField);
    Jorms_FieldFactory.registerFieldClass('repeater', Jorms_RepeaterField);
    Jorms_FieldFactory.registerFieldClass('select', Jorms_SelectField);
    Jorms_FieldFactory.registerFieldClass('select2', Jorms_Select2Field);
    Jorms_FieldFactory.registerFieldClass('switch', Jorms_SwitchField);

    const forms = $('form.jorms-form');
    forms.jorms();

});



