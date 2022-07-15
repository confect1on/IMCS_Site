var calc_vars = {};

function calc(formula) {
   formula = $.trim(formula);
   if (formula === '') return undefined;
   var tt = typeof t == 'function' ? t : function(msg) {return msg;};

   var equals_sign = formula.indexOf('=');
   var var_name = '';
   if (equals_sign > 0) {
      var_name = $.trim(formula.substring(0, equals_sign));
      if (!var_name.match(/^[A-Za-z_]\w*$/)) {
         return tt('bad var name');
      }
      formula = $.trim(formula.substring(equals_sign + 1));
   }

   formula = formula.toLowerCase();
   formula = formula.replace(/\bpi\b/, "PI")
   formula = formula.replace(/\be\b/, "E");

   var result = false;
   try {
      var expression = Parser.parse(formula);
      result = expression.evaluate(calc_vars);
      if (var_name != '') {
         calc_vars[var_name] = result;
         result = tt('var assigned');
      }
   }
   catch(ignore) {
      result = tt('error');
   }
   return result;
}
