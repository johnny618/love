(function($) {
	var g = {
		'normal' : {
			'transitionKey' : 'transition',
			'transformKey' : 'transform',
			'transitionProperty' : {
				'width' : 'width',
				'height' : 'height',
				'transform' : 'transform'
			}
		},
		'webkit' : {
			'transitionKey' : '-webkit-transition',
			'transformKey' : '-webkit-transform',
			'transitionProperty' : {
				'width' : 'width',
				'height' : 'height',
				'transform' : '-webkit-transform'
			}
		}
	};

	var kernel = getKernel();

	function getKernel() {
		var u = navigator.userAgent;
		if(u.indexOf('AppleWebKit') > -1) {
			return 'webkit';
		} else {
			return 'normal';
		}
	}

	function getTransitionStyle() {
		if(arguments.length == 1 && $.isArray(arguments[0])) {
			var configs = arguments[0];
			var style = {};
			//for(var kernel in g) {
			var transition = "";
			for(var i in configs) {
				var c = configs[i];
				var str = getTransitionStr(kernel, c['property'], c['duration'], c['timing_func'] || null, c['delay'] || 0);
				if(str) {
					if(transition) {
						transition += ", ";
					}
					transition += str;
				}
			}
			style[g[kernel]['transitionKey']] = transition;
			//}

			return style;
		} else {
			var style = {};
			//for(var kernel in g) {
			var transition = getTransitionStr(kernel, arguments[0], arguments[1], arguments[2], arguments[3]);
			if(transition) {
				style[g[kernel]['transitionKey']] = transition;
			}
			//}

			return style;
		}
	}

	function getTransitionStr(type, property, duration, timing_func, delay) {
		if(!type || !property) {
			return false;
		}

		if(typeof(g[type]["transitionProperty"][property]) == "undefined") {
			return false;
		}

		duration = duration || 0;
		var str = g[type]["transitionProperty"][property] + " " + duration + "ms";

		if(timing_func || delay) {
			timing_func = timing_func || "ease";
			str += " " + timing_func;
		} 

		if(delay) {
			str += " " + delay + "ms";
		}

		return str;
	}

	function getCurrentTranslate(obj) {
		var transform = obj.css(g[kernel]["transformKey"]);
		if(!transform || transform == "none") {
			return -1;
		} else {
			var m = transform.match(/translate3d\((-?\d*\.?\d+)px, (-?\d*\.?\d+)px, (-?\d*\.?\d+)px\)/);
			if(m) {
				return { x : parseFloat(m[1], 10), y : parseFloat(m[2], 10), z : parseFloat(m[3], 10) };
			} else {
				return -2;
			}
		}
	}

	function getCurrentRotate(obj) {
		var transform = obj.css(g[kernel]["transformKey"]);
		if(!transform || transform == "none") {
			return -1;
		} else {
			var m = transform.match(/rotate\((-?\d*\.?\d+)deg\)/);
			if(m) {
				return { deg : parseFloat(m[1], 10) };
			} else {
				return -2;
			}
		}
	}

	function getTranslateStyle(obj, x, y, z) {
		var curTranslate = getCurrentTranslate(obj);
		var translateStr = "translate3d(" + x + "px, " + y + "px, " + z + "px)";
		var style = {};
		//for(var kernel in g) {
		if(curTranslate < 0) {
			if(curTranslate == -1) {
				style[g[kernel]["transformKey"]] = translateStr;
			} else {
				style[g[kernel]["transformKey"]] = obj.css(g[kernel]["transformKey"]) + " " + translateStr;
			}
		} else {
			style[g[kernel]["transformKey"]] = obj.css(g[kernel]["transformKey"]).replace(/translate3d\((-?\d*\.?\d+)px, (-?\d*\.?\d+)px, (-?\d*\.?\d+)px\)/, translateStr);
		}

		//$.extend(style, getTransitionStyle('transform', time));
		//}

		//$.extend(style, { '-webkit-backface-visibility' : 'hidden' });

		return style;
	}

	function getRotateStyle(obj, deg) {
		var curRotate = getCurrentRotate(obj);
		var rotateStr = "rotate(" + deg + "deg)";
		var style = {};
		//for(var kernel in g) {
		if(curRotate < 0) {
			if(curRotate == -1) {
				style[g[kernel]["transformKey"]] = rotateStr;
			} else {
				style[g[kernel]["transformKey"]] = obj.css(g[kernel]["transformKey"]) + " " + rotateStr;
			}
		} else {
			style[g[kernel]["transformKey"]] = obj.css(g[kernel]["transformKey"]).replace(/rotate\((-?\d*\.?\d+)deg\)/, rotateStr);
		}

		return style;
	}

	function getMoveDirection(dirStr) {
		if(dirStr == "h" || dirStr == "horizontal") {
			return 1;
		} else if(dirStr == "v" || dirStr == "vertical") {
			return 2;
		} else {
			return 3;
		}
	}

	function getTouchIndex(obj, touches) {
		if(touches.length == 0) {
			return -1;
		} else if(touches.length == 1) {
			return 0;
		} else {
			for(var i = 0; i < touches.length; i++) {
				if(obj.indexOf(touches[i].target) >= 0) {
					return i;
				}
			}
		}
	}

	$.extend($, {
		animate : function(node, p, time, afterFunc) {
			if(node.size() == 0) {
				return;
			}

			var style = {};
			var type = [];
			if($.type(p.x) != 'undefined' || $.type(p.y) != 'undefined' || $.type(p.z) != 'undefined') {
				var x, y, z;
				var curTranslate = getCurrentTranslate(node);
				if($.type(p.x) != 'undefined') {
					x = p.x;
				} else {
					x = curTranslate < 0 ? 0 : curTranslate.x;
				}

				if($.type(p.y) != 'undefined') {
					y = p.y;
				} else {
					y = curTranslate < 0 ? 0 : curTranslate.y;
				}

				if($.type(p.z) != 'undefined') {
					z = p.z;
				} else {
					z = curTranslate < 0 ? 0 : curTranslate.z;
				}

				$.extend(style, getTranslateStyle(node, x, y, z));
				type.push("transform");
			}

			if($.type(p.deg) != 'undefined') {
				$.extend(style, getRotateStyle(node, p.deg));
				$.inArray(type) == -1 || type.push("transform");
			}

			if($.type(p.width) != 'undefined') {
				$.extend(style, { 'width' : p.width });
				type.push("width");
			}

			if($.type(p.height) != 'undefined') {
				$.extend(style, { 'height' : p.height });
				type.push("height");
			}

			if(!type.length) {
				return;
			}

			if(time) {
				var transition = [];
				for(var i in type) {
					transition.push({
						'property' : type[i],
						'duration' : time
					});
				}
				$.extend(style, getTransitionStyle(transition));
			}

			node.css(style);
			if($.isFunction(afterFunc)) {
				if(time) {
					setTimeout(function() {
						afterFunc(node);
					}, time);
				} else {
					afterFunc(node);
				}
			}
		},

		move : function(node, dx, dy, time, afterFunc) {
			if(node.size() == 0) {
				return;
			}

			var curTranslate = getCurrentTranslate(node);
			var x, y, z;
			if(curTranslate < 0) {
				x = dx;
				y = dy;
				z = 0;
			} else {
				x = curTranslate.x + dx;
				y = curTranslate.y + dy;
				z = curTranslate.z;
			}

			node.css($.extend(getTranslateStyle(node, x, y, z), getTransitionStyle('transform', time)));
			if($.isFunction(afterFunc)) {
				if(time) {
					setTimeout(function() {
						afterFunc(node);
					}, time);
				} else {
					afterFunc(node);
				}
			}
		},

		moveTo : function(node, x, y, time, afterFunc) {
			if(node.size() == 0) {
				return;
			}
			var curTranslate = getCurrentTranslate(node);
			var z;
			if(curTranslate < 0) {
				z = 0;
			} else {
				z = curTranslate.z;
			}
			node.css($.extend(getTranslateStyle(node, x, y, z), getTransitionStyle('transform', time)));
			if($.isFunction(afterFunc)) {
				if(time) {
					setTimeout(function() {
						afterFunc(node);
					}, time);
				} else {
					afterFunc(node);
				}
			}
		},

		rotate : function(node, dDeg, time, afterFunc) {
			if(node.size() == 0) {
				return;
			}

			var curRotate = getCurrentRotate(node);
			var deg;
			if(curRotate < 0) {
				deg = dDeg;
			} else {
				deg = curRotate.deg + dDeg;
			}

			node.css($.extend(getRotateStyle(node, deg), getTransitionStyle('transform', time)));
			if($.isFunction(afterFunc)) {
				if(time) {
					setTimeout(function() {
						afterFunc(node);
					}, time);
				} else {
					afterFunc(node);
				}
			}
		},

		rotateTo : function(node, deg, time, afterFunc) {
			if(node.size() == 0) {
				return;
			}

			node.css($.extend(getRotateStyle(node, deg), getTransitionStyle('transform', time)));
			if($.isFunction(afterFunc)) {
				if(time) {
					setTimeout(function() {
						afterFunc(node);
					}, time);
				} else {
					afterFunc(node);
				}
			}
		},

		movable : function(node, c) {
			if(node.size() == 0) {
				return;
			} else if(node.size() > 1) {
				node.each(function() {
					$(node).movable(c);
				});
				return node;
			}

			if(node.data("movable")) {
				node.unbind("touchstart touchmove touchend");
			}

			c = c || {};
			var direction = getMoveDirection(c.direction),
				canMoveHorizontal = (direction & 1),
				canMoveVertical = (direction & 2),
				minX = typeof(c.minX) == "undefined" ? null : c.minX,
				maxX = typeof(c.maxX) == "undefined" ? null : c.maxX,
				minY = typeof(c.minY) == "undefined" ? null : c.minY,
				maxY = typeof(c.maxY) == "undefined" ? null : c.maxY,
				shouldPreventMoveEvent = typeof(c.shouldPreventMoveEvent) == "undefined" ? false : c.shouldPreventMoveEvent,
				listeners = $.extend({}, c.listeners);
			var startX, startY, tX, tY;
			var movable = true;
			node.data("movable", true);
			node.on('touchstart', function(e) {
				console.log("Touch Start");
				if($.isFunction(listeners['movebefore'])) {
					movable = listeners['movebefore'](node, e) === false ? false : true;
				} else {
					movable = true;
				}
				if(movable && e.touches.length > 0) {
					var index = getTouchIndex(node, e.touches);
					startX = e.touches[index].screenX;
					startY = e.touches[index].screenY;
					var curTranslate = getCurrentTranslate(node);
					if(curTranslate < 0) {
						tX = 0;
						tY = 0;
					} else {
						tX = curTranslate.x;
						tY = curTranslate.y;
					}

					if($.isFunction(listeners['movestart'])) {
						listeners['movestart'](node, tX, tY, e);
					}
				}
			}).on('touchmove', function(e) {
				if(movable && e.touches.length > 0) {
					var index = getTouchIndex(node, e.touches);
					var dX = canMoveHorizontal ? e.touches[index].screenX - startX : 0; 
					var dY = canMoveVertical ? e.touches[index].screenY - startY : 0;
					if(canMoveHorizontal) {
						if(minX !== null && tX + dX < minX) {
							dX = minX - tX;
						}

						if(maxX !== null && tX + dX > maxX) {
							dX = maxX - tX;
						}
					}

					if(canMoveVertical) {
						if(minY !== null && tY + dY < minY) {
							dY = minY - tY;
						}

						if(maxY !== null && tY + dY > maxY) {
							dY = maxY - tY;
						}
					}
					node.moveTo(tX + dX, tY + dY, 0);

					if($.isFunction(shouldPreventMoveEvent)) {
						return shouldPreventMoveEvent(e, tX + dX, tY + dY);
					} else {
						return shouldPreventMoveEvent ? true : false;
					}
				}
			}).on('touchend', function(e) {
				if(!movable) {
					return;
				}

				if($.isFunction(listeners['moveend'])) {
					var curTranslate = getCurrentTranslate(node);
					var x = 0, y = 0;
					if(!(curTranslate < 0)) {
						x = curTranslate.x;
						y = curTranslate.y;
					}
					listeners['moveend'](node, x, y, x - tX, y - tY, e);
				}
			});

			return node;
		}
	});

	$.extend($.fn, {
		animate : function(p, time, afterFunc) {
			$.animate(this, p, time, afterFunc);
		},

		move : function(dx, dy, time, afterFunc) {
			$.move(this, dx, dy, time, afterFunc);
		},

		moveTo : function(x, y, time, afterFunc) {
			$.moveTo(this, x, y, time, afterFunc);
		},

		rotate : function(dDeg, time, afterFunc) {
			$.rotate(this, dDeg, time, afterFunc);
		},

		rotateTo : function(deg, time, afterFunc) {
			$.rotateTo(this, deg, time, afterFunc);
		},

		movable : function(c) {
			return $.movable(this, c);
		}
	});
}) (Zepto);