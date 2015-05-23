if (!('Notification' in window)) {
	window.Notification = window.notifications || window.webkitNotifications || window.oNotifications || window.msNotifications || false;
}
if (!('indexedDB' in window)) {
	window.indexedDB = window.indexedDB || window.mozIndexedDB || window.webkitIndexedDB || window.msIndexedDB || false;
}
if (!('hidden' in document)) {
	document.hidden = document.webkitHidden || document.msHidden || document.mozHidden || false;
}
if (!('visibilityState' in document)) {
	document.visibilityState = document.webkitVisibilityState || document.msVisibilityState || document.mozVisibilityState || false;
}
if (!('fullScreenElement' in document)) {
	document.fullScreenElement = document.mozFullScreenElement || document.webkitFullscreenElement || false;
}
if (!('requestAnimationFrame' in window)) {
	window.requestAnimationFrame = window.mozRequestAnimationFrame || window.webkitRequestAnimationFrame || window.msRequestAnimationFrame || false;
}
if (!('cancelFullScreen' in document)) {
	document.cancelFullScreen = document.mozCancelFullScreen || document.webkitCancelFullScreen || document.msCancelFullScreen || false;
}
if (!('requestFullScreen' in document)) {
	document.requestFullScreen = document.mozRequestFullScreen || document.webkitRequestFullScreen || false;
}
if (! ('Element' in window)) {
	/*Fix IE not allowing Element.prototype*/
	window.Element = function () {};
	Element.prototype = Object.prototype;
}
if (! ('CSS' in window)) {
	window.CSS = {};
	CSS.prototype = Object.prototype;
}
if(! ('show' in Element.prototype)) {
	Element.prototype.show = function() {
		this.setAttribute('open', '');
	};
}
if(! ('showModal' in Element.prototype)) {
	Element.prototype.showModal = function() {
		var backdrop = document.createElement('div');
		backdrop.classList.add('backdrop');
		$('dialog[open]').each(function(dialog) {
			dialog.close();
		});
		this.after(backdrop);
		this.classList.add('modal');
		this.show();
	};
}
if(! ('close' in Element.prototype)) {
	Element.prototype.close = function() {
		this.removeAttribute('open');
		this.classList.remove('modal');
		if(this.nextElementSibling.classList.contains('backdrop')) {
			this.nextElementSibling.parentElement.removeChild(this.nextElementSibling);
		}
	};
}
if (! ('HTMLimport' in Element.prototype)) {
	Element.prototype.HTMLimport = function() {
		if (supports('HTMLimports')) {
			var imported = document.querySelector(
				'link[rel=import][name="' + this.dataset.import + '"]'
			);

			if (this.hasAttribute('data-selector')) {
				this.appendChild(imported.import.querySelector(this.dataset.selector));
			} else {
				this.appendChild(imported.import.body.firstChild);
			}
		}
	};
}
if (! ('matches' in Element.prototype)) {
	/*Check if Element matches a given CSS selector*/
	if ('mozMatchesSelector' in Element.prototype) {
		Element.prototype.matches = Element.prototype.mozMatchesSelector;
	} else if ('webkitMatchesSelector' in Element.prototype) {
		Element.prototype.matches = Element.prototype.webkitMatchesSelector;
	} else if ('oMatchesSelector' in Element.prototype) {
		Element.prototype.matches = Element.prototype.oMatchesSelector;
	} else if ('msMatchesSelector' in Element.prototype) {
		Element.prototype.matches = Element.prototype.msMatchesSelector;
	} else {
		Element.prototype.matches = function(sel) {
			return ($(sel) .indexOf(this) !== -1);
		}
	}
}
if (! ('contains' in String.prototype)) {
	String.prototype.contains = function() {
		return String.prototype.indexOf.apply( this, arguments ) !== -1;
	};
}
if (! ('startsWith' in String.prototype)) {
	Object.defineProperty(String.prototype, 'startsWith', {
		enumerable: false,
		configurable: false,
		writable: false,
		value: function (searchString, position) {
			position = position || 0;
			return this.lastIndexOf(searchString, position) === position;
		}
	});
}
if (! ('endsWith' in String.prototype)) {
	Object.defineProperty(String.prototype, 'endsWith', {
		value: function (searchString, position) {
			var subjectString = this.toString();
			if (position === undefined || position > subjectString.length) {
				position = subjectString.length;
			}
			position -= searchString.length;
			var lastIndex = subjectString.indexOf(searchString, position);
			return lastIndex !== -1 && lastIndex === position;
		}
	});
}
if(! ('supports' in CSS)) {
	CSS.supports = function (prop, value) {
		var el = document.createElement('div');
		el.style = prop + ":" + value;
		return (getComputedStyle(el)[prop] === value);
	};
}
(function(root) {
	var CSS =  root.CSS;
	var InvalidCharacterError = function(message) {
		this.message = message;
	};
	InvalidCharacterError.prototype = new Error;
	InvalidCharacterError.prototype.name = 'InvalidCharacterError';
	if (! ('escape' in CSS)) {
		CSS.escape = function(value) {
			var string = String(value), length = string.length, index = -1, codeUnit, result = '', firstCodeUnit = string.charCodeAt(0);
			while (++index < length) {
				codeUnit = string.charCodeAt(index);
				if (codeUnit == 0x0000) {
					throw new InvalidCharacterError('Invalid character: the input contains U+0000.');
				}
				if (
					(codeUnit >= 0x0001 && codeUnit <= 0x001F) || codeUnit == 0x007F ||
					(index == 0 && codeUnit >= 0x0030 && codeUnit <= 0x0039) ||
					(
						index == 1 &&
						codeUnit >= 0x0030 && codeUnit <= 0x0039 &&
						firstCodeUnit == 0x002D
					)
				) {
					result += '\\' + codeUnit.toString(16) + ' ';
					continue;
				}
				if (
					codeUnit >= 0x0080 ||
					codeUnit == 0x002D ||
					codeUnit == 0x005F ||
					codeUnit >= 0x0030 && codeUnit <= 0x0039 ||
					codeUnit >= 0x0041 && codeUnit <= 0x005A ||
					codeUnit >= 0x0061 && codeUnit <= 0x007A
				) {
					result += string.charAt(index);
					continue;
				}
				result += '\\' + string.charAt(index);
			}
			return result;
		};
	}
}(typeof global !== 'undefined' ? global : this));

