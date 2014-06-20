'use strict';
var body = document.body,
	html = document.documentElement,
	prefixes = ['', '-moz-', '-webkit-', '-o-', '-ms-'];
if (!window.Element) {
	/*Fix IE not allowing Element.prototype*/
	Element = function () {
	};
}
if (!Element.prototype.matches) {
	/*Check if Element matches a given CSS selector*/
	Element.prototype.matches = function (sel) {
		try {
			if (html.mozMatchesSelector) {
				return this.mozMatchesSelector(sel);
			}
			else if (html.webkitMatchesSelector) {
				return this.webkitMatchesSelector(sel);
			}
			else if (html.oMatchesSelector) {
				return this.oMatchesSelector(sel);
			}
			else if (html.msMatchesSelector) {
				return this.msMatchesSelector(sel);
			}
			else {
				return ($(sel) .indexOf(this) !== -1);
			}
		}
		catch(e) {
			return ($(sel) .indexOf(this) !== -1);
		}
	}
}
Array.prototype.unique = function() {
	return this.filter(
		function(val, i, arr) {
			return (i <= arr.indexOf(val));
		}
	);
}
Array.prototype.end = function() {
	return this[this.length - 1];
}

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
}
/*===========================De-Prefix several JavaScript methods==========================================================================*/

if (!'Notification' in window) {
	Notification = mozNotification || false;
}
if (!'notifications' in window) {
	window.notifications = window.webkitNotifications || window.oNotifications || window.msNotifications || false;
}
if (!'indexedDB' in window) {
	window.indexedDB = window.indexedDB || window.mozIndexedDB || window.webkitIndexedDB || window.msIndexedDB || false;
}
if (!'hidden' in document) {
	document.hidden = function () {
		return document.webkitHidden || document.msHidden || document.mozHidden || false;
	}
}
if (!'visibilityState' in document) {
	document.visibilityState = document.webkitVisibilityState || document.msVisibilityState || document.mozVisibilityState || false;
}
if (!'fullScreenElement' in document) {
	document.fullScreenElement = document.mozFullScreenElement || document.webkitFullscreenElement || false;
}
//document.fullscreen = document.fullscreen || document.mozFullScreen || document.webkitFullscreen || false;

