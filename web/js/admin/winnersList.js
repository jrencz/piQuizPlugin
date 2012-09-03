(function ($) {

  var table = jQuery('.dm_data table');

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