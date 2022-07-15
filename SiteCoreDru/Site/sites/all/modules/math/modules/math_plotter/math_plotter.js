$(document).ready(function() {

   if ($("#edit-function").size() > 0) {

      if ($.browser.msie && $.browser.version < 9) {
         $("#math-calc-form").html(Drupal.t('Your browser is not supported.'));
      }

      $("#edit-function").cmd({
         handler: function(formula) {$("canvas").plot({func: formula});},
         history: $("#cmd-history"),
         clear_history: $("#cmd-clear-history"),
         button: $("#edit-calculate"),
         add_history_ajax: "/math/plotter/add-history",
         clear_history_ajax: "/math/plotter/clear-history",
         saved_history: (window.cmd_history === undefined ? {} : cmd_history),
         saved_history_run: false
      });

      $("#math-calc-form").submit(function() {
         return false;
      });
   }

});
