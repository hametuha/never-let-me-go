/*global NLMG:false*/

jQuery(document).ready(function ($) {

  "use strict";

  // Incremental search
  var $input = $('#nlmg_assign_to');
  $input.autocomplete({
    minLength: 1,
    source   : NLMG.endpoint,
    focus    : function (event, ui) {
      return false;
    },
    select   : function (event, ui) {
      jQuery(this).val(ui.item.ID);
      return false;
    }
  })
    .autocomplete("instance")._renderItem = function (ul, item) {
    return jQuery("<li>")
      .append("<div class='nlmg-inc-search'>" + item.avatar + "<span class=\"nlmg-inc-name\">" + item.display_name + "<small>(" + item.user_login + ")</small></span><div style=\"clear:left\"></div></div>")
      .appendTo(ul);
  };
});
