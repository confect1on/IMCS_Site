$(document).ready(function() {

   if ($("#edit-formula").size() > 0) {

      $("#edit-formula").cmd({
         handler: calc,
         history: $("#cmd-history"),
         clear_history: $("#cmd-clear-history"),
         button: $("#edit-calculate"),
         add_history_ajax: "/math/calc/add-history",
         clear_history_ajax: "/math/calc/clear-history",
         saved_history: (window.cmd_history === undefined ? {} : cmd_history),
         saved_history_run: true
      });

      $("#math-calc-form").submit(function() {
         return false;
      });
   }

});
