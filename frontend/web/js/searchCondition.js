(function($) {

	$.extend($, {
		sc : {
			setVal: function(mkey , mval) {
				if(null == mkey || undefined == mkey || '' == mkey){
					return undefined;
				}
				if(null == mval || undefined == mval || '' == mval){
					return undefined;
				}
				var scs = window.localStorage.search_condition;
				if(null == scs || undefined == scs || "" == scs){
					var obj = '{"' + mkey + '":"' + mval + '"}';
					window.localStorage.search_condition = obj;
					//window.localStorage.search_condition = JSON.stringify( { ('' + mkey) : ('' + mval) } );
					return;
				}

				var scsObj = JSON.parse(scs);
				scsObj[mkey] = mval;
				window.localStorage.search_condition = JSON.stringify(scsObj);
				return;
			},

			getVal : function(mkey) {
				if(null == mkey || undefined == mkey || '' == mkey){
					return undefined;
				}
				var scs = window.localStorage.search_condition;
				if(null == scs || undefined == scs || "" == scs){
					return undefined;
				}

				var scsObj = JSON.parse(scs);
				return scsObj[mkey];
			},

			removeVal : function(mkey) {
				if(null == mkey || undefined == mkey || '' == mkey){
					return undefined;
				}
				var scs = window.localStorage.search_condition;
				if(null == scs || undefined == scs || "" == scs){
					return;
				}
				var scsObj = JSON.parse(scs);
				delete scsObj[mkey];
				window.localStorage.search_condition = JSON.stringify(scsObj);
				return;
			},

			clearVal : function() {
				localStorage.removeItem('search_condition');
			}
		},

		wt : {
			addVal : function(wtKey, wtVal) {
				if(null == wtKey || undefined == wtKey || '' == wtKey){
					return undefined;
				}
				if(null == wtVal || undefined == wtVal || '' == wtVal){
					return undefined;
				}

				var wts = window.localStorage.work_time;
				if(null == wts || undefined == wts || "" == wts){
					var wtArray = new Array();
					wtArray.push(wtVal);
					var wtArrayStr = wtArray.join();
					
					var obj = '{"' + wtKey + '":"' + wtArrayStr + '"}';

					window.localStorage.work_time = obj;
					//window.localStorage.work_time = JSON.stringify( { ('' + wtKey) : ('' + wtVal) } );
					return;
				}
				
				var scsObj = JSON.parse(wts);
				var scsArrayStr = scsObj[wtKey];
				
				var wtArray = new Array();
				wtArray = scsArrayStr.split(',');

				if ($.inArray(wtVal,wtArray) ==-1) {
					if(wtArray.length < 20){
						wtArray.push(wtVal);
					}else{
						wtArray.shift();
						wtArray.push(wtVal);
					}
				};
				

				scsObj[wtKey] = wtArray.join();
				window.localStorage.work_time = JSON.stringify(scsObj);
				return;

			},

			getVal : function(wtKey){

				if(null == wtKey || undefined == wtKey || '' == wtKey){
					return undefined;
				}
				var wts = window.localStorage.work_time;
				if(null == wts || undefined == wts || "" == wts){
					return undefined;
				}

				var scsObj = JSON.parse(wts);
				return scsObj[wtKey];
			},

			clearVal : function(){
				localStorage.removeItem('work_time');
			}
		}
	});

	$.extend($.fn, {
		
	});
}) (Zepto);
