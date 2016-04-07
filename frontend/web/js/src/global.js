(function($) {

	function SearchCondition() {
		this.init();
	}

	$.extend(SearchCondition.prototype, {
		init : function() {
			this.shopId = 0;
			this.provinceId = 0;
			this.cityId = 0;
			this.districtId = 0;
			this.subDistrictId = 0;
			this.lat = null;
			this.lng = null;
			this.industryId = 0;
			this.occupationId = 0;
			this.salaryType = 0;
			this.minSalary = null;
			this.maxSalary = null;
			this.isToday = null;
			this.day = '';
			this.needAuth = false;
			this.sortType = 1;
			this.pageNo = 1;
		},

		set : function(c) {
			if(typeof(c['shop_id']) != "undefined") {
				this.shopId = c['shop_id'];
			}

			if(typeof(c['province_id']) != "undefined") {
				this.provinceId = c['province_id'];
			}

			if(typeof(c['city_id']) != "undefined") {
				this.cityId = c['city_id'];
			}

			if(typeof(c['district_id']) != "undefined") {
				this.districtId = c['district_id'];
			}

			if(typeof(c['sub_district_id']) != "undefined") {
				this.subDistrictId = c['sub_district_id'];
			}

			if(typeof(c['lat']) != "undefined") {
				this.lat = c['lat'];
			}

			if(typeof(c['lng']) != "undefined") {
				this.lng = c['lng'];
			}

			if(typeof(c['industry_id']) != "undefined") {
				this.industryId = c['industry_id'];
			}

			if(typeof(c['occupation_id']) != "undefined") {
				this.occupationId = c['occupation_id'];
			}

			if(typeof(c['salary_type']) != "undefined") {
				this.salaryType = c['salary_type'];
			}

			if(typeof(c['min_salary']) != "undefined") {
				this.minSalary = c['min_salary'];
			}

			if(typeof(c['max_salary']) != "undefined") {
				this.maxSalary = c['max_salary'];
			}

			if(typeof(c['is_today']) != "undefined") {
				this.isToday = c['is_today'];
			}

			if(typeof(c['day']) != "undefined") {
				this.day = c['day'];
			}

			if(typeof(c['need_auth']) != "undefined") {
				this.needAuth = c['need_auth'];
			}

			if(typeof(c['sort_type']) != "undefined") {
				this.sortType = c['sort_type'];
			}

			if(typeof(c['page_no']) != "undefined") {
				this.pageNo = c['page_no'];
			}
		},

		get : function() {
			return {
				'shop_id' : this.shopId,
				'province_id' : this.provinceId,
				'city_id' : $.logic.getCityId(),//this.cityId,
				'district_id' : this.districtId,
				'sub_district_id' : this.subDistrictId,
				'lat' : this.lat,
				'lng' : this.lng,
				'industry_id' : this.industryId,
				'occupation_id' : this.occupationId,
				'salary_type' : this.salaryType,
				'min_salary' : this.minSalary,
				'max_salary' : this.maxSalary,
				'is_today' : this.isToday,
				'day' : this.day,
				'need_auth' : this.needAuth,
				'sort_type' : this.sortType,
				'page_no' : this.pageNo
			};
		},

		toString : function() {
			return JSON.stringify(this.get());
		},

		restore : function(data) {
			if(!data) {
				return;
			}

			this.set(data);
		},

		getQueryParams : function() {
			var params = {};
			if(this.shopId) {
				params['shop_id'] = this.shopId;
			}

			if($.logic.getCityId()) {
				params['city_id'] = $.logic.getCityId();//this.cityId;
			}

			if(this.districtId) {
				params['district_id'] = this.districtId;
			}

			if(this.subDistrictId) {
				params['sub_district_id'] = this.subDistrictId;
			}

			if(this.lat != null) {
				params['lat'] = this.lat;
			}

			if(this.lng != null) {
				params['lng'] = this.lng;
			}

			if(this.industryId) {
				params['industry_id'] = this.industryId;
			}

			if(this.occupationId) {
				params['occupation_id'] = this.occupationId;
			}

			if(this.minSalary != null) {
				params['min_salary'] = this.minSalary * 100;
			}

			if(this.maxSalary != null) {
				params['max_salary'] = this.maxSalary * 100;
			}

			if(this.isToday != null && this.isToday) {
				var d = new Date();
				params['work_date'] = d.getFullYear() + "-" + (d.getMonth() + 1) + "-" + d.getDate();
			} else if(this.day) {
				params['work_date'] = this.day;
			}

			if(this.needAuth != null) {
				params['is_cert'] = this.needAuth ? 2 : 1;
			}

			params['sort_type'] = this.sortType;
			params['page_no'] = this.pageNo;

			return params;
		},

		query : function(clbkFunc) {
			$.getJSON(baseUrl + 'index.php?mod=position&act=search', 
				$.extend(this.getQueryParams(), { ts : new Date().getTime() / 60000 }), 
				function(ret) {
					$.isFunction(clbkFunc) && clbkFunc(ret);
				}
			);
		}

	});

	function GlobalMenu() {
		this.init();
	}

	$.extend(GlobalMenu.prototype, {
		init : function() {
			this.node = $('<div id="G_MENU" class="g-menu-panel">' +
				'<div class="g-mask mask"></div>' +
				'<ul class="g-menu bg-color-b5">' +
					'<li class="g-home"><i></i>企业中心</li>' +
					'<li class="g-account"><i></i>我的账本</li>' +
					'<li class="g-position"><i></i>职位列表</li>' +
					'<li class="g-shop"><i></i>账户信息</li>' +
					'<li class="g-setting"><i></i>设置</li>' +
					'<li class="g-privilege"><i></i>账户管理</li>' +
				'</ul>' +
			'</div>');
			$("body").append(this.node);
			this.moveLength = $(window).width() * 0.5625;
			this.node.width($(window).width());
			this.node.height($(window).height());
			this.node.find(".g-menu").moveTo(- this.moveLength, 0);
			this.node.find(".g-menu li").css("padding-left", $(window).width() / 10);
			this.node.hide();

			if(menuItemId) {
				this.select(menuItemId);
			}

			var self = this;
			this.node.find(".g-mask").click(function() {
				self.hide();
			});

			if($.logic.isSubAccount()) {
				$(".g-privilege").hide();
			} else {
				$(".g-privilege").show();
			}

			this.node.find("li").click(function() {
				if($(this).hasClass('active')) {
					return;
				}

				var id = $(this).attr("class").match(/(^|\s)g-(\w+)($|\s)/)[2];
				switch(id) {
					case 'home':
						location.href = './enterpriseCenter.html';
						break;
					case 'account':
						location.href = './myAccountBook.html';
						break;
					case 'position':
						location.href = './positionList.html';
						break;
					case 'shop':
						location.href = './editShop.html';
						break;
					case 'privilege':
						location.href = './accountList.html';
						break;
					case 'setting':
						location.href = './setUp.html';
						break;
				}
			});
		},

		show : function() {
			this.node.show();
			$("body").moveTo(this.moveLength, 0, 300);
		},

		hide : function() {
			var self = this;
			$("body").moveTo(0, 0, 300, function() {
				self.node.hide();
			});
		},

		select : function(id) {
			var item = this.node.find("li.g-" + id);
			if(!item.size()) {
				return;
			}

			if(item.hasClass("active")) {
				return;
			}

			this.node.find("li.active").removeClass("active");
			item.addClass('active');
		}
	});

	var sc = null, gm = null, menuItemId = null;
	var SEARCH_CONDITION_KEY = 'search';
	var REGION_DATA_KEY = 'region';
	var GLOBAL_DATA_KEY = 'global_config';
	var FAV_IDS_KEY = 'fav_ids';

	function getGlobalMenu() {
		if(!gm) {
			gm = new GlobalMenu();
		}

		return gm;
	}

	$.extend($, {
		util : {
			getLocation : function(clbkFunc) {
				if(window.navigator.geolocation) {
					window.navigator.geolocation.getCurrentPosition(function(p) {
						$.getJSON(baseUrl + 'index.php?act=convertLocation', {
							lat : p.coords.latitude,
							lng : p.coords.longitude
						}, function(ret) {
							if(ret && !ret.errno) {
								$.isFunction(clbkFunc) && clbkFunc(0, ret.lng, ret.lat);
							} else {
								$.isFunction(clbkFunc) && clbkFunc(3);
							}
						});
					}, function() {
						$.isFunction(clbkFunc) && clbkFunc(1);
					});
				} else {
					$.isFunction(clbkFunc) && clbkFunc(2);
				}
			},

			getPhone : function() {
				return $.util.cache.val('phone_num');
			},

			setPhone : function(phoneNum) {
				$.util.cache.val('phone_num', phoneNum);
			},

			cache : {
				val : function() {
					if(arguments.length == 1) {
						var key = arguments[0];
						if(window.localStorage) {
							var dataStr = localStorage.getItem(key);
							if(dataStr && dataStr.length) {
								var data = $.parseJSON(dataStr);
								if(data) {
									var expire = data['expire'];
									if(!expire || expire > new Date().getTime()) {
										return data['data'];
									} else {
										return null;
									}
								} else {
									return null;
								}
							} else {
								return null;
							}
						} else {
							var dataStr = $.fn.cookie(key);
							if(dataStr && dataStr.length) {
								return $.parseJSON(dataStr);
							} else {
								return null;
							}
						}

						
					} else if(arguments.length >= 2) {
						var key = arguments[0];
						var value = arguments[1];
						var expireSeconds = 0;
						if(arguments.length >= 3) {
							expireSeconds = arguments[2];
						}

						if(window.localStorage) {
							localStorage.setItem(key, JSON.stringify({ 
								data : value, 
								expire : expireSeconds ? new Date().getTime() + expireSeconds * 1000 : 0
							}));
						} else {
							return $.fn.cookie(key, value, { expires : expireSeconds });
						}
					}
				}
			},

			getQueryString : function(name) {
				var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
				var r = window.location.search.substr(1).match(reg);
				if (r != null) return decodeURIComponent(r[2]); return null;
			},

			ajax : {
				get : function(url, data, clbkFunc) {
					$.getJSON(url, data, clbkFunc);
				},

				post : function(url, data, clbkFunc, format) {
					format = format || "json";
					$.post(url, data, clbkFunc, format);
				}
			}
		},

		config : {
			baseUrl : document.domain == 'app.thepartime.com' ? 'http://app.thepartime.com/' : 'http://114.215.173.66/qjob/app/',
		},

		ui : {
			menu : {
				open : function() {
					getGlobalMenu().show();
				},

				close : function() {
					getGlobalMenu().hide();
				},

				select : function(id) {
					//getGlobalMenu().select(id);
					if(gm) {
						gm.select(id);
					} else {
						menuItemId = id;
					}
				}
			}
		},

		logic : {
			goLogin : function(needBack) {
				if(typeof(needBack) == "undefined") {
					needBack = false;
				}

				var backUrl;
				if(needBack) {
					backUrl = location.href;
				} else {
					backUrl = './enterpriseCenter.html';
				}

				location.href = './login.html?back_url=' + encodeURIComponent(backUrl);
			},

			checkLogin : function(nowUrl,vUrl){
				if($.fn.cookie('c_uid')){
					// alert("已登录");
				}else{
					$.logic.login(nowUrl,vUrl);
				}
			},

			login : function(nowUrl,vUrl){
				if(vUrl==null){
					// var vUrl = "http://wx-dev.koudaicp.com/wapi/reurl?";
					var vUrl = "http://wx.koudaicp.com/wapi/reurl?";
					//var vUrl = "http://www.koudaicp.com/wapi/reurl?";
				}

				$.post(
					'./wapi/respurl',
					{
						url : nowUrl
					},
					function(ret) {
						if(!ret.errno){
							// var verification = encodeURIComponent(vUrl);
							// var selfUrl = encodeURIComponent(nowUrl);
							// var url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx28be63216cb43a4b&redirect_uri="+verification+"reurl="+selfUrl+"&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";
							// alert(ret.data.url);
							// location.replace(url);
							location.replace(ret.data.url);
						}						
					},
					'json'
				);
			},

			checkValid : function() {
				var state = $.fn.cookie('state');
				if(!state) {
					$.logic.goLogin();
					return;
				}

				state = parseInt(state);
				switch(state) {
					case 1:
						location.href = "./fillEnterpriseInfor.html";
						return;
					case 2:
						location.href = "./waittingVerify.html";
						return;
					case 4:
						location.href = "./verifyFaild.html";
						return;
					default:
						break;
				}

				var shopId = $.fn.cookie('shop_id');
				if(!shopId || shopId == "0") {
					location.href = "./createDefaultShop.html";
				}

				return;
			},

			checkOpenToken : function() {
				return !!$.fn.cookie('open_token');
			},

			goWXAuth: function() {
				$.util.cache.val('auth_back_url', location.href);
				location.replace('http://dwz.cn/EyPNd');
			},

			isSubAccount : function() {
				if(arguments.length > 0) {
					var isSubAccount = arguments[0] ? 1 : 0;
					$.util.cache.val('is_sub_account', isSubAccount);
				} else {
					return !!$.util.cache.val('is_sub_account');
				}
			},

			getCityId : function() {
				return $.util.cache.val('city_id');
			},

			setCityId : function(cityId) {
				$.util.cache.val('city_id', cityId);
			},

			search : {
				reset : function(isDeep) {
					isDeep = typeof(isDeep) == "undefined" ? true : false;
					sc.init();
					if(isDeep) {
						sc.set('need_auth', null);
					}
					$.util.cache.val(SEARCH_CONDITION_KEY, sc.get());
				},

				set : function() {
					if(arguments.length == 1) {
						var c = arguments[0];
						sc.set(c);
					} else if(arguments.length >= 2) {
						var key = arguments[0];
						var value = arguments[1];
						var c = {};
						c[key] = value;
						sc.set(c);
					}
					
					$.util.cache.val(SEARCH_CONDITION_KEY, sc.get());
				},

				get : function() {
					if(arguments.length) {
						return sc.get()[arguments[0]];
					} else {
						return sc.get();
					}
				},

				query : function(clbkFunc) {
					return sc.query(clbkFunc);
				}
			},

			collect : {
				getFavIds : function(clbkFunc) {
					var favIds = $.util.cache.val(FAV_IDS_KEY);
					if(favIds) {
						$.isFunction(clbkFunc) && clbkFunc(0, favIds);
						return;
					}

					$.getJSON($.config.baseUrl + 'index.php?mod=favourite&act=listKeys', {}, function(ret) {
						if(ret && !ret.errno) {
							var favIds = [];
							for(var i in ret.data) {
								favIds.push(parseInt(ret.data[i]));
							}
							$.util.cache.val(FAV_IDS_KEY, favIds, 300);
							$.isFunction(clbkFunc) && clbkFunc(0, favIds);
						} else {
							$.isFunction(clbkFunc) && clbkFunc(ret.errno);
						}
					});
				},

				addFav : function(id, clbkFunc) {
					id = parseInt(id);
					$.post($.config.baseUrl + 'index.php?mod=favourite&act=fav', {
						'work_time_id' : id,
						'state' : 1
					}, function(ret) {
						if(ret && !ret.errno) {
							$.logic.collect.getFavIds(function(code, favIds) {
								if(code == 0) {
									if($.inArray(id, favIds) == -1) {
										favIds.push(id);
										$.util.cache.val(FAV_IDS_KEY, favIds, 300);
									}
								}

								$.isFunction(clbkFunc) && clbkFunc(0);
							});
						} else {
							clbkFunc(ret.errno);
						}
					}, 'json');
				},

				cancelFav : function(id, clbkFunc) {
					id = parseInt(id);
					$.post($.config.baseUrl + 'index.php?mod=favourite&act=fav', {
						'work_time_id' : id,
						'state' : 2
					}, function(ret) {
						if(ret && !ret.errno) {
							$.logic.collect.getFavIds(function(code, favIds) {
								if(code == 0) {
									for(var i = 0; i < favIds.length; i++) {
										if(favIds[i] == id) {
											favIds.splice(i, 1);
											i--;
										}
									}
									$.util.cache.val(FAV_IDS_KEY, favIds, 300);
								}

								$.isFunction(clbkFunc) && clbkFunc(0);
							});
						} else {
							clbkFunc(ret.errno);
						}
					}, 'json');
				}
			},

			config : {
				getGlobalConfig : function(clbkFunc) {
					var data = $.util.cache.val(GLOBAL_DATA_KEY);
					if(data) {
						$.isFunction(clbkFunc) && clbkFunc(data);
						return;
					}

					$.getJSON($.config.baseUrl + 'data/config.js', {}, function(ret) {
						data = ret;
						$.util.cache.val(GLOBAL_DATA_KEY, data, 3600);
						$.isFunction(clbkFunc) && clbkFunc(data);
					});
				},

				getRegionConfig : function(clbkFunc) {
					var data = $.util.cache.val(REGION_DATA_KEY);
					if(data) {
						$.isFunction(clbkFunc) && clbkFunc(data);
						return;
					}

					$.getJSON($.config.baseUrl + 'data/provCityConfig.js', {}, function(ret) {
						data = ret;
						$.util.cache.val(REGION_DATA_KEY, data, 3600);
						$.isFunction(clbkFunc) && clbkFunc(data);
					});
				},

				clearGlobalConfig : function() {
					$.util.cache.val(GLOBAL_DATA_KEY, '', 1);
				},

				clearRegionConfig : function() {
					$.util.cache.val(REGION_DATA_KEY, '', 1);
				},

				getQuestionConfig : function(clbkFunc) {
					var data = $.util.cache.val('user_question');
					if(data) {
						$.isFunction(clbkFunc) && clbkFunc(data);
						return;
					}

					$.getJSON($.config.baseUrl + 'data/userQuestions.js', {}, function(ret) {
						data = ret;
						$.util.cache.val('user_question', data, 3600);
						$.isFunction(clbkFunc) && clbkFunc(data);
					});
				}
			}
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

var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "//hm.baidu.com/hm.js?2fe91da7a66c87bbe4a08cd80c11c784";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(hm, s);
})();
