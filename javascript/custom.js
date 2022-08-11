jQuery(document).on('click', 'button#register', function(){
    //alert(jQuery("#register-form").serialize());
    jQuery.ajax({
        type:'POST',
        url:baseurl+'Auth/register',
        data:jQuery("#registerForm").serialize(),
        dataType:'json',    
        beforeSend: function () {
            jQuery('button#register').button('loading');
        },
        complete: function () {
            jQuery('button#register').button('reset');
            jQuery('#registerForm').find('input-field, input').each(function () {
                jQuery(this).val('');
            });
        },                
        success: function (json) {
            //console.log(json);
           $('.text-danger').remove();
            if (json['error']) {
             
                for (i in json['error']) {

                  var element = $('.input-contact-' + i.replace('_', '-'));
                  if ($(element).parent().hasClass('input-field')) {
                       
                    $(element).parent().after('<div class="text-danger" style="font-size: 14px;">' + json['error'][i] + '</div>');
                  } else {
                    $(element).after('<div class="text-danger" style="font-size: 14px;">' + json['error'][i] + '</div>');
                  }
                }
            } else {
                jQuery('span#success-msg').html('<div class="alert alert-success">Your form has been successfully submitted.</div>');
            }                       
        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }        
    });
});