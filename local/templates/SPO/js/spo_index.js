SpoUrl = function(url){
	function construct(me, url){
		var search, lastUrl;
		me.urlParams = {};

		// если пришел url, разбираем его, иначе текущий
		if(url){
			lastUrl = url.split('#');
			me.hash = (lastUrl.length === 2) ? lastUrl[1] : '';
			lastUrl = lastUrl[0];

			lastUrl = url.split('?');
			me.path = lastUrl[0];
			search = (lastUrl.length === 2) ? lastUrl[1] : '';
		}else{
			me.path = location.pathname;
			me.hash = location.hash;
			search = (location.search.length > 1) ? location.search.substring(1, location.search.length) : '';
		}

		me.getObjectFromUrl(search);
	}

	this.getObjectFromUrl = function(search){
		var getParams = (search.length > 1) ? search.split('&') : [],
			getParam,
			getParamName,
			getParamValue,
			eqPosition,
			i;

		if(!$.isArray(getParams)){
			return;
		}

		for(i=0; i<getParams.length; i++){
			getParam = getParams[i];
			eqPosition = getParam.indexOf('=');

			getParamName  = eqPosition > 0 ? getParam.substr(0, eqPosition) : eqPosition;
			getParamValue = eqPosition > 0 ? getParam.substr(eqPosition+1) : '';

			this.addParam(getParamName, getParamValue);
		}
	};
	this.toString = function(overrideUrlParams){
		var params=[],
			getParamName,
			u = $.extend({}, this.urlParams),
			i;

		overrideUrlParams = overrideUrlParams || {};
		$.extend(u, overrideUrlParams);

		for(getParamName in u){
			if(!u.hasOwnProperty(getParamName)){
				continue;
			}
			if($.isArray(u[getParamName])){
				for(i=0; i<u[getParamName].length; i++){
					params.push(getParamName + '=' + encodeURI(u[getParamName][i]));
				}
			}else{
				params.push(getParamName + '=' + encodeURI(u[getParamName]));
			}
		}
		return this.path + '?' + params.join('&') + this.hash;
	};
	this.changeLocation = function(){
		document.location = this.toString();
	};
	this.reload = function(){
		document.location.reload();
	};
	this.addParam = function(getParamName, getParamValue){
		getParamValue = decodeURIComponent(getParamValue);
		if(typeof this.urlParams[getParamName] !== 'undefined'){ // если уже есть такой элемент
			if(!$.isArray(this.urlParams[getParamName])){ // если это пока не массив, делаем массив
				this.urlParams[getParamName] = [this.urlParams[getParamName]];
			}
			this.urlParams[getParamName].push(getParamValue);
		}else{
			this.urlParams[getParamName] = getParamValue;
		}
		return this;
	};
	this.changeParam = function(getParamName, getParamValue){
		getParamValue = decodeURIComponent(getParamValue);
		this.urlParams[getParamName] = getParamValue;
		return this;
	};
	this.removeParam = function(getParamName){
		delete this.urlParams[getParamName];
		return this;
	};
	this.removePaging = function(){
		return this.removeParam('page');
	};
	this.getParam = function(getParamName){
		return this.urlParams[getParamName];
	};
	this.hasParam = function(getParamName){
		return (typeof this.urlParams[getParamName] !== 'undefined');
	};
	this.getHash = function(){
		return this.hash;
	};
	this.isHashSet = function(){
		return this.hash !== '';
	};

	construct(this, url || false);
};

SpoPaging = function(spoUrlObj){
	function construct(me, spoUrlObj){
		me.currentPage = 0;
		me.pageCount   = 0;
		me.pageParam   = 'page';
		me.spoUrlObj = spoUrlObj;
	}

	this.urlToNextPage = function(){
		return this.urlToPage(Math.min(this.currentPage+1, this.pageCount));
	};
	this.urlToPreviousPage = function(){
		return this.urlToPage(Math.max(this.currentPage-1, 1));
	};
	this.urlToFirstPage = function(){
		return this.urlToPage(1);
	};
	this.urlToLastPage = function(){
		return this.urlToPage(this.pageCount);
	};
	this.urlToPage = function(page){
		var p = {};
		p[this.pageParam] = page;
		return this.spoUrlObj.toString(p);
	};

	this.isCurrentPageIsLast = function(){
		return this.currentPage >= this.pageCount;
	};

	this.isCurrentPageIsFirst = function(){
		return this.currentPage <= 0;
	};

	if(typeof spoUrl === 'undefined'){
		throw 'spoUrl varibal is undefined';
	}

	construct(this, spoUrlObj);
};