function supports(type) {
	/*Feature detection. Returns boolean value of suport for type*/
	/**
	* A series of tests to determine support for a given feature
	* Defaults to testing support for an element of tag (type)
	* Which works by testing if the browser considers it unknown element type
	*/
	if (typeof type !== 'string') {
		return false;
	}

	try {
		switch (type.toLowerCase()) {
			case 'queryselectorall':
				return ('querySelectorAll' in document);
				break;

			case 'svg':
				return (document.implementation.hasFeature('http://www.w3.org/TR/SVG11/feature#Shape', '1.1'));
				break;

			case 'dataset':
				return ('DOMStringMap' in window);
				break;

			case 'htmlimports':
				return ('import' in document.createElement('link'));
				break;

			case 'geolocation':
				return ('geolocation' in navigator);
				break;

			case 'connectivity':
				return ('onLine' in navigator);
				break;

			case 'visibility':
				return ('visibilityState' in document) || ('webkitVisibilityState' in document);
				break;

			case 'validity':
				return ('validity' in document.createElement('input'));
				break;

			case 'fonts':
				return ('CSSFontFaceRule' in window);
				break;

			case 'csssupports':
				return (('CSS' in window) && ('supports' in CSS));
				break;

			case 'listeners':
				return ('addEventListener' in window);
				break;

			case 'animations':
				return ((supports('csssupports')
					&& (CSS.supports('animation', 'name')
					|| CSS.supports('-webkit-animation', 'name'))
				) ||
					'animation' in document.body.style ||
					'webkitAnimation' in document.body.style
				);
				break;

			case 'transitions':
				return ((supports('csssupports')
				&& (CSS.supports('transition', 'none') ||
					CSS.supports('-webkit-transition', 'none'))
				) ||
					'transition' in document.body.style ||
					'webkitTransition' in documnt.body.style
				);
				break;

			case 'cssgradients':
				return (supports('csssupports')
					&& CSS.supports('background-image', 'linear-gradient(red,red)'))
					|| (function() {
					var el = document.createElement('a');
					el.style.backgroundImage = 'linear-gradient(red, red)';
					return (!!el.style.backgroundImage);
				})();
				break;

			case 'notifications':
				return ('notifications' in window || 'Notification' in window);
				break;

			case 'applicationcache':
				return ('applicationCache' in window);
				break;

			case 'indexeddb':
				return ('indexedDB' in window);
				break;

			case 'fullscreen':
				return ('cancelFullScreen' in document);
				break;

			case 'workers':
				return ('Worker' in window);
				break;

			case 'promises':
				return ('Promise' in window);
				break;

			case 'cssmatches':
				return ('sessionStorage' in window && sessionStorage.hasOwnProperty('MatchesPre')) ||
				[':matches', ':any', ':-moz-any', ':-webkit-any'].some(function (pre) {
					try {
						if (document.querySelector(pre + '(body)') === document.body) {
							sessionStorage.setItem('MatchesPre', pre);
							return true;
						} else {
							return false;
						};
					} catch (e) {
						return false;
					}
				});
				break;

			case 'ajax':
				return ('XMLHttpRequest' in window);
				break;

			case 'cssvars':
				return (supports('csssupports') && CSS.supports('--x','x'));
				break;

			case 'formdata':
				return ('FormData' in window);
				break;

			case 'classlist':
				return ('DOMTokenList' in window);
				break;

			case 'localstorage':
				return ('localStorage' in window);
				break;

			case 'sessionstorage':
				return ('sessionStorage' in window);
				break;

			default:
				return (document.createElement(type.toLowerCase()) .toString() !== document.createElement('DNE') .toString());
		}
	} catch(e) {
		return false;
	}
}
"use strict";
var body = document.body,
	html = document.documentElement,
	prefixes = ['', '-moz-', '-webkit-', '-o-', '-ms-'];
DOMTokenList.prototype.pick = function(cname1, cname2, condition) {
	(condition) ? this.add(cname1) : this.add(cname2);
};
DOMTokenList.prototype.swap = function(cname1, cname2) {
	if(this.contains(cname1)) {
		this.remove(cname1);
		this.add(cname2);
	} else {
		this.remove(cname2);
		this.add(cname1);
	}
};
Array.prototype.unique = function() {
	return this.filter(
		function(val, i, arr) {
			return (i <= arr.indexOf(val));
		}
	);
};
Array.prototype.end = function() {
	return this[this.length - 1];
};
HTMLCollection.prototype.indexOf = Array.prototype.indexOf;
function selection() {
	var selected = getSelection();
	//this.target = selected.focusNode;
	this.start = selected.anchorOffset;
	this.end = selected.focusOffset;
	this.length = this.end - this.start;
	this.parent = selected.anchorNode;
	this.before = this.parent.textContent.substring(0, this.start);
	this.after = this.parent.textContent.substring(this.end);
	this.text = selected.focusNode.textContent.substring(this.start, this.end);
}
selection.prototype.constructor = selection;
selection.prototype.replace = function(rep) {
	this.parent.innerHTML = this.before + rep + this.after;
};
RegExp.prototype.escape = function() {
	return this.source.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
};

