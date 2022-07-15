var cmd_pos = -1, cmd_saved = '';

/* cfg fields:
   handler — function, e.g. handler_demo (required)
   history — jquery object, e.g. $("#cmd-history") (required)
   clear_history — jquery object, e.g. $("#cmd-clear-history")
   button — jquery object, e.g. $("#cmd-button")
   add_history_ajax — url, e.g. "/add-history"
   clear_history_ajax — url, e.g. "/clear-history"
   saved_history — array of strings
   saved_history_run — boolean
 */
$.fn.cmd = function(cfg) {
   return this.each(function() {

      cfg.textfield = $(this);
      cfg.textfield.focus();

      // handle "enter"
      cfg.textfield.keypress(function(event) {
         if (event.which == 13 && $.trim(cfg.textfield.val()) != '') {
            cmd_handler(cfg);
         }
      });

      // optional button
      if (cfg.button !== undefined) {
         cfg.button.click(function() {
            if ($.trim(cfg.textfield.val()) != '') {
               cmd_handler(cfg);
               cfg.textfield.focus();
            }
         });
      }

      // handle up/down arrows
      cfg.textfield.keydown(function(event) {
         var history = $(".cmd-history-input", cfg.history);
         if (cmd_pos == -1) cmd_saved = cfg.textfield.val();
         var cmd_line;
         if (event.which == 38) { // up
            cmd_pos++;
            if (cmd_pos < history.length) {
               cmd_line = $(history[cmd_pos]).text();
            } else {
               cmd_pos = -1;
               cmd_line = cmd_saved;
            }
         }
         else if (event.which == 40) { // down
            cmd_pos--;
            if (cmd_pos == -2) cmd_pos = history.length - 1;
            if (cmd_pos >= 0) {
               cmd_line = $(history[cmd_pos]).text();
            } else {
               cmd_pos = -1;
               cmd_line = cmd_saved;
            }
         }
         else {
            cmd_pos = -1;
         }
         if (cmd_line !== undefined) cfg.textfield.val(cmd_line);
      });

      // clear history
      cfg.clear_history.click(function() {
         $(".cmd-history-item").remove();
         cfg.textfield.focus();
         if (cfg.clear_history_ajax) $.get(cfg.clear_history_ajax);
         return false;
      });

      // restore history
      if (cfg.saved_history !== undefined) {
         for(var i = 0; i < cfg.saved_history.length; i++) {
            cmd_handler(cfg, cfg.saved_history[i]);
         }
      }

   });
};

function cmd_handler(cfg, cmd) {
   var tt = typeof t == 'function' ? t : function(msg) {return msg;};
   var new_cmd = cmd === undefined;
   if (new_cmd) cmd = cfg.textfield.val();
   var output;
   if (cfg.handler !== undefined && (new_cmd || cfg.saved_history_run === undefined || cfg.saved_history_run)) {
      try {
         output = cfg.handler(cmd);
      }
      catch(e) {
         output = tt("error");
      }
   }
   var id = 'cmd-history-item-' + ($(".cmd-history-item").size() + 1);
   if (output !== undefined && output != '') {
      output_html = (typeof output == 'number' ? ' = ' : ' - ') + '<span class="cmd-history-output cmd-history-output-' + typeof output + '">' + cmd_htmlspecialchars(output) + '</span>';
   } else {
      output_html = '';
   }
   cfg.history.prepend("<div class='cmd-history-item' id='" + id + "'><span class='cmd-history-input'>" + cmd_htmlspecialchars(cmd) + "</span> " + output_html + "</div>");
   $("#" + id + " .cmd-history-input, #" + id + " .cmd-history-output").click(function() {
      cfg.textfield.val(cfg.textfield.val() + $(this).text()).focus();
   });
   cfg.textfield.val('');
   if (new_cmd && cfg.add_history_ajax) {
      $.get(cfg.add_history_ajax, {input: cmd, output: output});
   }
   return false;
}

function cmd_htmlspecialchars(text) {
   return text === undefined ? '' : text.toString().replace("&", "&amp;").replace("<", "&lt;").replace(">", "&gt;");
}
