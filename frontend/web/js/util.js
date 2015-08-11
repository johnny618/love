var G = G || {};
G.util = G.util || {};
G.util.go = function(url, w) {
	w = w || window;
	if(typeof w == 'string') {
		switch(w) {
			case '_blank':
				window.open(url);
				break;
			case '_self':
				window.self.location.href = url;
				break;
			case '_top':
				window.top.location.href = url;
				break;
			case '_parent':
				window.parent.location.href = url;
				break;
			default:
				window[w].location.href = url;
				break;
		}
	} else {
		w.location.href = url;
	}
};

(function($) {
	$.extend($, {
		goUrl : function(url, w) {
			G.util.go(url, w);
		},

		replaceUrl : function(url) {
			if(!!(window.history && history.replaceState)){
			   window.history.replaceState({}, document.title, url);
			} else {
			   location.replace(url);
			}
		},

		browserType : function() {
			var ua = window.navigator.userAgent.toLowerCase();
			if(ua.indexOf('zozoms') != -1) {
				return 1;
			} else if(ua.indexOf('micromessager') != -1) {
				return 2;
			} else {
				return 3;
			}
		},

		getUrlParam : function(name) {    //获取get请求后的参数
			var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
			var r = window.location.search.substr(1).match(reg);
			return r == null ? false : unescape(r[2]);
		},

		checkParam : function(param){  //判断参数时候为 null、undefined、'', 若是，返回false；否则，返回true
			if(null == param || 'undefined' == typeof(param) || '' == param){
				return false;
			}
			return true;
		},

		goBack : function(){  //返回上个页面
			window.history.back();
		}
	});

	$.extend($.fn, {
		ajaxSubmit : function(clbk) {
			if(!this.is("form")) {
				return;
			}

			var time = new Date().getTime();
			var frame = $('<iframe style="display:none;" name="frame' + time + '"></iframe>');
			$("body").append(frame);
			var self = this;
			frame.on("load", function() {
				var fw = frame.get(0).contentWindow;
				var data = $.parseJSON(fw.document.body.innerHTML);
				self.removeAttr("target");
				frame.attr("src", "about:blank");
				try {
					fw.document.write("");
					fw.document.clear();
				} catch(e) {}
				frame.remove();
				$.isFunction(clbk) && clbk(data);
			});
			this.attr("target", "frame" + time);
			this.get(0).submit();
		}
	});
}) (Zepto);
