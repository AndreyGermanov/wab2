/*
 * © 2009 ROBO Design
 * http://www.robodesign.ro
 *
 * $Date: 2009-05-12 18:23:21 +0300 $
 */

function tool_pencil (app) {
  profiler.start('pencil.constructor');

  var _self = this,
      context = app.layer.context,
      mouse = app.mouse,
      width = app.image.width,
      height = app.image.height;

  this.mousedown = function (ev) {
    _self.x = ev.x_;
    _self.y = ev.y_;
    return true;
  };

  this.mousemove = function (ev) {
    if (mouse.buttonDown) {
      context.beginPath();
      context.moveTo(_self.x, _self.y);
      context.lineTo(ev.x_, ev.y_);
      context.stroke();
      context.closePath();
      _self.x = ev.x_;
      _self.y = ev.y_;
      return true;
    }
    return false;
  };

  this.mouseup = function (ev) {
    return _self.mousemove(ev);
  };

  profiler.stop('pencil.constructor');
};

var PaintWebInstance = new (function () {
  var _self = this;
  this.layer = {canvas: null, context: null};
  this.tool = null;
  this.doc = document;
  this.win = window;
  this.mouse = {x: 0, y: 0, buttonDown: false};
  this.image = {width: 0, height: 0, zoom: 1};

  var MathRound = Math.round,
      mouse = this.mouse,
      image = this.image,
      tool = null;

  function init () {
    profiler.start('init');

    _self.layer.canvas = _self.doc.getElementById('imageView');
    _self.layer.context = _self.layer.canvas.getContext('2d');
    _self.image.width = _self.layer.canvas.width;
    _self.image.height = _self.layer.canvas.height;

    _self.tool = new tool_pencil(_self);
    tool = _self.tool;

    _self.layer.canvas.addEventListener('mousedown', _self.ev_canvas, false);
    _self.layer.canvas.addEventListener('mousemove', _self.ev_canvas, false);
    _self.layer.canvas.addEventListener('mouseup',   _self.ev_canvas, false);

    profiler.stop('init');
  };

  // The general-purpose event handler. This function just determines the mouse 
  // position relative to the canvas element.
  this.ev_canvas = function (ev) {
    if (!tool) {
      return false;

    }
    
    switch (ev.type) {
      case 'mousedown':
        if (mouse.buttonDown) {
          return false;
        }
        mouse.buttonDown = true;
        break;

      case 'mouseup':
        if (!mouse.buttonDown) {
          return false;
        }
        mouse.buttonDown = false;
    }

    if (!('x_' in ev)) {
      if ('layerX' in ev) {
        if (image.zoom == 1) {
          ev.x_ = mouse.x = ev.layerX;
          ev.y_ = mouse.y = ev.layerY;
        } else {
          ev.x_ = mouse.x = MathRound(ev.layerX / image.zoom);
          ev.y_ = mouse.y = MathRound(ev.layerY / image.zoom);
        }
      } else if ('offsetX' in ev) {
        if (image.zoom == 1) {
          ev.x_ = mouse.x = ev.offsetX;
          ev.y_ = mouse.y = ev.offsetY;
        } else {
          ev.x_ = mouse.x = MathRound(ev.offsetX / image.zoom);
          ev.y_ = mouse.y = MathRound(ev.offsetY / image.zoom);
        }
      }
    }

    if (ev.type in tool && tool[ev.type](ev)) {
      ev.preventDefault();
      return true;
    } else {
      return false;
    }
  };

  init();
})();

// vim:set spell spl=en fo=wan1croqlt tw=80 ts=2 sw=2 sts=2 sta et ai cin fenc=utf-8 ff=unix:

