(function($) {
 
  var widget = jQuery('#dm_page div.dm_widget.pi_quiz_plugin_take_part'),
    submit = widget.find("input[type='submit']"),
    name,
    surname,
    answer,
    prize,
    email,
    quiz;

  widget.live('dmWidgetLaunch', function()
  {  
    submit.live('click', function(e) {
      e.preventDefault();

      jQuery.ajax({
        url: widget.find('form').attr('action'),
        data: {               
          name:                 widget.find('#pi_quiz_response_front_form_name').attr('value'),
          surname:              widget.find('#pi_quiz_response_front_form_surname').attr('value'),
          open_answer:          widget.find('#pi_quiz_response_front_form_open_answer').attr('value'),
          predefined_answer_id: widget.find('.radio_list :checked').attr('value'),
          prize_id:             widget.find('#pi_quiz_response_front_form_prize_id').attr('value'),
          email:                widget.find('#pi_quiz_response_front_form_email').attr('value'),
          quiz_id:              widget.find('#pi_quiz_response_front_form_quiz_id').attr('value')
        },
        success: function(response) {
          widget.find('form').replaceWith(response.data.html);  
        }
      });
    });
  });

})(jQuery);