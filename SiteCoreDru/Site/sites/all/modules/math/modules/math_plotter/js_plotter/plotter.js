/* cfg fields:
   func — function as string, e.g. x*sin(x) (required)
   x1,y1,x2,y2 - jquery text fields with ranges (by default: -30, -30, 30, 30)
*/
$.fn.plot = function(cfg, redraw) {
   return this.each(function() {
      if (!redraw) {
         plot_x1 = -30; plot_y1 = -30;
         plot_x2 = 30; plot_y2 = 30;

         // move center by click
         $(this).unbind('click').click(function(event) {
            var offset = $(this).offset();
            var x = event.pageX - offset.left, y = event.pageY - offset.top;
            x = x - this.width / 2;
            y = this.height - y - this.height / 2;
            var dx = x * (plot_x2 - plot_x1) / this.width;
            var dy = y * (plot_y2 - plot_y1) / this.height;
            plot_x1 += dx; plot_x2 += dx
            plot_y1 += dy; plot_y2 += dy;
            $(this).plot(cfg, true);
         });
         // add plus/minus for scale
         $("body").append('<div class="plot-controls"><a class="plot-plus" href="#">+</a> <a class="plot-minus" href="#">&minus;</a></div>');
         var offset = $(this).offset();
         offset.left += 10; offset.top += 10;
         $(".plot-controls").css('left', offset.left).css('top', offset.top);
         plot_canvas = this;
         $(".plot-minus").unbind('click').click(function() {
            var k = 1.25;
            plot_x1 *= k; plot_x2 *= k;
            plot_y1 *= k; plot_y2 *= k;
            $(plot_canvas).plot(cfg,true);
            return false;
         });
         $(".plot-plus").unbind('click').click(function() {
            var k = 1.25;
            plot_x1 /= k; plot_x2 /= k;
            plot_y1 /= k; plot_y2 /= k;
            $(plot_canvas).plot(cfg,true);
            return false;
         });
      }

      // init vars
      var ctx = this.getContext('2d');
      ctx.clearRect(0, 0, this.width, this.height);
      var func = Parser.parse(cfg.func);
      var x1 = plot_x1, x2 = plot_x2, y1 = plot_y1, y2 = plot_y2;
      if (cfg.x1 != undefined) {
         x1 = parseInt(cfg.x1.val());
         x2 = parseInt(cfg.x2.val());
         y1 = parseInt(cfg.y1.val());
         y2 = parseInt(cfg.y2.val());
      }
      var dx = (x2 - x1) / this.width
      var a = this.width / (x2 - x1), e = x1 * this.width / (x1 - x2);
      var d = this.height / (y1 - y2), f = y2 * this.height / (y2 - y1);
      var i, x, y;

      // draw grid
      ctx.strokeStyle = '#ddd';
      ctx.lineWidth   = 2;
      ctx.beginPath();
      var nx = 10, ny = 10;
      for(i = 0; i <= nx; i++) {
         x = x1 + i * (x2 - x1) / nx;
         ctx.moveTo(x * a + e, y1 * d + f);
         ctx.lineTo(x * a + e, y2 * d + f);
      }
      for(i = 0; i <= ny; i++) {
         y = y1 + i * (y2 - y1) / ny;
         ctx.moveTo(x1 * a + e, y * d + f);
         ctx.lineTo(x2 * a + e, y * d + f);
      }
      ctx.stroke();
      // draw axis
      ctx.beginPath();
      ctx.strokeStyle = '#999';
      if (x1 <= 0 && x2 >=0) {
         ctx.moveTo(e, y1 * d + f);
         ctx.lineTo(e, y2 * d + f);
      }
      if (y1 <= 0 && y2 >=0) {
         ctx.moveTo(x1 * a + e, f);
         ctx.lineTo(x2 * a + e, f);
      }
      ctx.stroke();

      // plot
      ctx.strokeStyle = 'blue';
      ctx.lineWidth   = 1;
      ctx.beginPath();
      var x0 = x1, y0 = func.evaluate({x: x1});
      for(i = 1; i <= this.width; i++) {
         x = x1 + i * dx;
         y = func.evaluate({x: x});
         if (y == y && y0 == y0 && y != Infinity && y != -Infinity && y0 != Infinity && y0 != -Infinity) {
            ctx.moveTo(x0 * a + e, y0 * d + f);
            ctx.lineTo(x  * a + e, y  * d + f);
         }
         x0 = x;
         y0 = y;
      }
      ctx.stroke();

	  // draw limits
	  var xy = '(' + _plotter_format_number(x1) + ',' + _plotter_format_number(y1) + ')';
	  ctx.fillText(xy, x1 * a + e + 2, y1 * d + f - 4);
	  xy = '(' + _plotter_format_number(x2) + ',' + _plotter_format_number(y2) + ')';
	  var xyWidth = ctx.measureText(xy);
	  ctx.fillText(xy, x2 * a + e - 2 - xyWidth.width, y2 * d + f + 10);
   });
}

function _plotter_format_number(x) {
	var mod = Math.abs(x);
	if (mod >= 10) {
		return parseInt(x);
	}
	if (mod > 1) {
		return parseInt(x * 10) / 10;
	}
	return parseInt(x * 100) / 100;
}