if (!'requestAnimationFrame' in window) {
	window.requestAnimationFrame = window.mozRequestAnimationFrame || window.webkitRequestAnimationFrame || window.msRequestAnimationFrame || false;
}
if (!'cancelFullScreen' in document) {
	document.cancelFullScreen = document.mozCancelFullScreen || document.webkitCancelFullScreen || document.msCancelFullScreen || false;
}
if (!'requestFullScreen' in document) {
	Element.prototype.requestFullScreen = function () {
		return this.mozRequestFullScreen() || this.webkitRequestFullScreen() || false;
	}
}
/*===============================================================================================================================================*/
Object.prototype.isaN = function () {
	/*Boolean... is this a number?*/
	return parseFloat(this) == this;
}
Element.prototype.delete = function() {
	this.parentElement.removeChild(this);
}
Object.prototype.camelCase = function () {
	return this.toLowerCase() .replace(/\ /g, '-') .replace(/-(.)/g, function (match, group1) {
		return group1.toUpperCase();
	});
}
Element.prototype.after = function (content) {
	this.insertAdjacentHTML('afterend', content);
	return this;
};
Element.prototype.before = function (content) {
	this.insertAdjacentHTML('beforebegin', content);
	return this;
}
Element.prototype.prev = function (){ /*Returns the node just prior to Element*/
	return this.previousSibling;
}
Element.prototype.prepend = function(content) {
	this.insertAdjacentHTML('afterbegin', content);
	return this;
}
Element.prototype.append = function(content) {
	this.insertAdjacentHTML('beforeend', content);
	return this;
}
Element.prototype.html = function(html) {
	this.innerHTML = html;
	return this;
}
Element.prototype.data = function(set, value) {
	var val = null;
	if(!!document.body.dataset){
		(typeof value !== 'undefined') ? this.dataset[set.camelCase()] = value : val = this.dataset[set.camelCase()];
	}
	else {
		(typeof value !== 'undefined') ? this.setAttribute('data-' + set, value): val = this.getAttribute('data-' + set);
	}
	return val;
}
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
}
Element.prototype.ancestor = function (sel) {
	/*return (this.parentElement.tagName.toLowerCase() === tag) ? this.parentElement : this.parentElement.ancestor(tag);*/
	if(this.parentElement.matches(sel)) {
		return this.parentElement;
	}
	else if(this === document.body) {
		return false;
	}
	else {
		return this.parentElement.ancestor(sel);
	}

}
Element.prototype.ajax = function(args) {
	ajax(args).then(
		this.html.bind(this),
		console.error
	);
	return this;
}
String.prototype.toBase64 = btoa;
String.prototype.fromBase64 = atob;
/*Element.prototype.addClass = function(cname) {
	(supports('classList')) ? this.classList.add(cname) : this.classlist().add(cname);
	return this;
}
Element.prototype.removeClass = function(cname) {
	(supports('classList')) ? this.classList.remove(cname) : this.classlist().remove(cname);
	return this;
}
Element.prototype.hasClass = function(cname) {
	return (supports('classList')) ? this.classList.contains(cname) : this.classlist().constains(cname);
}
Element.prototype.toggleClass = function(cname, condition) {
	(supports('classlist')) ? this.classList.toggle(cname, condition || !this.hasClass(cname)) : this.classlist().toggle(cname, condition || !this.hasClass(cname));
	return this;
}*/
Element.prototype.values = function () {
	var inputs = this.querySelectorAll('input:not([type=submit]):not([type=reset]),select,textarea'),
	results = [
		'form=' + this.name
	],
	val;
	inputs.forEach(function (input) {
		if (input.name && input.value) {
			(input.type === 'checkbox') ? val = input.checked : val = input.value;
			results.push(encodeURIComponent(input.name) + '=' + encodeURIComponent(val));
		}
	});
	return results.join('&');
}
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
				(Notification.permission.toLowerCase() === 'granted') ? notification = notify(options)  : alert(options.title || document.title + '\n' + options.body);
			});
		}
		notification = new Notification(options.title || document.title, options);
	}
	else if ('notifications' in window) {
		if (window.notifications.checkPermission != 1) {
			window.notifications.requestPermission();
		}
		notification = window.notifications.createNotification(options.icon, options.title || document.title, options.body) .show();
	}
	else {
		alert(options.title || document.title + '\n' + options.body);
	}
	if (!!notification) {
		(!!options.onclick) ? notification.onclick = options.onclick : null;
		(!!options.onshow) ? notification.onshow = options.onshow : null;
		(!!options.onclose) ? notification.onclose = options.onclose : null;
		(!!options.onerror) ? notification.onerror = options.onerror : notification.onerror = function (error) {
			console.error(error)
		};
		return notification;
	}
};
/*AppCache updater*/
/*$(window) .load(function (e) { *//*Check for appCache updates if there is a manifest set*/
window.addEventListener('load', function () {
	/**
	*TODO Should I check for manifest on anything but <html>?
	*		Could use (!!$('[manifest]').length) instead.
	*/
	if ((!!window.applicationCache) && typeof html.getAttribute('manifest') === 'string') {
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
function supports(type) {
	/*Feature detection. Returns boolean value of suport for type*/
	/**
	* A series of tests to determine support for a given feature
	* Defaults to testing support for an element of tag (type)
	* Which works by testing if the browser considers it unknown element type
	*/
	type = type.toLowerCase();
	if(typeof sessionStorage['Supports_' + type] !== 'undefined') {
		return sessionStorage['Supports_' + type] === 'true';
	}
	var supports = false,
	prefixes = [
		/*Array of vendor prefixes*/
		'',
		'moz',
		'webkit',
		'ms',
		'o'
	],
	/*Shorten for CSS properties*/
	style = document.documentElement.style;
	supportsTest:
	switch (type) {
		case 'queryselectorall':
			supports = ('querySelectorAll' in document);
			break;
		case 'svg':
			supports = (document.implementation.hasFeature('http://www.w3.org/TR/SVG11/feature#Shape', '1.1'));
			break;
		case 'dataset':
			supports = (!!document.body.dataset);
			break;
		case 'geolocation':
			supports = ('geolocation' in navigator);
			break;
		case 'connectivity':
			supports = ('onLine' in navigator);
			break;
		case 'visibility':
			supports = ('visibilityState' in document) || ('webkitVisibilityState' in document);
			break;
		case 'validity':
			supports = (!!document.createElement('input') .validity);
			break;
		case 'fonts':
			supports = ('CSSFontFaceRule' in window);
			break;
		case 'csssupports':
			supports = ('supports' in CSS);
			break;
		case 'listeners':
			supports = ('addEventListener' in window);
			break;
		case 'animations':
			supports = ((('supports' in CSS) && CSS.supports('animation', 'name') ||
				CSS.supports('-webkit-animation', 'name')) ||
				style.animation !== undefined ||
				style.webkitAnimation !== undefined ||
				style.MozAnimation !== undefined ||
				style.OAnimation !== undefined ||
				style.MsAnimationn !== undefined
			);
			break;
		case 'transitions':
			supports = ((('supports' in CSS) && CSS.supports('transition', 'none') ||
				CSS.supports('-webkit-transition', 'none')) ||
				style.transition !== undefined ||
				style.webkitTransition !== undefined ||
				style.MozTransition !== undefined ||
				style.OTransition !== undefined ||
				style.MsTransition !== undefined
			);
			break;
		case 'cssgradients':
			supports = (('supports' in CSS) && CSS.supports('background-image', 'linear-gradient(red,red)')) || (function(){
				var el = document.createElement('a');
				el.style.backgroundImage = 'linear-gradient(red, red)';
				return (!!el.style.backgroundImage);
			})();
			break;
		case 'notifications':
			supports = ('notifications' in window || 'Notification' in window);
			break;
		case 'applicationcache':
			supports = ('applicationCache' in window);
			break;
		case 'indexeddb':
			supports = ('indexedDB' in window);
			break;
		case 'fullscreen':
			supports = (!!document.cancelFullScreen);
			break;
		case 'workers':
			supports = (!!window.Worker);
			break;
		case 'promises':
			supports = ('Promise' in window);
			break;
		case 'cssmatches':
			var matches = [':matches', ':any', ':-moz-any', ':-webkit-any'], i;
			for(i = 0; i < matches.length; i++) {
				try {
					supports = Boolean(document.querySelector(matches[i] + '(body)') === document.body);
					sessionStorage.MatchesPre = matches[i];
				}
				catch(e) {
					null;
				}
			}
			break;
		case 'ajax':
			supports = ('XMLHttpRequest' in window);
			break;
		case 'cssvars':
			supports = (!!CSS.supports('var-x','x'));
			break;
		case 'formdata':
			supports = ('FormData' in window);
			break;
		case 'classlist' :
			supports = ('DOMTokenList' in window);
			break;
		case 'localstorage':
			supports = ('localStorage' in window);
			break;
		case 'sessionstorage':
			supports = ('sessionStorage' in window);
			break;
		default:
			supports = (document.createElement(type.toLowerCase()) .toString() !== document.createElement('DNE') .toString());
	}
	sessionStorage['Supports_' + type] = supports;
	return supports;
}
Element.prototype.query = function(query) {
	var els = [];
	if(this.matches(query)) {
		els.push(this);
	}
	this.querySelectorAll(query).forEach(function(el) {
		els.push(el)
	});
	return els;
}
Object.prototype.keys = function() {
	return Object.keys(this) || [];
}
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
		data.form.querySelectorAll('[contenteditable][data-input-name]').forEach(function(input) {
			data.request.append(input.data('input-name'), input.innerHTML);
		});
	}
	return new Promise(function (success, fail) {
		var canonical = $('[rel=canonical]');
		/*https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Promise*/
		if(data.cache && cache.has(data.cache)) {
			if(typeof data.history === 'string') {
				history.pushState(null, '', data.history);
				if(canonical.found) {
					canonical.get(0).href = data.history;
				}
			}
			success(cache.get(data.cache));
		}
		else if(typeof navigator.onLine !== 'boolean' || navigator.onLine) {
			var req = new XMLHttpRequest(),
				progress = document.createElement('progress');
			if(typeof data.contentType !== 'string') {
				data.contentType = 'application/x-www-form-urlencoded';
			}
			progress.min = 0;
			progress.max = 1;
			progress.value = 0;
			progress.className = 'ajax_progress'
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
			req.addEventListener('load', function () {
				if(req.status == 200) {
					progress.parentElement.removeChild(progress);
					if(data.cache) {
						cache.set(data.cache, req.response.trim());
					}
					success(req.response.trim());
					if(typeof data.history === 'string') {
						history.pushState(null, '', data.history);
						if(canonical.found) {
							canonical.get(0).href = data.history;
						}
					}
				}
				else {
					fail(Error(req.statusText));
				}
			});
			req.addEventListener('error', function () {
				fail(Error('Network Error'));
				progress.parentElement.removeChild(progress);
			});
			if(typeof data.request !== 'undefined') {
				req.send(data.request);
			}
			else {
				req.send();
			}
		}
		else {
			notify({
				title: 'Network:',
				body: 'offline',
				icon: 'images/icons/network-server.png'
			});
			fail('No Internet Connection');
		}
	});
}
function handleJSON(json){
	if(typeof json === 'undefined') {
		return false;
	}
	if(typeof json === 'string') {
		json = JSON.parse(json.trim());
	}
	if (json.remove) {
		document.querySelectorAll(json.remove).forEach(function(el){
			el.parentElement.removeChild(el);
		});
	}
	Object.keys(json.text || []).forEach(function(key){
		document.querySelector(key).textContent = json.text[key];
	});
	Object.keys(json.html || []).forEach(function(key){
		document.querySelector(key).innerHTML = json.html[key];
	});
	Object.keys(json.after || []).forEach(function(key){
		document.querySelector(key).insertAdjacentHTML('afterend', json.after[key]);
	});
	Object.keys(json.before || []).forEach(function(key){
		document.querySelector(key).insertAdjacentHTML('beforebegin', json.before[key]);
	});
	Object.keys(json.append || []).forEach(function(key){
		document.querySelector(key).insertAdjacentHTML('beforeend', json.append[key]);
	});
	Object.keys(json.prepend || []).forEach(function(key){
		document.querySelector(key).insertAdjacentHTML('afterbegin', json.prepend[key]);
	});
	Object.keys(json.addClass || []).forEach(function(selector){
		document.querySelectorAll(selector).forEach(function(el){
			json.addClass[selector].split(',').forEach(function(cname) {
				el.classList.add(cname);
			});
		});
	});
	Object.keys(json.removeClass || []).forEach(function(selector){
		document.querySelectorAll(selector).forEach(function(el){
			json.removeClass[selector].split(',').forEach(function(cname) {
				el.classList.remove(cname);
			});
		});
	});
	Object.keys(json.attributes || []).forEach(function(selector) {
		document.querySelectorAll(selector).forEach(function(el) {
			Object.keys(json.attributes[selector] || []).forEach(function(attribute) {
				if(typeof json.attributes[selector][attribute] === 'boolean'){
					(json.attributes[selector][attribute]) ? el.setAttribute(attribute, '') : el.removeAttribute(attribute);
				}
				else {
					el.setAttribute(attribute, json.attributes[selector][attribute]);
				}
			});
		});
	});
	Object.keys(json.sessionStorage || []).forEach(function(key) {
		(json.sessionStorage[key] === '') ? delete sessionStorage[key] : sessionStorage[key] = json.sessionStorage[key];
	});
	Object.keys(json.localStorage || []).forEach(function(key) {
		(json.localStorage[key] === '') ? delete sessionStorage[key] : localStorage[key] = json.localStorage[key];
	});
	if (json.notify) {
		notify(json.notify);
	}
	if(json.script) {
		eval(json.script);
	}
	if(json.log){
		console.log(json.log);
	}
	if(json.error){
		console.error(json.error);
	}
}
function cache() {
	return this;
}
cache.prototype.constructor = cache;
cache.prototype.has = function(key) {
	return localStorage.keys().indexOf(('cache ' + key).camelCase()) !== -1;
}
cache.prototype.get = function(key) {
	return (this.has(key)) ? localStorage[('cache ' + key).camelCase()] : false;
}
cache.prototype.set = function(key, value) {
	localStorage[('cache ' + key).camelCase()] = value;
	return this;
}
cache.prototype.unset = function(key) {
	delete localStorage[('cache ' + key).camelCase()];
	return this;
}
cache.prototype.keys = function() {
	return localStorage.keys().filter(function(key){
		return /^cache/.test(key);
	});
}
cache.prototype.each = function(callback) {
	return this.keys().forEach(callback.bind(this));
}
cache.prototype.clear = function() {
	this.each(function(key){
		delete localStorage[key];
	});
	return this;
}
Object.prototype.isArray = false;
Object.prototype.isString = false;
Object.prototype.isNumber = false;
Array.prototype.isArray = true;
String.prototype.isString = true;
Number.prototype.isNumber = true;
/*======================================================zQ Functions=========================================================*/
Object.prototype.isZQ = false;
zQ.prototype.isZQ = true;
/*Add Array prototypes to NodeList*/
['forEach', 'indexOf', 'some', 'every', 'map', 'filter'].forEach(function(feat){
	NodeList.prototype[feat] = Array.prototype[feat];
});
function $(e) {
	if(e.isZQ){
		return e;
	}
	return new zQ(e);
}
zQ.prototype.constructor = zQ;
function zQ(q) {
	this.query = q;
	try {
		switch(typeof this.query) {
			case 'string':
				 this.results = document.querySelectorAll(this.query);
				break;
			default:
				this.results = [this.query];
		}
	}
	catch (error) {
		console.error(error, this);
		console.error('No results for ' + this.query);
	}
	this.length = this.results.length;
	this.found = (!!this.results.length);
	return this;
}
zQ.prototype.get = function(n) {
	return this.results[n];
}
zQ.prototype.each = function(callback) {
	if(this.found) {
		this.results.forEach(callback);
	}
	return this;
}
zQ.prototype.indexOf = function(i) {
	return this.results.indexOf(i);
}
zQ.prototype.some = function(callback) {
	return this.results.some(callback);
}
zQ.prototype.every = function(callback) {
	return this.results.every(callback);
}
zQ.prototype.filter = function(callback) {
	return this.results.filter(callback);
}
zQ.prototype.map = function(callback) {
	return this.results.map(callback);
}
zQ.prototype.addClass = function(cname) {
	this.each(function(el) {
		el.classList.add(cname);
	});
	return this;
}
zQ.prototype.removeClass = function(cname) {
	this.each(function(el){
		el.classList.remove(cname);
	});
	return this;
}
zQ.prototype.hasClass = function(cname) {
	return this.some(function(el){
		return el.classList.contains(cname)
	});
}
zQ.prototype.toggleClass = function(cname, condition) {
	this.each(function(el){
		el.classList.toggle(cname, condition || !el.classList.contains(cname));
	});
	return this;
}
zQ.prototype.delete = function() {
	this.each(function(el){
		el.parentElement.removeChild(el);
	});
}
zQ.prototype.pause = function() {
	this.each(function(media){
		media.pause();
	});
	return this;
}
/*======================================================Listener Functions=========================================================*/

zQ.prototype.on = function (event, callback) {
	this.each(function (e) {
		(html.addEventListener) ? e.addEventListener(event, callback, true)  : e['on' + event] = callback;
	});
	return this;
};
/*Listeners per event type*/
['click','dblclick','contextmenu','keypress','keyup','keydown','mouseenter','mouseleave','mouseover','mouseout','mousemove','mousedown','mouseup','input','change','submit','reset','select','focus','blur','resize','updateready','DOMContentLoaded','load','unload','beforeunload','abort','error','scroll','drag','offline', 'online','visibilitychange','popstate', 'pagehide'].forEach(function(ev){
	zQ.prototype[ev] = function(callback){
		return this.on(ev, callback);
	}
});
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
			$(e) .listen(pre + 'visibilitychange', callback);
		});
	});
	return this;
};
zQ.prototype.watch = function(watching, options, attributeFilter) {
	/*https://developer.mozilla.org/en-US/docs/Web/API/MutationObserver*/
	if(typeof options === 'undefined') {
		options = [];
	}
	var watcher = new MutationObserver(function(mutations){
		mutations.forEach(function(mutation){
			watching[mutation.type].call(mutation);
		});
	}),
	watches = new Object();
	Object.keys(watching).concat(options).forEach(function(event){
		watches[event] = true;
	});
	if(typeof attributeFilter !== 'undefined' && attributeFilter.isArray) {
		watches.attributeFilter = attributeFilter;
	}
	this.each(function(el){
		watcher.observe(el, watches);
	});
	return this;
}
/*====================================================================================================================*/
zQ.prototype.$ = function (q) {
	if((typeof this.query === 'string') && supports('cssmatches')) {
		/*Only works for $().$(), but not for $().$().[*].$()*/
		this.query = sessionStorage.MatchesPre + '(' + this.query +') '+ ' ' + sessionStorage.MatchesPre + '(' + q +')';
		return $(this.query);
	}
	this.results = [];
	(this.query.isArray) ? this.query.push(q) : this.query = [this.query, q];
	/*What happesn with this.query?*/
	this.each(function(el){
		el.querySelectorAll(q).forEach(function(e){
			this.results.push(e);
		});
	});
	this.results = this.results.unique();
	this.length = this.results.length;
	return this;
}
$(window) .popstate(function () {
		ajax({
			url: location.pathname,
			type: 'GET'
		}).then(
			handleJSON,
			console.error
		);
	});
Object.prototype.$ = function(q) {
	if(this === document || this === window){
		return $(q);
	}
	return $(this).$(q);
}
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
	for (let i = 0; i < args.length; i++) {
		this.each(function (e) {
			e.style[args[i]] = value[i];
		});
	}*/
	var style = document.styleSheets[document.styleSheets.length - 1];
	style.insertRule(this.query + '{' + args +'}', style.cssRules.length);
	return this;
};
