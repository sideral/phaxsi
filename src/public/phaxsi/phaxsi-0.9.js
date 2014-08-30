Phaxsi = {};

Phaxsi.Module = {};

Phaxsi.Util = {
	url: function(path){
		return Phaxsi.path.base+path;
	},
	localizedUrl: function(path){
		return Phaxsi.path.local+path;
	},
	publicUrl: function(path){
		return Phaxsi.path['public'] +path;
	},
	trim: function(str){
		return str.replace(/^\s+|\s+$/g, '') ;
	},
	jsonToUrl: function(json){
		var result = [];
		for(var x in json){
			result[result.length] = x + "=" + json[x];
		}
		return result.join("&");
	}
};

Phaxsi.Array = {
	search: function(value, array){
		var index = -1;
		for(var i=0; i < array.length; i++){
			if(array[i]==value){
				index = i;
				break;
			}
		}
		return index;
	},

	is: function(array){
		return !!(array && array.constructor == Array);
	},

	flip: function(array){
		var flipped = {};
		for(var i=0; i < array.length; i++){
			flipped[array[i]] = i;
		}
		return flipped;
	}, 

	diffKey: function(a, b){
		var diff = {};
		for(var x in a){
			if(b[x]===undefined){
				diff[x] = a[x];
			}
		}
		return diff;
	},
	
	diff: function(a, b){
		var diff = {};
		for(var x in a){
			if(Phaxsi.Array.search(x, b)==-1){
				diff[x] = a[x];
			}
		}
		return diff;
	},
	
	clean: function(actual, deleteValue){
	  var newArray = new Array();
	  for(var i = 0; i< actual.length; i++){
		  if (actual[i] != deleteValue){
			newArray.push(actual[i]);
		}
	  }
	  return newArray;
	},
	
	unique: function(inputArr){
		//From phpjs.org

		var key = '',
			tmp_arr2 = [],
			val = '';

		var __array_search = function (needle, haystack) {
			var fkey = '';
			for (fkey in haystack) {
				if (haystack.hasOwnProperty(fkey)) {
					if ((haystack[fkey] + '') === (needle + '')) {
						return fkey;
					}
				}
			}
			return false;
		};

		for (key in inputArr) {
			if (inputArr.hasOwnProperty(key)) {
				val = inputArr[key];
				if (false === __array_search(val, tmp_arr2)) {
					tmp_arr2[key] = val;
				}
			}
		}

		return tmp_arr2;

	}
}

Phaxsi.Validator = {}

Phaxsi.Validator.List = {};
Phaxsi.Validator.Current = null;
Phaxsi.Validator.DefaultErrorMessages = {};

Phaxsi.Validator.Manager = function(id, error){
	this.init(id,error);
}

Phaxsi.Validator.Manager.prototype = {
	validators: null,
	id: "",
	form: null,

	init:function(id){
		this.id = id;
		this.form = document.getElementById(this.id);
		this.validators = new Object();
	},

	attachToSubmit: function(){
		Phaxsi.Event.addEvent(this.form, 'submit', this.validate.createDelegate(this));
	},
	
	validate: function(e){
		
		var values = [];
		var uriString = Phaxsi.serialize(this.form);
		var uriParts = uriString.split('&');
		
		for(var i=0; i < uriParts.length; i++){
			var pair = uriParts[i].split('=');
			var name = decodeURIComponent(pair[0]);
			var match = name.match(/(.+?)\[(.+?)\]$/);
			if(match){
				if(values[match[1]]===undefined)
					values[match[1]] = [];
				values[match[1]][match[2]] = decodeURIComponent(pair[1]);
			}
			else{
				values[name] = decodeURIComponent(pair[1]);
			}
		}

		var error = new Phaxsi.Validator.Error();
		
		var valid = true;
		for(var name in this.validators){
			var message  = this.validators[name].validate(values[name]);
			if(message != ""){
				error.show(name, message, this.id);
				valid = false;
			}
			else{
				error.hide(name, this.id);
			}
		}
		
		if(!valid){
			e.preventDefault();
		}
		
	},

	addValidator: function(name, options, messages, config){
		this.validators[name] = new Phaxsi.Validator.Instance(options, messages, config);
	}
}

Phaxsi.Validator.Error = function(){
	return {
		show: function(inputName, message, formId){
			var element = document.getElementById("error-message-" + inputName);
			if(!element)return;
			element.innerHTML = message;
			element.style.display = "";
		},

		hide: function(inputName, formId){
			var element = document.getElementById("error-message-" + inputName);
			if(!element)return;
			element.style.display = "none";
		}
	}
}

