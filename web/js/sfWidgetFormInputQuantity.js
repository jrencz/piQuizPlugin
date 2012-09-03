(function($) {
  
  

    plus  = jQuery(".quantity_increase").click(function (e) {
      e.stopPropagation();
      e.preventDefault();
      value = parseInt(jQuery(this).siblings('.sf_widget_form_input_quantity').attr('value'));
      jQuery(this).siblings('input').attr('value', ++value);  
    }); 
    
    minus = jQuery(".quantity_decrease").click(function (e) {
      e.stopPropagation();
      e.preventDefault();
      value = parseInt(jQuery(this).siblings('.sf_widget_form_input_quantity').attr('value'));
      if (value>0) {
        jQuery(this).siblings('input').attr('value', --value);
      }
    });
    

})(jQuery);