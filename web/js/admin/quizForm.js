(function($) {
  
  function shuffleFields (type) {
    switch (type) {
      case "OPEN": 
        jQuery('#sf_fieldset_predefined_answers').hide();
        jQuery('#sf_fieldset_answers').show();
        break;
      case "PREDEFINED":
        jQuery('#sf_fieldset_predefined_answers').show();
        jQuery('#sf_fieldset_answers').hide();
        break;
    }
  }
  
  var type = $('#pi_quiz_admin_form_type');
  shuffleFields(type.attr('value'));
  type.change(function () {
    shuffleFields(type.attr('value'));
  });
  
  

})(jQuery);