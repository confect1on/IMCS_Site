$(document).ready(function() {

   if ($("#edit-expression").size() > 0) {

      $("#edit-expression").cmd({
         handler: function(expression) {
            $("#cmd-history").addClass('cmd-history-right');
            var src = "http://www.wolframalpha.com/input/?i=" + encodeURIComponent(expression);
            $(".math_wolfram_alpha_iframe").html("<iframe src='" + src + "' id='math_wolfram_alpha_iframe' width='700' height='500'></iframe>");
         },
         history: $("#cmd-history"),
         clear_history: $("#cmd-clear-history"),
         button: $("#edit-calculate"),
         add_history_ajax: "/math/wolfram-alpha/add-history",
         clear_history_ajax: "/math/wolfram-alpha/clear-history",
         saved_history: (window.cmd_history === undefined ? {} : cmd_history),
         saved_history_run: false
      });

      $("#math-calc-form").submit(function() {
         return false;
      });
   }

});
