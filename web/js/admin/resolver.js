(function ($) {

  var table = jQuery('.dm_data table');

  Array.prototype.getRandom = function(num, cut){
      var A = cut ? this : this.slice(0);
      A.sort(function(){
          return .5 - Math.random();
      });
      return A.splice(0, num);
  }
  Array.prototype.removeByValue = function(val) {
      for(var i=0; i<this.length; i++) {
          if(this[i] == val) {
              this.splice(i, 1);
              break;
          }
      }
  }
  
  if (table.metadata().step == 1) {

  }
  else if (table.metadata().step == 2)
  {
    var id,
      checkboxes = [],
      competitors = []
      counterWrapper = [],
      counter = [],
      overalCounter = 0;
    

    jQuery(".winner_counter").each(function () {
      $this = jQuery(this);
      id = $this.attr('data-prize-id');

      checkboxes[id] = jQuery('.winner[data-prize-id='+id+']');
      counterWrapper[id] = $this.find('span');
      counter[id] = parseInt(counterWrapper[id].text(), 10) - checkboxes[id].filter(":checked").length;
      overalCounter += counter[id];
      counterWrapper[id].text(counter[id]);

      // FIXME: not the best event but it catches changes. Twice...
      $this.bind('DOMSubtreeModified', function () {
        overalCounter = 0;
        for (var i = counter.length - 1; i >= 0; i--){
          if (typeof(counter[i]) !== 'undefined') {
            overalCounter += counter[i];
            // prevent chosing more winners than prizes
            // if we have run out of certain prize
            if (counter[i] === 0) {
              // disable choosing another winner for this prize
              jQuery('.winner[data-prize-id='+i+']:not(:checked)').attr('disabled', "disabled");
            } else {
              // reenable choosing winners for this prize
              jQuery('.winner[data-prize-id='+i+']:disabled').attr('disabled', false);
            }
          }
        };

        if (overalCounter === 0) {
          // enable submit
          jQuery('input[type=submit]').attr('disabled', false);
        } else {
          jQuery('input[type=submit]').attr('disabled', 'disabled');
        }
      });

      // store all responses' ids
      competitors[id] = [];
      checkboxes[id].each(function () {
        competitors[id].push($(this).attr('value'));
      });
      // random automatic picking
      $this.find(".pick_random").click(function () {
        id = $(this).parent().attr('data-prize-id');
        picked = competitors[id].getRandom(counter[id]);
        for (var i = picked.length - 1; i >= 0; i--){
          jQuery('input[value='+picked[i]+']').attr('checked', 'checked');
          counterWrapper[id].text(--counter[id]);
          competitors[id].removeByValue(picked[i]);
        };
      }); 
      // manual picking
      jQuery("input[data-prize-id="+id+"]").click(function (e) {
        id = $(this).attr('data-prize-id');
        // update counter
        if (jQuery(this).is(":checked")) {
          counterWrapper[id].text(--counter[id]);
          competitors[id].removeByValue($(this).attr('value'));
        } else {
          counterWrapper[id].text(++counter[id]);
          competitors[id].push($(this).attr('value'));
        }

        // also allow to follow on when all correct answers are chosen and prizes are left
        // TODO: make it work for all prizes. Now if any prize has less pozzible winners than there is prizes submit is being unlocked
        if (counter[id] !== 0 && (jQuery('.winner[data-prize-id='+id+']:checked').length === checkboxes[id].length))
          jQuery('input[type=submit]').attr('disabled', false);  
      }); 
    });
    // disable submission at first
    jQuery('input[type=submit]').attr('disabled', "disabled");
  }

  jQuery("a.ip").bind('click', function(e){
    e.preventDefault();
    $(this).colorbox({
      width: '80%', 
      height: '90%'
    });    
  });

  table.dataTable({
    "oLanguage": {
      "sUrl": table.metadata().translation_url
    },
    "bJQueryUI": true,
    "sPaginationType": "full_numbers",
    "aLengthMenu": [[10, 25, 100, -1], [10, 25, 100, "∞"]],
    "iDisplayLength": 25,
    "iDisplayStart": 0
  });
})(jQuery);