SpoAjax = function(options){
	options = options || {};

	var successCallback = options.successCallback || function(){},
		failureCallback = options.failureCallback || function(){},
		alwaysCallback  = options.alwaysCallback  || function(){};

	if(!options.preventDefaultFailureCallback){
		var failureCb = failureCallback;

		failureCallback = function(msg, response){
			$errPlace = $('.error-place');

			if($errPlace.length > 0){
				$errPlace.html(msg);
			}else{
				spoUtil.showModal({content: msg});
			}

			failureCb(msg, response);
		}
	}

	options.sendDataAsJson = options.sendDataAsJson === true;

	return $.ajax({
			url:  options.url,
			data: (options.data && options.sendDataAsJson) ? {data: JSON.stringify(options.data)} : options.data,
			method: options.data ? 'post' : 'get',
			dataType: 'json',
			optionsType: 'json'
		})
		.done(function(data){
			if(data.success){
				successCallback(data.data || {});
			}else{
				failureCallback($.isArray(data.errors) ? data.errors.join(',') : '', data);
			}
		})
		.fail(function(data){
			failureCallback(data.responseText, {});
		})
		.always(function(){
			alwaysCallback();
		});
};

SpoUtil = function(){
	function construct(me){}

	this.isAnyModalVisible = function(){
		return $('.modal:visible').length > 0;
	};

	this.showModal = function(opts){
		opts = opts || {};

		var content = opts.content || '',
			title   = opts.title   || 'Уведомление',
			id      = 'wind-' + Math.floor((Math.random() * 10000) + 1),
			markup  = [
			'<div id="', id, '" class="modal fade dialog-form-container in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false" style="display: hidden;">',
				'<div class="modal-dialog">',
					'<div class="modal-content">',
						'<div class="modal-header">',
							'<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>',
							'<h4 class="modal-title" id="myModalLabel">', title, '</h4>',
						'</div>',
						'<div class="modal-body">',
							'<form>',
								content,
							'</form>',
							'</div>',
								'<div class="modal-footer">',
									'<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>',
								'</div>',
							'</div>',
						'</div>',
					'</div>',
				'</div>',
			'</div>'
			].join('');

		$('body').append(markup);
		$('#' + id)
			.modal()
			.on('hidden.bs.modal', function(){
				$(this).remove();
			})
			.modal('show');

	};

	construct(this);
};

SpoForm = function($form, opts){
	function construct(me, $form, opts){
		me.onErrorShow = opts.onErrorShow || function(){};
		me.$form = $form;
	}

	this.showValidationErrors = function(errorList){
		var i,
			$fld,
			$errPlace,
			$form = this.$form,
			fldName,
			fldFound,
			err,
			commonErrors = [];

		for(i=0; i<errorList.length; i++){
			err = errorList[i]['message'] ? errorList[i] : {message: errorList[i]};
			fldFound = false;

			if(err['entity'] && err['property']){
				fldName  = err['entity'] + '[' + err['property'] + ']';
				$fld     = $form.find('[name="' + fldName + '"]');
				fldFound = $fld.length > 0;
			}

			if(!fldFound && err['property']){
				fldName  = err['property'];
				$fld     = $form.find('[name="' + fldName + '"]');
				fldFound = $fld.length > 0;
			}

			if(fldFound){
				$errPlace = $fld.parents('.form-group').addClass('has-error').find('.error-list');
				if($errPlace.length > 0){
					$errPlace.html(err.message);
					this.onErrorShow($fld, err.message, fldName);
					continue;
				}
			}

			commonErrors.push(err.message);
		}

		if(commonErrors.length > 0){
			$errPlace = $form.find('.error-place');

			if($errPlace.length > 0){
				$errPlace.html(commonErrors.join('<br>'));
			}else{
				alert(commonErrors.join(','));
			}
		}
	};

	opts = opts || {};
	construct(this, $form, opts);
};


spoUtil   = new SpoUtil();
spoUrl    = new SpoUrl();
spoPaging = new SpoPaging(spoUrl);