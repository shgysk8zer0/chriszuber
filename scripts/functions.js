'use strict';
var body = document.body,
html = document.documentElement,
	prefixes = ['-moz-', '-webkit-', '-o-', '-ms-'];
if (!window.Element) {
	/*Fix IE not allowing Element.prototype*/
	Element = function () {
	};
}
if (!Element.prototype.matches) {
	/*Check if Element matches a given CSS selector*/
	Element.prototype.matches = function (sel) {
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
}
Array.prototype.unique = function() {
	return this.filter(
		function(val, i, arr)
		{
			return (i <= arr.indexOf(val));
		}
	);
}
Array.prototype.end = function() {
	return this[this.length - 1];
}
/*===========================De-Prefix several JavaScript methods==========================================================================*/

if (!Notification) {
	Notification = mozNotification || false;
}
if (!window.notifications) {
	window.notifications = window.webkitNotifications || window.oNotifications || window.msNotifications || false;
}
if (!window.indexedDB) {
	window.indexedDB = window.indexedDB || window.mozIndexedDB || window.webkitIndexedDB || window.msIndexedDB || false;
}
if ('boolean' !== typeof document.hidden) {
	document.hidden = function () {
		return document.webkitHidden || document.msHidden || document.mozHidden || false;
	}
}
if (!document.visibilityState) {
	document.visibilityState = document.webkitVisibilityState || document.msVisibilityState || document.mozVisibilityState || false;
}
if (!document.fullScreenElement) {
	document.fullScreenElement = document.mozFullScreenElement || document.webkitFullscreenElement || false;
}
//document.fullscreen = document.fullscreen || document.mozFullScreen || document.webkitFullscreen || false;

if (!window.requestAnimationFrame) {
	window.requestAnimationFrame = window.mozRequestAnimationFrame || window.webkitRequestAnimationFrame || window.msRequestAnimationFrame || false;
}
if (!document.cancelFullScreen) {
	document.cancelFullScreen = document.mozCancelFullScreen || document.webkitCancelFullScreen || document.msCancelFullScreen || false;
}
if (!document.requestFullScreen) {
	Element.prototype.requestFullScreen = function () {
		return this.mozRequestFullScreen() || this.webkitRequestFullScreen() || false;
	}
}
/*===============================================================================================================================================*/
Object.prototype.log = function () {
	/*Use instead of console.log(this)*/
	console.log(this);
	return this;
}
Object.prototype.isaN = function () {
	/*Boolean... is this a number?*/
	return parseFloat(this) == this;
}
Object.prototype.camelCase = function () {
	return this.toLowerCase() .replace(/\ /g, '-') .replace(/-(.)/g, function (match, group1) {
		return group1.toUpperCase();
	});
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
Element.prototype.data = function(set, value) {
	var val = null;
	if(!!document.body.dataset){
		(typeof value !== 'undefined') ? this.dataset[set] = value : val = this.dataset[set];
	}
	else {
		(typeof value !== 'undefined') ? this.setAttribute('data-' + set, value): val = this.getAttribute('data-' + set);
	}
	return val;
}
function ajax(data) {
	if (typeof data.url !== 'string') {
		data.url = document.baseURI;
	}
	if (typeof data.type !== 'string') {
		data.type = 'POST';
	}
	if (typeof data.async !== 'boolean') {
		data.async = true;
	}
	if ((data.type === 'get') && (typeof data.request === 'string')) {
		data.url += '?' + data.request;
	}
	return new Promise(function (success, fail) {
		/*https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Promise*/
		var req = new XMLHttpRequest();
		req.open(data.type, data.url, data.async);
		req.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		req.setRequestHeader('Request-Type', 'AJAX');
		req.onload = function () {
			(req.status == 200) ? success(req.response)  : fail(Error(req.statusText));
		};
		req.onerror = function () {
			fail(Error('Network Error'));
		};
		(typeof data.request === 'string') ? req.send(data.request)  : req.send();
	});
}
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
	/**
	*TODO Directly create notification instead of defining function by which method is supported
	*/
	var notification;
	if (typeof options === 'string') {
		options = {
			body: options
		};
	}
	if ('Notification' in window) {
		if (Notification.permission.toLowerCase() === 'default') {
			Notification.requestPermission(function () {
				(Notification.permission.toLowerCase() === 'granted') ? notification = notify(options)  : alert(options.title || document.title + '\n' + options.body);
			});
		}
		notification = new Notification(options.title || document.title, options);
	}
	else if ('mozNotification' in navigator) {
		notification = navigator.mozNotification.createNotification(options.title || document.title, options.body, options.icon) .show();
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
			supports = (!!document.querySelectorAll);
			break;
		case 'svg':
			supports = (document.implementation.hasFeature('http://www.w3.org/TR/SVG11/feature#Shape', '1.1'));
			break;
		case 'dataset':
			supports = (!!document.body.dataset);
			break;
		case 'geolocation':
			supports = (!!navigator.geolocation);
			break;
		case 'connectivity':
			supports = (!!navigator.onLine);
			break;
		case 'visibility':
			supports = (!!((document.visibilityState) || (document.webkitVisibilityState)));
			break;
		case 'validity':
			supports = (!!document.createElement('input') .validity);
			break;
		case 'fonts':
			supports = !!window.CSSFontFaceRule;
			break;
		case 'csssupports':
			supports = (!!CSS.supports);
			break;
		case 'listeners':
			supports = (!!window.addEventListener);
			break;
		case 'animations':
			supports = (((!!CSS.supports) && CSS.supports('animation', 'name') ||
			CSS.supports('-webkit-animation', 'name')) ||
			style.animation !== undefined ||
			style.webkitAnimation !== undefined ||
			style.MozAnimation !== undefined ||
			style.OAnimation !== undefined ||
			style.MsAnimationn !== undefined
			);
			break;
		case 'transitions':
			supports = (((!!CSS.supports) && CSS.supports('transition', 'none') ||
			CSS.supports('-webkit-transition', 'none')) ||
			style.transition !== undefined ||
			style.webkitTransition !== undefined ||
			style.MozTransition !== undefined ||
			style.OTransition !== undefined ||
			style.MsTransition !== undefined
			);
			break;
		case 'notifications':
			supports = (!!window.notifications || !!window.Notification);
			break;
		case 'applicationcache':
			supports = (!!window.applicationCache);
			break;
		case 'indexeddb':
			supports = (!!window.indexedDB);
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
		default:
			supports = (document.createElement(type.toLowerCase()) .toString() !== document.createElement('DNE') .toString());
	}
	sessionStorage['Supports_' + type] = supports;
	return supports;
}
Element.prototype.bootstrap = function() {
	this.parentElement.querySelectorAll('form').forEach(function(el){
		el.addEventListener('submit', function(event){
			event.preventDefault();
			this.ajaxSubmit().then(handleXHRjson, console.error);

		});
	});
	this.parentElement.querySelectorAll('[data-request]:not([data-target])').forEach(function(el) {
		el.addEventListener('click', function(){
			ajax({
				url: this.data('url')|| document.baseURI,
				request: this.data('request')
			}).then(handleXHRjson, console.error);
		});
	});
	this.parentElement.querySelectorAll('[data-request][data-target]').forEach(function(el) {
		el.addEventListener('click', function(){
			ajax({
				url: this.data('url'),
				request: this.data('request')
			}).then(function(resp){
				document.querySelector(el.data('target')).innerHTML = resp;
			}, console.error);
		});
	});
	return this;
}
NodeList.prototype.bootstrap = function() {
	this.forEach(function(node){
		node.bootstrap();
	});
	return this;
}
function handleXHRjson(data){
	var json = JSON.parse(data);
	if (json.notify) {
		notify(json.notify);
	}
	if (json.remove) {
		$(json.remove).delete();
	}
	Object.keys(json.html || []).forEach(function(key){
		document.querySelector(key).innerHTML = json.html[key];
	});
	Object.keys(json.after || []).forEach(function(key){
		document.querySelector(key) .after(json.after[key]);
	});
	Object.keys(json.before || []).forEach(function(key){
		document.querySelector(key) .before(json.before[key]);
	});
	Object.keys(json.append || []).forEach(function(key){
		document.querySelector(key).append(json.append[key]);
	});
	Object.keys(json.prepend || []).forEach(function(key){
		document.querySelector(key).prepend(json.prepend[key]);
	});
}
Element.prototype.ajaxSubmit = function() {
	var form = this;
	return new Promise(function (success, fail) {
		if(form.tagName.toLowerCase() !== 'form'){
			fail(Error(form.tagName + ' is not a form'));
		}
		var formData = new FormData(form),
			req = new XMLHttpRequest();
		formData.append('form', form.name);
		req.open(form.method, form.action);
		req.setRequestHeader('Request-Type', 'AJAX');
		req.send(formData);
		req.onload = function () {
			(req.status == 200) ? success(req.response)  : fail(Error(req.statusText));
		};
		req.onerror = function () {
			fail(Error('Network Error'));
		};
	});
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
		(typeof this.query === 'string') ? this.results = document.querySelectorAll(this.query)  : this.results = [
			this.query
		];
	}
	catch (error) {
		console.error(error);
		console.error('No results for ' + this.query);
	}
	this.length = this.results.length;
	return this;
}
zQ.prototype.get = function(n) {
	return this.results[n];
}
zQ.prototype.each = function(callback) {
	this.results.forEach(callback);
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
		el.classList.add(cname);;
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
zQ.prototype.delete = function() {
	this.each(function(el){
		el.parentElement.removeChild(el);
	});
}
/*======================================================Listener Functions=========================================================*/

zQ.prototype.listen = function (event, callback) {
	this.each(function (e) {
		(html.addEventListener) ? e.addEventListener(event, callback, true)  : e['on' + event] = callback;
	});
	return this;
};
/*Listeners per event type*/
['click','dblclick','contextmenu','keypress','keyup','keydown','mouseenter','mouseleave','mouseover','mouseout','mousemove','mousedown','mouseup','input','change','submit','reset','select','focus','blur','resize','updateready','DOMContentLoaded','load','unload','beforeunload','abort','error','scroll','drag','offline', 'online','visibilitychange','popstate', 'pagehide'].forEach(function(ev){
	zQ.prototype[ev] = function(callback){
		return this.listen(ev, callback);
	}
});
zQ.prototype.networkChange = function (callback) {
	return this.online(callback) .offline(callback);
};
zQ.prototype.playing = function (callback) {
	this.forEach(function (e) {
		/*Does not work with listeners. Use onEvent by default*/
		e.onplay = callback;
	});
	return this;
};
zQ.prototype.paused = function (callback) {
	this.forEach(function (e) {
		e.onpause = callback;
	});
	return this;
};
zQ.prototype.visibilitychange = function (callback) {
	this.forEach(function (e) {
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
zQ.prototype.watch = function(watching, options) {
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
	})
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