Object.prototype.isaN = function () {
	return parseFloat(this) == this;
};
Object.prototype.camelCase = function () {
	return this.toLowerCase() .replace(/\ /g, '-') .replace(/-(.)/g, function (match, group1) {
		return group1.toUpperCase();
	});
};
Element.prototype.delete = function() {
	this.parentElement.removeChild(this);
};
Element.prototype.after = function () {
	for(var i = 0; i < arguments.length; i++) {
		(typeof arguments[i] === 'string')
			? this.insertAdjacentHTML('afterend', arguments[i])
			: this.parentElement.insertBefore(arguments[i], this.nextSibling);
	}
	return this;
};
Element.prototype.before = function() {
	for(var i = 0; i < arguments.length; i++) {
		(typeof arguments[i] === 'string')
			? this.insertAdjacentHTML('beforebegin', arguments[i])
			: this.parentElement.insertBefore(arguments[i], this);
	}
	return this;
};
Element.prototype.prepend = function() {
	for(var i = 0; i < arguments.length; i++) {
		(typeof arguments[i] === 'string')
			? this.insertAdjacentHTML('afterbegin', arguments[i])
			: this.insertBefore(arguments[i], this.firstChild);
	}
	return this;
};
Element.prototype.append = function() {
	for(var i = 0; i < arguments.length; i++) {
		(typeof arguments[i] === 'string')
			? this.insertAdjacentHTML('beforeend', arguments[i])
			: this.appendChild(arguments[i]);
	}
	return this;
};
Element.prototype.clone = function() {
	return this.cloneNode(true);
};
Element.prototype.next = function () {
	return this.nextSibling;
};
Element.prototype.prev = function () {
	return this.previousSibling;
};
Element.prototype.html = function(html) {
	this.innerHTML = html;
	return this;
};
Element.prototype.ancestor = function (sel) {
	if(this.parentElement.matches(sel)) {
		return this.parentElement;
	} else if(this === document.body) {
		return false;
	} else {
		return this.parentElement.ancestor(sel);
	}
};
Element.prototype.data = function(set, value) {
	var val = null;
	if(supports('dataset')) {
		(typeof value !== 'undefined') ? this.dataset[set.camelCase()] = value : val = this.dataset[set.camelCase()];
	} else {
		(typeof value !== 'undefined') ? this.setAttribute('data-' + set, value): val = this.getAttribute('data-' + set);
	}
	return val;
};
Element.prototype.attr = function(attr, val) {
	switch(typeof val) {
		case 'string':
			this.setAttribute(attr, val);
			return this;
			break;

		case 'boolean':
			(val) ? this.setAttribute(attr, '') : this.removeAttribute(attr);
			return this;
			break;

		default:
			return this.getAttribute(attr);
	}
};
Element.prototype.uniqueSelector = function () {
	if (this.nodeType !== 1) {
		return null;
	}
	var path = [],
	current = this;
	while (current !== document.documentElement) {
		if (current === document.body) {
			path.push('body');
			break;
		} else if(current.hasAttribute('id')) {
			path.push('#' + current.id);
			break;
		} else {
			path.push(current.tagName.toLowerCase() + ':nth-child(' + (current.parentElement.children.indexOf(current) + 1).toString() + ')');
			current = current.parentElement;
		}
	}
	return path.reverse() .join(' > ');
};
Element.prototype.ajax = function(args) {
	ajax(args).then(
		this.html.bind(this),
		console.error
	);
	return this;
};
Element.prototype.wordCount = function() {
	return this.textContent.split(' ').length;
};
function notify(options) {
	/*Creates a notification, with alert fallback*/
	var notification;
	if (typeof options === 'string') {
		options = {
			body: options
		};
	}
	if(!options.icon) {
		options.icon = 'images/icons/info.png';
	}
	if ('Notification' in window) {
		if (Notification.permission.toLowerCase() === 'default') {
			Notification.requestPermission(function () {
				(Notification.permission.toLowerCase() === 'granted')
					? notification = notify(options)
					: alert(options.title || document.title + '\n' + options.body);
			});
		}
		notification = new Notification(options.title || document.title, options);
	} else if ('notifications' in window) {
		if (window.notifications.checkPermission != 1) {
			window.notifications.requestPermission();
		}
		notification = window.notifications.createNotification(options.icon, options.title || document.title, options.body) .show();
	} else {
		alert(options.title || document.title + '\n' + options.body);
	}
	if (!!notification) {
		if ('onclick' in options) {
			notification.onclick = options.onclick;
		}
		if ('onshow' in options) {
			notification.onshow = options.onshow;
		}
		if ('onclose' in options) {
			notification.onclose = options.onclose;
		}
		if ('onerror' in options) {
			notification.onerror = options.onerror;
		} else {
			notification.onerror = console.error;
		}
		return notification;
	}
}
/*AppCache updater*/
/*$(window) .load(function (e) { *//*Check for appCache updates if there is a manifest set*/
window.addEventListener('load', function () {
	/**
	*TODO Should I check for manifest on anything but <html>?
	*		Could use (!!$('[manifest]').length) instead.
	*/
	if (('applicationCache' in window) && ('manifest' in document.documentElement)) {
		var appCache = window.applicationCache;
		$(appCache) .updateready(function (e) {
			if (appCache.status == appCache.UPDATEREADY) {
				appCache.update() && appCache.swapCache();
				if (confirm('A new version of this site is available. Load it?')) {
					window.location.reload();
				}
			}
		});
	}
});
function getLocation(options) {
	/*https://developer.mozilla.org/en-US/docs/Web/API/Geolocation.getCurrentPosition*/
	if (typeof options === 'undefined') {
		options = {};
	}
	return new Promise(function(success, fail) {
		if (!('geolocation' in navigator)) {
			fail('Your browser does not support GeoLocation');
		}
		navigator.geolocation.getCurrentPosition(success, fail, options);
	});
}
Element.prototype.query = function(query) {
	var els = [];
	if(this.matches(query)) {
		els.push(this);
	}
	this.querySelectorAll(query).forEach(function(el) {
		els.push(el);
	});
	return els;
};
Object.prototype.keys = function() {
	return Object.keys(this) || [];
};
function ajax(data) {
	if ((typeof data.type !== 'undefined' && data.type.toLowerCase() === 'get') && (typeof data.request === 'string')) {
		data.url += '?' + data.request;
	}
	if(typeof data.form !== 'undefined') {
		if(typeof data.form === 'string') {
			data.form = document.forms[data.form];
		}
		data.request = new FormData(data.form);
		data.request.append('form', data.form.name);
		data.request.append('nonce', sessionStorage.getItem('nonce'));
		data.form.querySelectorAll('[data-input-name]').forEach(function(input) {
			data.request.append(input.data('input-name'), input.innerHTML);
		});
	}
	return new Promise(function (success, fail) {
		var resp;
		/*https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Promise*/
		if(('cache' in data) && cache.has(data.cache)) {
			if(typeof data.history === 'string') {
				history.pushState({}, document.title, data.history);
			}
			success(cache.get(data.cache));
		} else if(typeof navigator.onLine !== 'boolean' || navigator.onLine) {
			var req = new XMLHttpRequest(),
				progress = document.createElement('progress');
			if (("withCredentials" in req) && ('withCredentials' in data)) {
				req.withCredentials = data.withCredentials;

			}
			if(typeof data.contentType !== 'string') {
				data.contentType = 'application/x-www-form-urlencoded';
			}
			document.body.appendChild(progress);
			req.open(
				data.type || 'POST',
				data.url || document.baseURI,
				data.async || true
			);
			if(typeof data.request === 'string') {
				req.setRequestHeader('Content-type', data.contentType);
			}
			req.setRequestHeader('Request-Type', 'AJAX');
			req.addEventListener('progress', function(event) {
				if(event.lengthComputable) {
					progress.value = event.loaded / event.total;
				}
			});
			req.addEventListener('load', function (event) {
				switch(req.getResponseHeader('Content-Type')) {
					case 'application/json':
						resp = JSON.parse(req.response.trim());
						break;

					case 'text/xml':
						resp = new DOMParser().parseFromString(req.response.trim(), "text/xml");
						break;

					case 'text/html':
						resp = document.createDocumentFragment();
						resp.innerHTML = req.response.trim();
						break;

					default:
						resp = req.response.trim();
				}
				progress.parentElement.removeChild(progress);
				if(req.status == 200) {
					if(data.cache) {
						cache.set(data.cache, req.response.trim());
					}
					if(typeof data.history === 'string') {
						history.pushState({}, document.title, data.history);
					}
					success(resp);
				} else {
					fail(Error(req.statusText));
				}
			});
			req.addEventListener('error', function () {
				fail(Error('Network Error'));
				progress.parentElement.removeChild(progress);
			});
			if(typeof data.request !== 'undefined') {
				req.send(data.request);
			} else {
				req.send();
			}
		} else {
			notify({
				title: 'Network:',
				body: 'offline',
				icon: 'images/icons/network-server.png'
			});
			fail('No Internet Connection');
		}
	});
}
function cache() {
	return this;
}
cache.prototype.constructor = cache;
cache.prototype.has = function(key) {
	return localStorage.keys().indexOf(('cache ' + key).camelCase()) !== -1;
};
cache.prototype.get = function(key) {
	return localStorage.getItem(('cache ' + key).camelCase()) || false;
};
cache.prototype.set = function(key, value) {
	localStorage.setItem(('cache ' + key).camelCase(), value);
	return this;
};
cache.prototype.unset = function(key) {
	localStorage.removeItem(('cache ' + key).camelCase());
	return this;
};
cache.prototype.keys = function() {
	return localStorage.keys().filter(function(key) {
		return /^cache/.test(key);
	});
};
cache.prototype.each = function(callback) {
	return this.keys().forEach(callback.bind(this));
};
cache.prototype.clear = function() {
	this.each(function(key) {
		localStorage.removeItem(key);
	});
	return this;
};
Object.prototype.isArray  = false;
Object.prototype.isString = false;
Object.prototype.isNumber = false;
Array.prototype.isArray   = true;
String.prototype.isString = true;
Number.prototype.isNumber = true;
window.addEventListener('popstate', function() {
	ajax({
		url: location.pathname,
		type: 'GET'
	}).then(
		handleJSON,
		console.error
	);
});
/*======================================================zQ Functions=========================================================*/
Object.prototype.isZQ = false;
zQ.prototype.isZQ = true;
/*Add Array prototypes to NodeList*/
['forEach', 'indexOf', 'some', 'every', 'map', 'filter'].forEach(function(feat) {
	NodeList.prototype[feat] = Array.prototype[feat];
});
function $(q) {
	if(typeof q === 'undefined') {
		q = document.documentElement;
	} else if(q.isZQ) {
		return q;
	}
	return new zQ(q);
}
zQ.prototype.constructor = zQ;
function zQ(q) {
	if(typeof q === 'undefined') {
		q = document.documentElement;
	}
	this.query = q;
	try {
		switch(typeof this.query) {
			case 'string':
				 this.results = document.querySelectorAll(this.query);
				break;

			default:
				this.results = [this.query];
		}
	} catch (error) {
		console.error(error, this);
		console.error('No results for ' + this.query);
	}
	this.length = this.results.length;
	this.found = (!!this.results.length);
	this.filters = [];
	return this;
}
zQ.prototype.get = function(n) {
	return this.results.item(n);
};
zQ.prototype.each = function(callback) {
	if(this.found) {
		this.results.forEach(callback);
	}
	return this;
};
zQ.prototype.toArray = function() {
	if(!this.results.isArray) {
		var temp = [];
		this.each(function(node) {
			temp.push(node);
		});
		this.results = temp;
	}
	return this;
};
zQ.prototype.indexOf = function(i) {
	return this.results.indexOf(i);
};
zQ.prototype.some = function(callback) {
	return this.results.some(callback);
};
zQ.prototype.every = function(callback) {
	return this.results.every(callback);
};
zQ.prototype.filter = function(callback) {
	this.filters.push(callback.toString());
	this.results = this.results.filter(callback);
	this.length = this.results.length;
	return this;
};
zQ.prototype.map = function(callback) {
	return this.results.map(callback);
};
zQ.prototype.addClass = function(cname) {
	this.each(function(el) {
		el.classList.add(cname);
	});
	return this;
};
zQ.prototype.removeClass = function(cname) {
	this.each(function(el) {
		el.classList.remove(cname);
	});
	return this;
};
zQ.prototype.hasClass = function(cname) {
	return this.some(function(el) {
		return el.classList.contains(cname);
	});
};
zQ.prototype.toggleClass = function(cname, condition) {
	if(typeof condition ==='undefined') {
		this.each(function(el) {
			el.classList.toggle(cname);
		});
	} else {
		this.each(function(el) {
			el.classList.toggle(cname, condition);
		});
	}
	return this;
};
zQ.prototype.swapClass = function(cname1, cname2) {
	this.each(function(el) {
		el.classList.swap(cname1, cname2);
	});
	return this;
};
zQ.prototype.pickClass = function(cname1, cname2, condition) {
	(condition) ? this.addClass(cname1) : this.addClass(cname2);
	return this;
};
zQ.prototype.delete = function() {
	this.each(function(el) {
		el.parentElement.removeChild(el);
	});
};
zQ.prototype.hasAttribute = function(attr) {
	return this.some(function(el) {
		return el.hasAttribute(attr);
	});
};
zQ.prototype.attr = function(attr, val) {
	if(typeof val == 'undefined' || val === true) {
		val = '';
	}
	if(val === false) {
		this.each(function(el) {
			el.removeAttribute(attr);
		});
	} else {
		this.each(function(el) {
			el.setAttribute(attr, val);
		});
	}
	return this;

};
zQ.prototype.pause = function() {
	this.each(function(media) {
		media.pause();
	});
	return this;
};
/*======================================================Listener Functions=========================================================*/