Phaxsi.Validator.Instance = function(options, messages, config){

	return {
		validate: function(value){

			if(config && config.array){
				if(value && !Phaxsi.Array.is(value)){
					return "generic";
				}
				return this.validateArray(value);
			}

			if(config && config.file){
				return this.validateFile(value);
			}

			if(config && config.trim){
				value = Phaxsi.Util.trim(value);
			}

			if(config.callback){
				var func = eval(config.callback);
				if(func(value) == false){
					return this.getErrorMessage('callback');
				}
			}

			var isNull = Phaxsi.Array.search(value, options['null_values']) != -1;
			
			if(options['required'] && isNull){
				return this.getErrorMessage('required');
			}

			if(isNull){
				return "";
			}

			if(options['expression'] && !value.match(new RegExp(options['expression']))){
				return this.getErrorMessage('expression');
			}

			if(options['in'] && Phaxsi.Array.search(value, options['in'])==-1){
				return this.getErrorMessage('in');
			}

			if(options['max_length'] && value.length > options['max_length']){
				return this.getErrorMessage('max_length');
			}

			if(options['min_length'] && value.length < options['min_length']){
				return this.getErrorMessage('min_length');
			}

			var floatValue = parseFloat(value);
			
			if(options['max_value'] && floatValue > options['max_value']){
				return this.getErrorMessage('max_value');
			}

			if(options['min_value'] && floatValue < options['min_value']){
				return this.getErrorMessage('min_value');
			}

			return "";
		},

		validateArray: function(values){

			if(values===undefined) values = [];
			
			var nullCount = 0;
			
			for(var i=0; i < values.length; i++){
				if(Phaxsi.Array.search(values[i], options['null_values']) != -1){
					nullCount++;
				}
			}

			if(options['required'] && (nullCount !== 0 || values.length === 0)){
				return this.getErrorMessage('required');
			}
			
			if(options['array_max_count'] && (values.length - nullCount) > options['array_max_count']){
				return this.getErrorMessage('array_max_count');
			}

			if(options['array_min_count'] && (values.length - nullCount) < options['array_min_count']){
				return this.getErrorMessage('array_min_count');
			}
			
			if(options['array_required_keys']){
				for(var i=0; i < options['array_required_keys'].length; i++){
					if(values[options['array_required_keys'][i]] === undefined){
						return this.getErrorMessage('array_required_keys');
					}
				}
			}
			
			if(options['array_required_values']){
				for(var i=0; i < options['array_required_values'].length; i++){
					if(Phaxsi.Array.search(options['array_required_values'][i], values)){
						return this.getErrorMessage('array_required_values');
					}
				}
			}

			if(options['array_count'] && values.length != options['array_count']){
				return this.getErrorMessage('array_count');
			}

			if(options['array_allow_duplicates']===false){
				var cleaned = Phaxsi.Array.clean(values, "");
				var unique = Phaxsi.Array.unique(cleaned);
				if(unique.length != cleaned.length){
					return this.getErrorMessage('array_allow_duplicates');
				}
			}

			if(values.length == nullCount){
				return "";
			}
			
			config.array = false;
			for(var i=0; i < values.length; i++){
				var message = this.validate(values[i]);
				if(message != ""){
					config.array = true;
					return message;
				}
			}

			config.array = true;
			return "";

		},

		validateFile: function(value){
			if(options['required'] && !value){
				return this.getErrorMessage('required');
			}
			
			if(options['extension'] && value){
				var extPattern = options['extension'].join('|');
				if(!value.match(new RegExp(".+\\."+extPattern+"$", "i"))){
					return this.getErrorMessage('extension');
				}
			}
			
			return "";
		},

		getErrorMessage: function(key){
			if(messages[key]){
				return messages[key];
			}
			if(Phaxsi.Validator.DefaultErrorMessages[key]){
				return Phaxsi.Validator.DefaultErrorMessages[key];
			}
			return Phaxsi.Validator.DefaultErrorMessages['generic'];
		}
	}
}



// Adapted from addEvent by Dean Edwards
// written by Dean Edwards, 2005
// with input from Tino Zijdel, Matthias Miller, Diego Perini

// http://dean.edwards.name/weblog/2005/10/add-event/