zQ.prototype.on = function (event, callback) {
	this.each(function (e) {
		(html.addEventListener) ? e.addEventListener(event, callback, true)  : e['on' + event] = callback;
	});
	return this;
};
/*Listeners per event type*/
[
	'click',
	'dblclick',
	'contextmenu',
	'keypress',
	'keyup',
	'keydown',
	'mouseenter',
	'mouseleave',
	'mouseover',
	'mouseout',
	'mousemove',
	'mousedown',
	'mouseup',
	'input',
	'change',
	'submit',
	'reset',
	'invalid',
	'select',
	'focus',
	'blur',
	'resize',
	'updateready',
	'DOMContentLoaded',
	'load',
	'unload',
	'beforeunload',
	'abort',
	'error',
	'scroll',
	'drag',
	'offline',
	'online',
	'visibilitychange',
	'popstate',
	'pagehide'
].forEach(function(event) {
	zQ.prototype[event] = function(callback) {
		return this.on(event, callback);
	};
});
zQ.prototype.ready = function(callback) {
	return this.on('DOMContentLoaded', callback);
};
zQ.prototype.networkChange = function (callback) {
	return this.online(callback) .offline(callback);
};
zQ.prototype.playing = function (callback) {
	this.each(function (e) {
		/*Does not work with listeners. Use onEvent by default*/
		e.onplay = callback;
	});
	return this;
};
zQ.prototype.paused = function (callback) {
	this.each(function (e) {
		e.onpause = callback;
	});
	return this;
};
zQ.prototype.visibilitychange = function (callback) {
	this.each(function (e) {
		[
			'',
			'moz',
			'webkit',
			'ms'
		].forEach(function (pre) {
			$(e) .on(pre + 'visibilitychange', callback);
		});
	});
	return this;
};
zQ.prototype.watch = function(watching, options, attributeFilter) {
	/*https://developer.mozilla.org/en-US/docs/Web/API/MutationObserver*/
	if(typeof options === 'undefined') {
		options = [];
	}
	var watcher = new MutationObserver(function(mutations) {
		mutations.forEach(function(mutation) {
			watching[mutation.type].call(mutation);
		});
	}),
	watches = {};
	Object.keys(watching).concat(options).forEach(function(event) {
		watches[event] = true;
	});
	if(typeof attributeFilter !== 'undefined' && attributeFilter.isArray) {
		watches.attributeFilter = attributeFilter;
	}
	this.each(function(el) {
		watcher.observe(el, watches);
	});
	return this;
};
/*====================================================================================================================*/
zQ.prototype.$ = function (q) {
	return $(this.query.split(',').map(function(str) {
		return q.split(',').map(function(q) {
			return str.trim() + ' ' + q.trim();
		});
	}).join(', '));
};
Object.prototype.$ = function(q) {
	if(this === document || this === window) {
		return $(q);
	}
	return $(this).$(q);
};
zQ.prototype.css = function (args) { /*Set style using CSS syntax*/
	/*var n,
		i,
		e,
		value = [
		];
	args = args.replace('; ', ';') .replace(': ', ':') .replace('float', 'cssFloat') .split(';');
	for (var i = 0; i < args.length; i++) {
		value[i] = args[i].slice(args[i].indexOf(':') + 1).trim();
		args[i] = args[i].slice(0, args[i].indexOf(':')).trim().camelCase();
	}
	for (var i = 0; i < args.length; i++) {
		this.each(function (e) {
			e.style[args[i]] = value[i];
		});
	}*/
	var style = document.styleSheets[document.styleSheets.length - 1];
	style.insertRule(this.query + '{' + args +'}', style.cssRules.length);
	return this;
};
function handleJSON(json) {
	if(typeof json === 'string') {
		json = JSON.parse(json.trim());
	} else if(typeof json !== 'object') {
		return false;
	}
	if ('remove' in json) {
		document.querySelectorAll(json.remove).forEach(function(el) {
			el.parentElement.removeChild(el);
		});
	}
	if('text' in json) {
		Object.keys(json.text).forEach(function(key) {
			document.querySelector(key).textContent = json.text[key];
		});
	}
	if('html' in json) {
		Object.keys(json.html).forEach(function(key) {
			document.querySelector(key).innerHTML = json.html[key];
		});
	}
	if('after' in json) {
		Object.keys(json.after).forEach(function(key) {
			document.querySelector(key).insertAdjacentHTML('afterend', json.after[key]);
		});
	}
	if('before' in json) {
		Object.keys(json.before).forEach(function(key) {
			document.querySelector(key).insertAdjacentHTML('beforebegin', json.before[key]);
		});
	}
	if('append' in json) {
		Object.keys(json.append).forEach(function(key) {
			document.querySelector(key).insertAdjacentHTML('beforeend', json.append[key]);
		});
	}
	if('prepend' in json) {
		Object.keys(json.prepend).forEach(function(key) {
			document.querySelector(key).insertAdjacentHTML('afterbegin', json.prepend[key]);
		});
	}
	if('addClass' in json) {
		Object.keys(json.addClass).forEach(function(selector) {
			document.querySelectorAll(selector).forEach(function(el) {
				json.addClass[selector].split(',').forEach(function(cname) {
					el.classList.add(cname);
				});
			});
		});
	}
	if('removeClass' in json) {
		Object.keys(json.removeClass).forEach(function(selector) {
			document.querySelectorAll(selector).forEach(function(el) {
				json.removeClass[selector].split(',').forEach(function(cname) {
					el.classList.remove(cname);
				});
			});
		});
	}
	if('attributes' in json) {
		Object.keys(json.attributes).forEach(function(selector) {
			document.querySelectorAll(selector).forEach(function(el) {
				Object.keys(json.attributes[selector]).forEach(function(attribute) {
					if(typeof json.attributes[selector][attribute] === 'boolean') {
						(json.attributes[selector][attribute]) ? el.setAttribute(attribute, '') : el.removeAttribute(attribute);
					}
					else {
						(attribute in el) ? el[attribute] = json.attributes[selector][attribute] : el.setAttribute(attribute, json.attributes[selector][attribute]);
					}
				});
			});
		});
	}
	if('increment' in json) {
		Object.keys(json.increment).forEach(function(selector) {
			var el = document.querySelector(selector);
			Object.keys(json.increment[selector]).forEach(function(attribute) {
				if(attribute in el) {
					el[attribute] += json.increment[selector][attribute]
				}
				else {
					el.setAttribute(attribute, parseFloat(el.getAttribute(attribute) + json.increment[selector][attribute]));
				}
			});
		})
	}
	if('stepUp' in json) {
		Object.keys(json.stepUp).forEach(function(selector) {
			document.querySelectorAll(selector).forEach(function(el) {
				el.stepUp(json.stepUp[selector]);
			});
		});
	}
	if('stepDown' in json) {
		Object.keys(json.stepDown).forEach(function(selector) {
			document.querySelectorAll(selector).forEach(function(el) {
				el.stepDown(json.stepDown[selector]);
			});
		});
	}
	if('style' in json) {
		Object.keys(json.style).forEach(function(sel) {
			document.querySelectorAll(sel).forEach(function(el) {
				Object.keys(json.style[sel]).forEach(function(prop) {
					el.style[prop.camelCase()] = json.style[sel][prop];
				});
			});
		});
	}
	if('dataset' in json) {
		Object.keys(json.dataset).forEach(function(sel) {
			document.querySelectorAll(sel).forEach(function(el) {
				Object.keys(json.dataset[sel]).forEach(function(prop) {
					el.data(prop, json.dataset[sel][prop]);
				});
			});
		});
	}
	if('sessionStorage' in json) {
		Object.keys(json.sessionStorage).forEach(function(key) {
			(json.sessionStorage[key] === '') ? sessionStorage.removeItem(key) : sessionStorage.setItem(key, json.sessionStorage[key]);
		});
	}
	if('localStorage' in json) {
		Object.keys(json.localStorage).forEach(function(key) {
			(json.localStorage[key] === '') ? sessionStorage.removeItem(key) : localStorage.setItem(key, json.localStorage[key]);
		});
	}
	if ('notify' in json) {
		notify(json.notify);
	}
	if('script' in json) {
		eval(json.script);
	}
	if('log' in json) {
		console.log(json.log);
	}
	if('table' in json) {
		('table' in console) ? console.table(json.table) : console.log(json.table);
	}
	if('dir' in json) {
		('dir' in console) ? console.dir(json.dir) : console.log(json.dir);
	}
	if('info' in json) {
		console.info(json.info);
	}
	if('warn' in json) {
		console.log(json.warn);
	}
	if('error' in json) {
		console.error(json.error);
	}
	if('scrollTo' in json) {
		document.querySelectorAll(json.scrollTo.sel).item(json.scrollTo.nth).scrollIntoView();
	}
	if('focus' in json) {
		document.querySelector(json.focus).focus();
	}
	if('select' in json) {
		document.querySelector(json.select).select();
	}
	if('reload' in json) {
		window.location.reload();
	}
	if('clear' in json) {
		document.forms[json.clear].reset();
	}
	if('open' in json) {
		var specs = [];
		json.open.specs.keys().forEach(function(spec) {
			specs.push(spec + '=' + json.open.specs[spec]);
		});
		window.open(json.open.url, '_blank', specs.join(','), json.open.replace);
	}
	if('show' in json) {
		document.querySelectorAll(json.show).forEach(function(el) {
			el.show();
		});
	}
	if('showModal' in json) {
		document.querySelector(json.showModal).showModal();
	}
	if('close' in json) {
		document.querySelectorAll(json.close).forEach(function(el) {
			el.close();
		});
	}
	if('triggerEvent' in json) {
		Object.keys(json.triggerEvent).forEach(function(selector) {
			document.querySelectorAll(selector).forEach(function(target) {
				var event = json.triggerEvent[selector].toLowerCase();
				if(event === 'click') {
					target.dispatchEvent(new MouseEvent(event));
				} else {
					target.dispatchEvent(new Event(event));
				}
			});
		});
	}
	if('serverEvent' in json) {
		var serverEvent = new EventSource(json.serverEvent);
		serverEvent.addEventListener('ping', function(event) {
			handleJSON(JSON.parse(event.data));
		});
		serverEvent.addEventListener('close', function(event) {
			serverEvent.close();
			handleJSON(JSON.parse(event.data));
		});
		serverEvent.addEventListener('error', function(error) {
			console.error(new Error(error));
			console.error(error);
		});
	}
}
"use strict";
window.addEventListener('load', function() { /*Cannot rely on $(window).load() to work, so use this instead*/
	var html = $('html'),
		body = $('body'),
		head = $('head');
		cache = new cache();
		document.documentElement.classList.swap('no-js', 'js');
	['svg', 'audio', 'video', 'picture', 'canvas', 'menuitem', 'details',
	'dialog', 'dataset', 'HTMLimports', 'classList', 'connectivity',
	'visibility','notifications', 'ApplicationCache', 'indexedDB',
	'localStorage','sessionStorage', 'CSSgradients', 'transitions',
	'animations',  'CSSvars','CSSsupports', 'CSSmatches', 'querySelectorAll',
	'workers', 'promises', 'ajax', 'FormData'].forEach(function(feat){
		document.documentElement.classList.pick(feat, 'no-' + feat, supports(feat));
	});
	document.documentElement.classList.pick('offline', 'online', (supports('connectivity') && !navigator.onLine));
	setTimeout(
		function(){
			body.results.bootstrap();
		}, 100
	);
	body.watch({
		childList: function(){
			this.addedNodes.bootstrap();
		},
		attributes: function(){
			switch(this.attributeName) {
				case 'contextmenu':
					var menu = this.target.attr('contextmenu');
					if(this.oldValue !== '') {
						$('menu#' + this.oldValue).delete();
					}
					if(menu && menu !== '') {
						if(!$('menu#'+ menu).found){
							ajax({
								url: document.baseURI,
								request: 'load_menu=' + menu.replace(/\_menu$/ ,''),
								cache: this.target.data('cache')
							}).then(
								handleJSON,
								console.error
							);
						}
					}
					break;

				case 'contextmenu':
					if(this.oldValue !== '') {
						$('menu#' + this.oldValue).delete();
					}
					break;

				case 'open':
					if(
						this.target.hasAttribute('open')
						&& (this.target.offsetTop + this.target.offsetHeight < window.scrollY)
					) {
						this.target.scrollIntoView();
					}
					break;

				case 'data-import':
					if (this.target.hasAttribute('data-import')) {
						this.target.HTMLimport();
					}
					break;

				case 'data-request':
					if(this.oldValue !== '') {
						this.target.addEventListener('click', function() {
							if(!this.data('confirm') || confirm(this.data('confirm'))){
								ajax({
									url: this.data('url')|| document.baseURI,
									request: (this.data('prompt'))
										? this.data('request') + '&prompt_value=' + encodeURIComponent(prompt(this.data('prompt')))
										: this.data('request'),
									cache: el.data('cache')
								}).then(
									handleJSON,
									console.error
								);
							}
					});
					}
					break;

				case 'data-dropzone':
					document.querySelector(this.target.data('dropzone')).DnD(this.target);
					break;

				default:
					console.error('Unhandled attribute in watch', this);
			}
		}
	}, [
		'subtree',
		'attributeOldValue'
	], [
		'contextmenu',
		'list',
		'open',
		'data-request',
		'data-dropzone',
		'data-import'
	]);
	$(window).networkChange(function() {
		$('html').toggleClass('online', navigator.onLine).toggleClass('offline', !navigator.onLine);
	}).online(function(){
		$('fieldset').each(function(fieldset){
			fieldset.removeAttribute('disabled');
		});
		notify({
			title: 'Network:',
			body: 'online',
			icon: 'images/icons/network-server.png'
		});
	}).offline(function(){
		$('fieldset').each(function(fieldset){
			fieldset.disabled = true;
		});
		notify({
			title: 'Network:',
			body: 'offline',
			icon: 'images/icons/network-server.png'
		});
	});
	if(!sessionStorage.hasOwnProperty('nonce')) {
		ajax({
			request: 'request=nonce'
		}).then(
			handleJSON,
			console.error
		);
	}
});
NodeList.prototype.bootstrap = function() {
	this.forEach(function(node){
		if(node.nodeType !== 1) {
			return this;
		}
		if(!supports('details')) {
			node.query('details > summary').forEach(function(details) {
				details.addEventListener('click', function() {
					if(this.parentElement.hasAttribute('open')) {
						this.parentElement.removeAttribute('open');
					} else {
						this.parentElement.setAttribute('open', '');
					}
				});
			});
		}
		if(supports('menuitem')) {
			node.query('[contextmenu]').forEach(function(el) {
				var menu = el.attr('contextmenu');
				if(menu && menu !== '') {
					if(!$('menu#'+ menu).found){
						ajax({
							url: document.baseURI,
							request: 'load_menu=' + menu.replace(/\_menu$/ ,''),
							cache: el.data('cache')
						}).then(
							handleJSON,
							console.error
						);
					}
				}
			});
		}
		if(supports('datalist')) {
			node.query('[list]').forEach(function(list) {
				if(!$('#' + list.getAttribute('list')).found) {
					ajax({
						request: 'datalist=' + list.getAttribute('list'),
						type: 'POST'
					}).then(
						handleJSON,
						console.error
					);
				}
			});
		}
		if(!supports('picture')) {
			node.query('picture').forEach(function(picture) {
				if('matchMedia' in window) {
					var sources = picture.querySelectorAll('source[media][srcset]');
					for(var n = 0; n < sources.length; n++) {
						if(matchMedia(sources[n].getAttribute('media')).matches) {
							picture.getElementsByTagName('img')[0].src = sources[n].getAttribute('srcset');
							break;
						}
					}
				} else {
					picture.getElementsByTagName('img')[0].src = picture.querySelector('source[media][srcset]').getAttribute('srcset');
				}
			});
		}
		node.query('[autofocus]').forEach(function(input) {
			input.focus();
		});
		node.query('script:not([src])').forEach(function(script) {
			eval(script.textContent);
		});
		$('a').filter(function(a) {return new RegExp(document.location.origin).test(a.href)})
		node.query(':not([target="_blank"]):not([download])').filter(function(a) {
			return new RegExp(document.location.origin).test(a.href);
		}).forEach(function(a) {
			a.addEventListener('click', function(event) {
				if(typeof ga === 'function') {
					ga('send', 'pageview', this.href);
				}
				event.preventDefault();
				ajax({
					url: this.href,
					type: 'GET',
					history: this.href,
					cache: this.data('cache')
				}).then(
					handleJSON,
					console.error
				);
			});
		});
		node.query('form[name]').forEach(function(form){
			form.addEventListener('submit', function(event){
				event.preventDefault();
				if(!this.data('confirm') || confirm(this.data('confirm'))) {
					ajax({
						url: this.action || document.baseURI,
						type: this.method || 'POST',
						contentType: this.enctype,
						form: this
					}).then(
						handleJSON,
						console.error
					);
				}
			});
			if(form.name === 'new_post') {
				var retain = setInterval(function(){
					ajax({
						url: document.baseURI,
						request: 'action=keep-alive'
					}).then(
						handleJSON,
						console.error
					);
				}, 60000);
				form.addEventListener('submit', function(){
					clearInterval(retain);
				});
			}
		});
		node.query('[data-show]').forEach(function(el) {
			el.addEventListener('click', function() {
				document.querySelector(this.data('show')).show();
			});
		});
		node.query('[data-show-modal]').forEach(function(el) {
			el.addEventListener('click', function() {
				document.querySelector(this.data('show-modal')).showModal();
			});
		});
		node.query('[data-import]').forEach(function(el) {
			el.HTMLimport();
		});
		node.query('[data-close]').forEach(function(el) {
			el.addEventListener('click', function() {
				document.querySelector(this.data('close')).close();
			});
		});
		node.query('[data-scroll-to]').forEach(function(el ) {
			el.addEventListener('click', function() {
				this.scrollIntoView();
			});
		});
		node.query('fieldset button[type=button].toggle').forEach(function(toggle) {
			toggle.addEventListener('click', function() {
				this.ancestor('fieldset').querySelectorAll('input[type=checkbox]').forEach(function(checkbox) {
					if(checkbox.checked) {
						checkbox.checked = false;
					} else {
						checkbox.checked = true;
					}
				});
			});
		});
		node.query('[data-must-match]').forEach(function(match) {
			match.pattern = new RegExp(document.querySelector('[name="' + match.data('must-match') + '"]').value).escape();
			document.querySelector('[name="' + match.data('must-match') + '"]').addEventListener('change', function() {
				document.querySelector('[data-must-match="' + this.name + '"]').pattern = new RegExp(this.value).escape();
			});
		});
		node.query('[data-dropzone]') .forEach(function (el) {
			document.querySelector(el.data('dropzone')).DnD(el);
		});
		node.query('input[data-equal-input]').forEach(function(input) {
			input.addEventListener('input', function() {
				document.querySelectorAll('input[data-equal-input="' + this.data('equal-input') + '"]').forEach(function(other) {
					if(other !== input) {
						other.value = input.value;
					}
				});
			});
		});
		node.query('[data-editor-command]').forEach(function(item) {
			item.addEventListener('click', function() {
				var arg = null;
				if(this.data('editor-value')) {
					arg = this.data('editor-value');
				} else if(this.data('prompt')) {
					arg = prompt(this.data('prompt'));
				} else if(this.data('selection-to')) {
					var createdEl = document.createElement(this.data('selection-to'));
					createdEl.textContent = getSelection().toString();
					arg = createdEl.outerHTML;
				}
				document.execCommand(this.data('editor-command'), null, arg);
			});
		});
		node.query('[data-link]').forEach(function(link) {
			link.addEventListener('click', function(){
				ajax({
					url: this.data('link'),
					type: 'GET',
					history: this.data('link'),
					cache: this.data('cache')
				}).then(
					handleJSON,
					console.error
				);
			});
		});
		node.query('[data-request]').forEach(function(el) {
			el.addEventListener('click', function(event) {
				event.preventDefault();
				if(!this.data('confirm') || confirm(this.data('confirm'))){
					ajax({
						url: this.data('url')|| document.baseURI,
						request: (this.data('prompt')) ? this.data('request') + '&prompt_value=' + encodeURIComponent(prompt(this.data('prompt'))) : this.data('request'),
						history: this.data('history') || null,
						cache: el.data('cache')
					}).then(
						handleJSON,
						console.error
					);
				}
			});
		});
		node.query('[data-svg-icon]').forEach(function(el) {
			el.ajax({
				url: 'images/icons/' + el.data('svg-icon') + '.svg',
				type: 'GET'
			});
		});
		node.query('[data-ajax], [data-ajax-request]').forEach(function(el) {
			ajax({
				url: el.data('ajax') || document.baseURI,
				request: el.data('ajax-request') || null,
				type: el.data('type') || 'GET',
				cache: el.data('cache')
			}).then(
				handleJSON,
				console.error
			);
		});
		node.query('[data-dropzone]') .forEach(function (finput) {
			document.querySelector(finput.data('dropzone')).DnD(finput);
		});
		node.query('[data-fullscreen]').forEach(function(el) {
			el.addEventListener(el.data('fullscreen'), function() {
				this.requestFullscreen();
			});
		});
		node.query('.clock').forEach(function(el) {
			el.worker_clock();
		});
		node.query('[data-encode]').forEach(function(el) {
			ajax({
				url: document.baseURI,
				request: 'encode=' + el.data('encode') + '&nonce=' + sessionStorage.nonce,
				type: 'POST',
				async: true,
				cache: el.data('encode')
			}).then(function(resp){
					el.attr(el.data('prop') || 'src', resp);
				},
				function(resp) {
					console.error(resp);
					if(el.data('fallback')) {
						el[el.data('prop') || 'src'] = el.data('fallback');
					}
				}
			);
		});
		node.query('[data-delete]').forEach(function(el) {
			el.addEventListener('click', function() {
				document.querySelectorAll(this.data('delete')).forEach(function(remove) {
					remove.parentElement.removeChild(remove);
				});
			});
		});
		node.query('menuitem[label="Edit Post"]').forEach(function(el) {
			el.addEventListener('click', function() {
				var form = document.createElement('form'),
					article = document.querySelector('article'),
					header = article.querySelector('header'),
					title = header.querySelector('[itemprop="headline"]'),
					keywords = header.querySelector('[itemprop="keywords"]'),
					content = article.querySelector('[itemprop="text"]'),
					submit = document.createElement('button'),
					fieldset = document.createElement('fieldset'),
					legend = document.createElement('legend'),
					oldTitle = document.createElement('input'),
					description = document.createElement('textarea'),
					footer = article.querySelector('footer');
				footer.parentElement.removeChild(footer);
				form.name = 'edit_post';
				form.method = 'POST';
				form.action = document.baseURI;
				form.attr('contextmenu', 'wysiwyg_menu');
				description.name = 'description';
				description.value = document.querySelector('meta[name="description"]').content;
				description.required = true;
				description.maxLength = 160;
				description.placeholder = 'Description will appear in searches. 160 character limit';
				legend.textContent = 'Update Post';
				submit.type = "Submit";
				submit.textContent = 'Update Post';
				title.attr('contenteditable', 'true');
				title.data('input-name','title');
				keywords.attr('contenteditable', 'true');
				keywords.data('input-name', 'keywords');
				keywords.setAttribute('open', '');
				content.attr('contenteditable', 'true');
				content.data('input-name', 'content');
				content.data('dropzone', 'main');
				oldTitle.name = 'old_title';
				oldTitle.type = 'hidden';
				oldTitle.readonly = true;
				oldTitle.required = true;
				oldTitle.value = title.textContent;
				fieldset.append(legend, header, content, oldTitle, description, submit);
				form.appendChild(fieldset);
				article.appendChild(form);
				var retain = setInterval(function(){
					ajax({
						url: document.baseURI,
						request: 'action=keep-alive'
					}).then(
						handleJSON,
						console.error
					);
				}, 60000);
				form.addEventListener('submit', function(){
					clearInterval(retain);
				});
			});
		});
		node.query('[label="Clear Cache"]').forEach(function(el) {
			el.addEventListener('click', function() {
				if(!this.data('confirm') || confirm(this.data('confirm'))){
					cache.clear();
				}
			});
		});
	});
	return this;
};
Element.prototype.worker_clock=function(){
	var clockWorker = new Worker(document.baseURI + 'scripts/workers/clock.js'),
		time = this;
	clockWorker.addEventListener('message', function(e){
		time.textContent = e.data.norm;
		time.setAttribute('datetime', e.data.datetime);
	});
	clockWorker.postMessage('');
};
function notifyLocation() {
	getLocation({
		enableHighAccuracy: true,
		maximumAge: 0
	}).then(function(pos){
		console.log(pos.coords);
		notify({
			title: 'Your current location:',
			body: 'Longitude: ' + pos.coords.longitude + "\nLatitude: " + pos.coords.latitude,
			icon: document.baseURI + 'images/icons/map.png'
		});
	});
}
Element.prototype.DnD = function (sets) {
	this.ondragover = function (event) {
		this.classList.add('receiving');
		return false;
	};
	this.ondragend = function (event) {
		this.classList.remove('receiving');
		return false;
	};
	this.ondrop = function (e) {
		this.classList.remove('receiving');
		e.preventDefault();
		console.log(e);
		if(e.dataTransfer.files.length) {
			for(var i=0; i < e.dataTransfer.files.length; i++) {
				var file = e.dataTransfer.files[i],
					reader = new FileReader(),
					progress = document.createElement('progress');
				progress.min = 0;
				progress.max = 1;
				progress.value= 0;
				progress.classList.add('uploading');
				sets.appendChild(progress);
				console.log(e, reader);
				reader.readAsDataURL(file);
				reader.addEventListener('progress', function(event) {
					if(event.lengthComputable){
						progress.value = event.loaded / event.total;
					}
				});
				reader.onload = function (event) {
					progress.parentElement.removeChild(progress);
					console.log(event);
					if(typeof sets !== 'undefined') {
						switch(sets.tagName.toLowerCase()) {
							case 'input':
							case 'textarea': {
								sets.value = event.target.result;
							} break;
							case 'img': {
								sets.src = event.target.result;
							} break;
							default: {
								if(/image\/*/.test(file.type)) {
									document.execCommand('insertimage', null, event.target.result);
								}
								else if(/text\/*/.test(file.type)){
									sets.innerHTML = event.target.result;
								}
							}
						}
					}
				};
				reader.onerror = function (event) {
					progress.parentElement.removeChild(progress);
					console.error(event);
				};
			console.log(file);
			}
		}
		return false;
	};
};