Phaxsi.Event = {
	
	// a counter used to create unique IDs
	guid: 1,
	
	addEvent: function(element, type, handler) {
		if (element.addEventListener) {
			element.addEventListener(type, handler, false);
		} else {
			// assign each event handler a unique ID
			if (!handler.$$guid) handler.$$guid = this.guid++;
			// create a hash table of event types for the element
			if (!element.events) element.events = {};
			// create a hash table of event handlers for each element/event pair
			var handlers = element.events[type];
			if (!handlers) {
				handlers = element.events[type] = {};
				// store the existing event handler (if there is one)
				if (element["on" + type]) {
					handlers[0] = element["on" + type];
				}
			}
			// store the event handler in the hash table
			handlers[handler.$$guid] = handler;
			// assign a global event handler to do all the work
			element["on" + type] = this.handleEvent;
		}
	},

	removeEvent: function(element, type, handler) {
		if (element.removeEventListener) {
			element.removeEventListener(type, handler, false);
		} else {
			// delete the event handler from the hash table
			if (element.events && element.events[type]) {
				delete element.events[type][handler.$$guid];
			}
		}
	},

	handleEvent: function(event) {
		var returnValue = true;
		// grab the event object (IE uses a global event object)
		event = event || this.fixEvent(((this.ownerDocument || this.document || this).parentWindow || window).event);
		// get a reference to the hash table of event handlers
		var handlers = this.events[event.type];
		// execute each event handler
		for (var i in handlers) {
			this.$$handleEvent = handlers[i];
			if (this.$$handleEvent(event) === false) {
				returnValue = false;
			}
		}
		return returnValue;
	},

	fixEvent: function(event) {
		// add W3C standard event methods
		event.preventDefault = function() {
			this.returnValue = false;
		};
		event.stopPropagation = function() {
			this.cancelBubble = true;
		};
		return event;
	},

	//http://dean.edwards.name/weblog/2006/06/again/#comment367184
	onDOMReady: function(callback){
		
		function init() {
		  if (arguments.callee.done) return;
		  arguments.callee.done = true;
		  callback();
		}

		if (document.addEventListener) {
		  document.addEventListener('DOMContentLoaded', init, false);
		}
		
		(function() {
		  /*@cc_on
		  try {
			document.body.doScroll('up');
			return init();
		  } catch(e) {}
		  /*@if (false) @*/
		  if (/loaded|complete/.test(document.readyState)) return init();
		  /*@end @*/
		  if (!init.done) setTimeout(arguments.callee, 30);
		})();

		if (window.addEventListener) {
		  window.addEventListener('load', init, false);
		} else if (window.attachEvent) {
		  window.attachEvent('onload', init);
		}
		
	}
	
	
}

//Adapted from YUI 3.4 BSD License. http://yuilibrary.com/license/

Phaxsi.serialize = function(element) {
	var data = [],
	item = 0,
	id = element.id,
	input, form, input_name, input_value, is_disabled, i, il, j, jl, options;
	
	form = document.getElementById(id);
	
	// Iterate over the form elements collection to construct the
	// label-value pairs.
	for (i = 0, il = form.elements.length; i < il; ++i) {
		input = form.elements[i];
		is_disabled = input.disabled;
		input_name = input.name;
		
		if (input_name && !is_disabled) {
			input_name = encodeURIComponent(input_name) + '=';
			input_value = encodeURIComponent(input.value);
			
			switch (input.type) {
				// Safari, Opera, FF all default options.value from .text if
				// value attribute not specified in markup
				case 'select-one':
					if (input.selectedIndex > -1) {
						options = input.options[input.selectedIndex];
						data[item++] = input_name + encodeURIComponent(options.attributes.value && options.attributes.value.specified ? options.value : options.text);
					}
					break;
				case 'select-multiple':
					if (input.selectedIndex > -1) {
						for (j = input.selectedIndex, jl = input.options.length; j < jl; ++j) {
							options = input.options[j];
							if (options.selected) {
								data[item++] = input_name + encodeURIComponent(options.attributes.value && options.attributes.value.specified ? options.value : options.text);
							}
						}
					}
					break;
				case 'radio':
				case 'checkbox':
					if (input.checked) {
						data[item++] = input_name + input_value;
					}
					break;
				case 'file':
				// stub case as XMLHttpRequest will only send the file path as a string.
				case undefined:
				// stub case for fieldset element which returns undefined.
				case 'reset':
					// stub case for input type reset button.
				case 'button':
					// stub case for input type button elements.
					break;
				case 'submit':
				default:
					data[item++] = input_name + input_value;
			}
		}
	}
	return data.join('&');
}

//Thank to ExtJS for this
Function.prototype.createDelegate  = function(C,B,A){
	var D=this;
	return function(){
		var F=B||arguments;
		if(A===true){
			F=Array.prototype.slice.call(arguments,0);
			F=F.concat(B);
		}
		else{
			if(typeof A=="number"){
				F=Array.prototype.slice.call(arguments,0);
				var E=[A,0].concat(B);
				Array.prototype.splice.apply(F,E);
			}
		}
		return D.apply(C||window,F);
	}
}
