'use strict';
var body = document.body,
html = document.documentElement;
Error.prototype.report = function (){ /* Use instead of console.error(error)*/
	console.error(this);
}
function _(query){ /*Shortened document.querySelector*/
	return document.querySelector(query);
}
function host(){/* Returns http(s)?://example.com*/
	return top.location.protocol.toString()+'//'+top.location.host.toString();
}
function internalLinks(){ /*Matches all links that are not external*/
	/**
	*TODO Needs work on relative links
	*/
	var host=top.location.host.toString();
	return $('a[href^="//'+host+'"],a[href^="http://'+host+'"],a[href^="https://'+host+'"],a[href^="/"], a[href^="./"], a[href^="../"], a[href^="#"]');
}
if ( !window.Element ) { /*Fix IE not allowing Element.prototype*/
	Element = function() {};
}
if (!Element.prototype.matches) { /*Check if Element matches a given CSS selector*/
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
			return ($(sel) .has(this));
		}
	}
}
/*===========================De-Prefix several JavaScript methods==========================================================================*/

if(!Notification){
	Notification = mozNotification || false;
	}
if(!window.notifications) {
	window.notifications = window.webkitNotifications || window.oNotifications || window.msNotifications || false;
}
if(!window.indexedDB) {
	window.indexedDB = window.indexedDB || window.mozIndexedDB || window.webkitIndexedDB || window.msIndexedDB || false;
}
if('boolean' !== typeof document.hidden) {
	document.hidden = function() {
		return document.webkitHidden || document.msHidden || document.mozHidden || false;
	}
}
if(!document.visibilityState) {
	document.visibilityState = document.webkitVisibilityState || document.msVisibilityState || document.mozVisibilityState || false;
}
if(!document.fullScreenElement) {
	document.fullScreenElement = document.mozFullScreenElement || document.webkitFullscreenElement || false;
}
//document.fullscreen = document.fullscreen || document.mozFullScreen || document.webkitFullscreen || false;
if(!window.requestAnimationFrame) {
	window.requestAnimationFrame =  window.mozRequestAnimationFrame || window.webkitRequestAnimationFrame || window.msRequestAnimationFrame || false;
}
if(!document.cancelFullScreen){
	document.cancelFullScreen = document.mozCancelFullScreen || document.webkitCancelFullScreen || document.msCancelFullScreen || false;
}
if (!document.requestFullScreen) {
	Element.prototype.requestFullScreen = function () {
		return this.mozRequestFullScreen() || this.webkitRequestFullScreen() || false;
	}
}
/*===============================================================================================================================================*/

Object.prototype.log = function () { /*Use instead of console.log(this)*/
	console.log(this);
	return this;
}
Object.prototype.isaN = function(){ /*Boolean... is this a number?*/
	return parseFloat(this) == this;
}
Array.prototype.find = function (obj) {/*Retuns an index of 1 based search for obj. Returns 0 if not found*/
	return this.indexOf(obj) + 1;
}
Array.prototype.has = function (obj) {/*Does Array contain obj?*/
	return (!!this.find(obj));
}
Array.prototype.filter = function (pat) {/*Advanced filters for array*/
	/**
	*TODO Make this actually work and be useful
	*/
	var e,
	arr = this;
	this.forEach(function (e) {
		if (!e.matches(pat)) {
			arr = arr.not(pat);
		}
	});
	return this;
}
Array.prototype.add = function (obj) {/*concat or push, depending on if obj is an array*/
	arr=this;
	(obj.isArray()) ? arr = arr.concat(obj)  : arr.push(obj);
	return arr;
}
Array.prototype.not = function (obj) { /*Returns and array containing everyhing but obj*/
	var arr = this,
	e;
	if(obj.isString()){
		obj=$(obj);
	}
	if (obj.isArray()) {
		obj.forEach(function (e) {
			arr = arr.not(e);
		});
		return arr;
	} 
	else {
		var i = this.indexOf(obj);
		(i !== - 1) ? this.splice(i, 1)  : null;
		return this;
	}
}
Element.prototype.hasChild = function (tag){ /*Does Element have any children of type tag?*/
	return (!!this.querySelector(tag));
};
function getCookie(cName) { /*Gets value of cookie named cName*/
	/**
	*[TODO]Create a setCookie method
	* Handle multi-value cookies as seen in Ananlytics
	*/
	var cookies = document.cookie.split(';'),
	names = cookies.map(function (cookie) {
		return cookie.substring(0, cookie.indexOf('='));
	}),
	values = cookies.map(function (cookie) {
		return cookie.substring(cookie.indexOf('=') + 1);
	}),
	index = names.indexOf(cName);
	if (index !== - 1) {
		return values[index];
	}
	return false;
};
Element.prototype.append = function (content){ /*Append a node to Element*/
	this.appendChild(content.cloneNode());
	return this;
}
Element.prototype.prepend = function (content){ /*Prepend a node to Element*/
	this.parentElement.insertBefore(content, this);
	return this;
}
Element.prototype.before = function (el, clone) { /*Insert a node before ELement*/
	if('boolean' === typeof clone && clone) {
		el = el.cloneNode(true);
	}
	this.parentElement.insertBefore(el, this);
	return this;
}
Element.prototype.after = function (el, clone) { /*Insert a node after Element*/
	if('boolean' === typeof clone && clone) {
		el = el.cloneNode(true);
	}
	this.parentElement.insertBefore(el, this.nextSibling);
	return this;
}
Element.prototype.prev = function (){ /*Returns the node just prior to Element*/
	return this.previousSibling;
}
Element.prototype.getStyles = function () { /*Returns style rules (array-like) for Element*/
	return window.getComputedStyle(this);
};
Element.prototype.getStyle = function (prop) { /*Gets a single style rule for Element*/
	return this.getStyles[prop.camelCase()];
};
Element.prototype.addClass = function (cn) { /*Adds a class, using classList if available*/
	if(!this.hasClass(cn)){
		if(!!this.classList) {
			this.classList.add(cn);
		}
		else{
			classes = this.className.split(' ');
			classes.push(cn);
			this.className = classes.join(' ');
		}
	}
	return this;
}
Element.prototype.removeClass = function (cn) { /*Removes a class, using classList if available*/
	if(this.hasClass(cn)){
		if(!!this.classList) {
			this.classList.remove(cn);
		}
		else{
			classes = this.className.split(' ');
			classes = classes.not(cn);
			this.className = classes.join(' ');
		}
		return this;
	}
}
Element.prototype.toggleClass = function(a, b){ /*Add/removes a class, or swaps a and be class, using classList if available*/
	if(typeof b === 'undefined'){
		if(!!html.classList){
			this.classList.toggle(a);
		}
		else{
			(this.hasClass(a)) ? this.removeClass(a) : this.addClass(a);
		}
	}
	else{
		(this.hasClass(a)) ? this.removeClass(a) .addClass(b) : this.removeClass(b) .addClass(a);
	}
	return this;
}
Element.prototype.hasClass=function(cn){ /*Does Element have class cn? Uses classList if available*/
	var has;
	(!!document.body.classList) ? has = this.classList.contains(cn) : has = this.className.split(' ').has(cn);
	return has;
}
Object.prototype.toArray = function () { /*Converts an object (nodeList) to an array*/
	/**
	*TODO Use Array methods instead of itterating though all nodes
	*		Ultimately, I would like to extend nodeList prototype to include array methods
	*/
	if (this instanceof zQ) {
		return this;
	}
	var arr = new zQ(),
	i;
	for (let i = 0; i < this.length; i++) {
		arr.push(this[i]);
	}
	return arr;
};
Element.prototype.downloadHTML = function () { /*Creates and returns data: URI of Element's outerHTML*/
	return 'data:text/html,' + encodeURIComponent(this.outerHTML)
};
Element.prototype.ancestorElement = function (tag) { /*Recursive search until parent matches tag*/
	var el;
	(this.parentElement.tagName == tag.toUpperCase()) ? el = this.parentElement : el = this.parentElement.ancestorElement(tag);
	return el;
}
Element.prototype.attr = function (attr, val) { /*Quick and easy set/get Attribute*/
	if (typeof val !== 'undefined') {
		if (val === '' || val === null || val === 'null') {
			this.removeAttribute(attr);
		} 
		else {
			this.setAttribute(attr, val);
		}
		return this;
	} 
	else {
		return this.getAttribute(attr);
	}
}
Element.prototype.edit = function () { /*Toggles contenteditable Attibute of Element*/
	(this.getAttribute('contenteditable')) ? this.attr('contenteditable', null)  : this.attr('contenteditable', true);
}
Object.prototype.fullscreen = function () { /*Toggles fullscreen on this*/
	if (isFullscreen()) {
		closeFullscreen();
	} 
	else {
		(this.requestFullScreen) && this.requestFullScreen() || (this.mozRequestFullScreen) && this.mozRequestFullScreen() || (this.webkitRequestFullScreen) && this.webkitRequestFullScreen() || (this.msRequestFullScreen) && this.msRequestFullScreen() || $(this.addClass('fullscreen'));
	}
	return this;
};
Object.prototype.attrs = function () { /*Returns all properties of this.*/
	return Object.keys(this);
};
String.prototype.toCaps = function () { /*Capitalizes all words in string*/
	var i = 0,
	words = this.toLowerCase() .split(' ');
	words.forEach(function (word) {
		words[i++] = word.charAt(0) .toUpperCase() + word.substring(1);
	});
	words = words.join(' ');
	return words;
};
Element.prototype.magicAJAX = function () { /*Magic buttons and menus using ajax and dataset*/
	$(this) .$('[data-target]') .click(function () {
		var url;
		(this.dataset.url) ? url = this.dataset.url : url = './';
		(typeof this.dataset.request === 'string') ? $(this.dataset.target) .ajax(url, this.dataset.request)  : $(this.dataset.target) .ajax(url);
	});
}
Element.prototype.ajax_submit = function () { /*Submit form using ajax*/
	var action = this.action,
	method = this.method,
	target;
	(this.dataset.target !== 'undefined') ? target = this.dataset.target : target = this.parentElement;
	$(target) .ajax(this.action, this.formData());
}
Element.prototype.formData = function () { /*Get names and values of form's inputs*/
	var inputs = $(this) .$('input[name]:not([type=submit]):not([type=reset]),select'),
	data = [
	],
	val;
	if(!!this.name){
		data.push({
			name: 'form',
			value: this.name
		});
	}
	inputs.forEach(function (input) {
		(input.type === 'checkbox') ? val = input.checked : val = input.value;
		data.push({
			name: input.name,
			value: val
		});
	});
	return data.jsonToURI();
}
Array.prototype.jsonToURI = function () {
	var URI = [
	],
	i;
	this.forEach(function (i) {
		URI.push(encodeURIComponent(i.name) + '=' + encodeURIComponent(i.value));
	});
	return URI.join('&');
}
Array.prototype.end = function() { /*Returns last object in array*/
	return this[this.length - 1];
}
Object.prototype.camelCase = function () {
	return this.toLowerCase() .replace(/\ /g, '-') .replace(/-(.)/g, function (match, group1) {
		return group1.toUpperCase();
	});
}
function $(e) { /*returns querySelectorAll in array format, as a zQ*/
	var results;
	try{
		(typeof e === 'string') ? results = document.querySelectorAll(e).toArray() : results = [e] .toArray() ;
		return results;
	}
	catch(error){
		console.error(error);
		console.error('No results for ' + e);
	}
}
function zQ() { /*Yeah, it's supposed to be kinda like jQuery*/
	/**
	*TODO Create an object containing query, length, results, etc
	*		instead of just using an array
	*		Need to extend nodeList to include array methods
	*/
	Array.call(this);
}
zQ.prototype = [
];
zQ.prototype.constructor = zQ;
zQ.prototype.$ = function (r) { /*$ function using currant instance of zQ instead of document*/
	var el,
	e,
	matches = new zQ();
	this.forEach(function (el) {
		el.querySelectorAll(r) .toArray() .forEach(function (e) {
			matches.push(e);
		});
	});
	return matches;
};
zQ.prototype.get = function (i) { /*Keep as zQ, but pick out a particular object*/
	return $(this[i]);
};
zQ.prototype.each = function (callback) { /*Shorter version of forEach*/
	/**
	*TODO zQ.prototype.each = Array.prototype.forEach
	*/
	var e;
	this.forEach(function (e) {
		callback(e);
	});
	return this;
};
/*===================================================Listener Functions======================================================*/
zQ.prototype.listen = function (event, callback) { /*Listeners, the easy way and with onEvent fallback*/
	/**
	* TODO handle this as a JSON object. $().listen({success:..., fail:...})
	*/
	var e;
	this.forEach(function (e) {
		(html.addEventListener) ? e.addEventListener(event, callback, true) : e['on'+event]=callback;
	});
	return this;
};
/*Listeners per event tyoe*/
zQ.prototype.click = function (callback) {
	return this.listen('click', callback);
};
zQ.prototype.dblclick = function (callback) {
	return this.listen('dblclick', callback);
};
zQ.prototype.contextmenu = function (callback) {
	return this.listen('contextmenu', callback);
};
zQ.prototype.keypress = function (callback) {
	return this.listen('keypress', callback);
};
zQ.prototype.keyup = function (callback) {
	return this.listen('keyup', callback);
};
zQ.prototype.keydown = function (callback) {
	return this.listen('keydown', callback);
};
zQ.prototype.mouseenter = function (callback) {
	return this.listen('mouseenter', callback);
};
zQ.prototype.mouseleave = function (callback) {
	return this.listen('mouseleave', callback);
};
zQ.prototype.mouseover = function (callback) {
	return this.listen('mouseover', callback);
};
zQ.prototype.mouseout = function (callback) {
	return this.listen('mouseout', callback);
};
zQ.prototype.mousemove = function (callback) {
	return this.listen('mousemove', callback);
};
zQ.prototype.mousedown = function (callback) {
	return this.listen('mousedown', callback);
};
zQ.prototype.mouseup = function (callback) {
	return this.listen('mouseup', callback);
};
zQ.prototype.input = function (callback) {
	return this.listen('input', callback);
};
zQ.prototype.change = function (callback) {
	return this.listen('change', callback);
};
zQ.prototype.submit = function (callback) {
	return this.listen('submit', callback);
};
zQ.prototype.reset = function (callback) {
	return this.listen('reset', callback);
};
zQ.prototype.select = function (callback) {
	return this.listen('select', callback);
};
zQ.prototype.focus = function (callback) {
	return this.listen('focus', callback);
};
zQ.prototype.blur = function (callback) {
	return this.listen('blur', callback);
};
zQ.prototype.resize = function (callback) {
	return this.listen('resize', callback);
};
zQ.prototype.updateready = function (callback) {
	return this.listen('updateready', callback);
};
zQ.prototype.ready = function (callback) {
	return this.listen('DOMContentLoaded', callback);
};
zQ.prototype.load = function (callback) {
	return this.listen('load', callback);
};
zQ.prototype.unload = function (callback) {
	return this.listen('unload', callback);
};
zQ.prototype.beforeunload = function (callback) {
	return this.listen('beforeunload', callback);
};
zQ.prototype.abort = function (callback) {
	return this.listen('abort', callback);
};
zQ.prototype.error = function (callback) {
	return this.listen('error', callback);
};
zQ.prototype.scroll = function (callback) {
	return this.listen('scroll', callback);
};
zQ.prototype.playing = function (callback) { /*For Audio/Video*/
	var e;
	this.forEach(function (e) { /*Does not work with listeners. Use onEvent by default*/
		e.onplay = callback;
	});
	return this;
};
zQ.prototype.paused = function (callback) { /*Ditto ^*/
	var e;
	this.forEach(function (e) {
		e.onpause = callback;
	});
	return this;
};
zQ.prototype.pagehide = function (callback) {
	return this.listen('pagehide', callback);
};
zQ.prototype.drag = function (callback) {
	return this.listen('drag', callback);
};
zQ.prototype.visibilitychange = function (callback) { /*Event for tab show/hide*/
	var e,
	pre;
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
zQ.prototype.offline = function (callback) {
	return this.listen('offline', callback);
};
zQ.prototype.online = function (callback) {
	return this.listen('online', callback);
};
zQ.prototype.networkChange = function (callback) {
	return this.online(callback) .offline(callback);
};
zQ.prototype.visibilitychange = function (callback) {
	return this.listen('visibilitychange', callback);
};
zQ.prototype.popstate = function (callback) { /*History.back event*/
	return this.listen('popstate', callback);
};
/*====================================================================================================================*/
zQ.prototype.del = function () { /*Removes array of elements from DOM*/
	var e;
	this.forEach(function (e) {
		e.parentElement.removeChild(e);
	});
	return this;
};
zQ.prototype.swap = function (rep) { /*Change one Node for another*/
	/**
	* TODO Clone instead?
	*/
	var e;
	this.forEach(function (e) {
		e.after(rep);
		$(e) .del();
	});
	return this;
};
zQ.prototype.clone = function () { /*Creates an array of clones for each Node in array*/
	var e,
	clones = [
	];
	this.forEach(function (e) {
		clones.push(e.cloneNode());
	});
	return clones;
};
zQ.prototype.hide = function () { /*Sets hidden attribute to hide*/
	var e;
	this.forEach(function (e) {
		e.setAttribute('hidden', '');
	});
	return this;
};
zQ.prototype.show = function () { /*Removes hidden attribute*/
	var e;
	this.forEach(function (e) {
		e.removeAttribute('hidden');
	});
	return this;
};
zQ.prototype.disable = function () { /*Sets disabled attribute*/
	this.attr('disabled', true)
};
zQ.prototype.enable = function () { /*Removes disabled attribute*/
	this.removeAttr('disabled')
};
zQ.prototype.after = function (content) { /*Insert a node after this*/
	/**
	* TODO Use clones instead?
	* Currently only has an effect on final object in array
	*/
	var e;
	this.forEach(function (e) {
		e.insertAdjacentHTML('afterend', content);
	});
	return this;
};
zQ.prototype.before = function (content) { /* Inserts a node before this*/
	/**
	* TODO See after prototype
	*/
	var e;
	this.forEach(function (e) {
		e.insertAdjacentHTML('beforebegin', content);
	});
	return this;
};
zQ.prototype.append = function(content){
	var e;
	this.forEach(function (e) {
		e.appendChild(content);
	});
};
zQ.prototype.classes = function () { /*Returns classList (if supported) or an array of classes*/
	var hasClasses;
	(!!document.body.classList) ? hasClass = this[0].classList.toArray() : hasClass = this[0].className.split(' ');
	return hasClass;
};
zQ.prototype.ancestor = function (tag) { /*Recursive seearch for this.parentElement of tagName of tag*/
	return this[0].ancestorElement(tag);
};
zQ.prototype.attr = function (attr, val) {
	var e,
	results = [
	];
	if (typeof val === 'undefined') {
		this.forEach(function (e) {
			results.push(e.attr(attr));
		});
	} 
	else {
		this.forEach(function (e) {
			results.push(e.attr(attr, val));
		});
	}
	return results.toArray();
}
zQ.prototype.removeAttr = function (attr) {
	var e;
	this.forEach(function (e) {
		e.removeAttribute(attr);
	})
};
zQ.prototype.dataset = function (attr, val) { /*Use dataset, or use setAttribute if needed.*/
	var e,
	results = [
	];
	if (typeof val == 'undefined') {
		if (!document.body.dataset) {
			this.forEach(function (e) {
				results.push(e.attr('data-' + attr));
			});
		} 
		else {
			this.forEach(function (e) {
				results.push(e.dataset[attr.camelCase()]);
			});
		}
	} 
	else {
		if (!document.dataset) {
			this.forEach(function (e) {
				results.push(e.attr('data-' + attr, val));
			});
		} 
		else {
			this.forEach(function (e) {
				results.push(e.dataset[attr.camelCase()] = val);
			});
		}
	}
	return results.toArray();
}
zQ.prototype.addClass = function (cName) { /*Addes a class to all nodes in array*/
	var e;
	this.forEach(function (e) {
		e.addClass(cName);
	});
	return this;
};
zQ.prototype.removeClass = function (cName) { /*Removes a class from all nodes in array*/
	var e;
	this.forEach(function(e){
		e.removeClass(cName);
	});
	return this;
};
zQ.prototype.toggleClass = function (a, b) {/*Adds/removes a class from all nodes in array, depending on if it already has it*/
	if (typeof b === 'undefined') {
		b = '';
	}
	var e;
	this.forEach(function (e) {
		e.toggleClass(a, b);
	});
	return this;
}
zQ.prototype.hasClass = function (cn) { /*Check if any node has className containing cn*/
	var has = false,
	e;
	cn = '.' + cn.split(' ') .join('.');
	this.forEach(function (e) {
		if (!has && e.hasClass(cn)) {
			has = true;
		}
	});
	return has;
};
zQ.prototype.text = function(str){ /*Sets textContent for all nodes in array*/
	var e;
	this.forEach(function(e){
		e.textContent=str;
	});
}
zQ.prototype.edit = function () { /*Sets contenteditable attribute for all nodes in array*/
	var e;
	this.forEach(function (e) {
		e.edit();
	});
	return this;
};
zQ.prototype.html = function (html) { /*Sets innerHTML for all nodes in array*/
	var e;
	this.forEach(function (e) {
		e.innerHTML = html;
	});
	return this;
};
zQ.prototype.parent = function () { /*Returns an array of each node's parentElement*/
	var p = [
	],
	e;
	this.forEach(function (e) {
		p.push(e.parentElement);
	});
	return p.toArray();
};
zQ.prototype.children = function () { /*Might as well do this.(>*), but not sure that would work*/
	var c = [
	],
	e,
	a;
	this.forEach(function (e) {
		e.childNodes.toArray() .forEach(function (a) {
			if (typeof a.tagName === 'string') {
				c.push(a);
			}
		});
	});
	return c.toArray();
};
zQ.prototype.siblings = function () { /*Returns an array of all sibling nodes for each of these*/
	var sibs = [
	],
	e;
	this.forEach(function (e) {
		sibs = sibs.concat($(e.parentElement) .drop(e));
	});
	return sibs;
};
zQ.prototype.remove = function (i) { /*Removes "stuff" from the array*/
	return this.drop(i);
};
zQ.prototype.ajaxForms = function () { /*Use ajax to submit all forms in this*/
	var form;
	this.submit(function (event) {
		event.preventDefault();
		event.target.ajax_submit();
	});
	return this;
}
zQ.prototype.ajax = function (url, request, type, async) { /*Makes ajax requests*/
	/**
	* TODO Make it take an object instead of a series of arguments
	* $().ajax({
	* 	url:...
	* 	request:...,
	* 	type: get|post,
	* 	async: true|false,
	* 	success:callback,
	* 	failure:callback
	* })
	
	* Also, enable setting variables instead of just innerHTML
	*/
	/*Generic AJAX requests. Return becomes content of element*/
	var e,
	ajax,
	hist={};
	this.observe({
		childList: true,
		attributes: true
	});
	if (typeof type !== 'string') {
		type = 'POST';
	}
	if (typeof async !== 'boolean') {
		async = true;
	}
	if (window.XMLHttpRequest) {
		ajax = new XMLHttpRequest();
	} 
	else {
		ajax = new ActiveXObject('Microsoft.XMLHTTP');
	}
	this.forEach(function (e) {
		ajax.onreadystatechange = function () {
			if (ajax.readyState == 3) {
				$(e) .addClass('loading');
			}
			if (ajax.readyState == 4) {
				$(e) .removeClass('loading');
				switch (ajax.status) {
				case 200:
					history.pushState(hist, '', url);
					var content = ajax.responseText;
					$(e) .html(content);
					e.magicAJAX();
					$(e) .$('form') .ajaxForms();
					break;
				default:
					console.error('AJAX request to ' + url + ' returned status code: ' + ajax.status + ' ' + ajax.statusText);
				}
			}
		};
		ajax.open(type, url, async);
		ajax.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		if (request && (typeof request === 'string')) {
			ajax.send(request);
		} 
		else {
			ajax.send();
		}
	});
	return this;
};
zQ.prototype.observe = function (observes) { /*Create Mutation observers*/
	/**
	*TODO Allow callbacks for each type of observes
	*/
	if (typeof observes === 'undefined') {
		observes = {
			attributes: true,
			childList: true,
			characterData: true,
			subtree: true,
			attributeOldValue: true
		};
	}
	var target,
	observer = new MutationObserver(function (mutations) { /*https://developer.mozilla.org/en-US/docs/Web/API/MutationObserver*/
		mutations.forEach(function (mutation) {
			/*mutation.addedNodes | attributeName | attributeNamespace | nextSibling |oldValue | previousSibling | removedNodes | target | type*/
			switch (mutation.type) {
			case 'childList':
					$(mutation.target) .$('form[data-target]').ajaxForms();
					observer.disconnect();
					break;
				break;
			case 'attributes':
				console.log(mutation.type + ': ' + mutation.attributeName + ' from ' + mutation.oldValue);
				break;
			default:
				console.log(mutation.type + ' ' + mutation.target.innerHTML);
			}
		});
	});
	this.forEach(function (target) {
		observer.observe(target, observes);
	});
	return this;
};
zQ.prototype.ajaxLoaded = function () { /*Because ajax has no 'onload'*/
	this.$('button[data-target],menuitem[data-target]') .click(function () {
		this.magicAJAX()
	});
	this.$('td input.sql') .change(function () {
		data = this.sqlTable(),
		url = 'ajax.php',
		request = 'table=' + data.table + '&id=' + encodeURIComponent(data.id) + '&name=' + encodeURIComponent(data.name) + '&value=' + encodeURIComponent(data.value);
		$('code.sql') .ajax(url, request);
	});
}
zQ.prototype.formValues = function () { /*Get values from this form*/
	/**
	* TODO Stop using this
	* I have other methods to acheive the same effect
	* Also, at least verify that this is a form
	*/
	var results = new zQ(),
	inputs = this.$('input:not([type=submit]):not([type=reset]),select,textarea'),
	val;
	inputs.forEach(function (input) {
		if (input.name && input.value) {
			(input.type === 'checkbox') ? val = input.checked : val = input.value;
			results.push(input.name + '=' + val);
		}
	});
	return results;
};
zQ.prototype.getChecked = function () { /*Get names of all checked checkboxes for this (form|fieldset)*/
	/*where this is form or fieldset*/
	var i,
	f,
	values,
	results = [
	];
	this.forEach(function (f) {
		values = [
		];
		$(f) .$('input[type=checkbox]') .forEach(function (i) {
			if (i.checked && i.name) {
				values.push(i.name);
			}
		});
		results.push(f.name + '=' + values.join(','));
	});
	return results.join('&');
};
zQ.prototype.interval = function (callback, timing) { /*setInterval on an a node array*/
	var e;
	this.forEach(function (e) {
		e.setInterval(callback, timing);
	});
};
zQ.prototype.create = function (args) { /*Creates new element from an object (args)*/
	/**
	*TODO Handle the object better. Not very useful just yet
	*/
	if (typeof args === 'string') {
		el = document.createElement(args);
	} 
	else if (args.tagName) {
		var el = document.createElement(args.tagName),
		arg;
		args.attrs() .forEach(function (arg) {
			el[arg] = args[arg];
		});
	}
	if (el) {
		this.forEach(function (e) {
			e.appendChild(el);
		});
	}
	return el;
};
zQ.prototype.css = function (args) { /*Set style using CSS syntax*/
	var n,
	e,
	value = [
	];
	args = args.replace('; ', ';') .replace(': ', ':') .replace('float', 'cssFloat') .split(';');
	for (let i = 0; i < args.length; i++) {
		value[i] = args[i].slice(args[i].indexOf(':') + 1);
		args[i] = args[i].slice(0, args[i].indexOf(':'));
		while (args[i].indexOf('-') !== - 1) {
			n = args[i].indexOf('-');
			args[i] = args[i].substring(0, n) + args[i].charAt(n + 1) .toUpperCase() + args[i].substring(n + 2, args[i].length) .replace(' ', '');
		}
	}
	for (let i = 0; i < args.length; i++) {
		this.forEach(function (e) {
			e.style[args[i]] = value[i];
		});
	}
	return this;
};
zQ.prototype.setPattern = function (type) { /*Sets the pattern attribute for inputs based on type*/
	/**
	*TODO Check that this is an input
	* TODO use the pattern method instead of switch to avoid duplicate code
	*/
	var pattern;
	switch(type){
		case "text":
			pattern = "(\\w+(\\ )?)+";
			break;
		case "name":
			pattern = "[A-z]+(( [A-z]+){1,2})?";
			break;
		case "password":
			pattern = "(?=^.{8,35}$)((?=.*\\d+)|(?=.*\\W+))(?![.\\n])(?=.*[A-Z])(?=.*[a-z]).*$";
			break;
		case "email": 
			pattern = ".+@.+\\.+[\\w]+";
			break;
		case "url":
			pattern = "http[s]?://[\\S]+\.[\\S]+";
			break;
		case "tel":
			pattern = "([+]?[1-9][-]?)?((\\([\\d]{3}\\))|(\\d{3}[-]?))\\d{3}[-]?\\d{4}";
			break;
		case "number":
			pattern = "\\d+(\\.\\d+)?";
			break;
		case "color":
			pattern = "#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})";
			break;
		case "date":
			pattern = "(\\d{4}-\\d{2}-\\d{2})|(\\d{2}\\/\\d{2}\\/\\d{4})";
			break;
		case "time":
			pattern = "(([0-1]?\\d)|(2[0-3])):[0-5]\\d(:[0-5]\\d)?";
			break;
		case "datetime":
			pattern = "([0-2]\\d{3})\-([0-1]\\d)\-([0-3]\\d)T([0-5][0-9])\\:([0-5]\\d)\:([0-5]\\d)(Z|([\\-\\+]([0-1]\\d)\\:\\d{2}))";
			break;
		case "credit":
			pattern = "\\d{13,16}";
			break;
		default: pattern = type;
	}
	(pattern.length !== 0) ? this.attr('pattern', '^' + pattern.replace(/(^\^|\$$)/g,'') + '$'.replace(/(^\/|\/$)/g, ''))  : null;
	return this;
};
zQ.prototype.pause = function () { /*Pauses playback of Audio/Video*/
	var e;
	this.forEach(function (e) {
		e.pause()
	});
}
zQ.prototype.play = function () { /*REsumes/begins playback of Audio/Video*/
	var e;
	this.forEach(function (e) {
		e.play()
	});
}
function pattern(type){ /*Creates a regular expression based on type*/
	var pattern;
	switch(type){
		case "text":
			pattern = "(\w+(\ )?)+";
			break;
		case "name":
			pattern = "[A-Za-z]{3,30}";
			break;
		case "password":
			pattern = "(?=^.{8,35}$)((?=.*\d+)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$";
			break;
		case "email": 
			pattern = ".+@.+\.+[\w]+";
			break;
		case "url":
			pattern = "http[s]?://[\S]+\.[\S]+";
			break;
		case "tel":
			pattern = "([+]?[1-9][-]?)?((\([\d]{3}\))|(\d{3}[-]?))\d{3}[-]?\d{4}";
			break;
		case "number":
			pattern = "\d+(\.\d+)?";
			break;
		case "color":
			pattern = "#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})";
			break;
		case "date":
			pattern = "((((0?)[1-9])|(1[0-2]))(-|/)(((0?)[1-9])|([1-2][\d])|3[0-1])(-|/)\d{4})|(\d{4}-(((0?)[1-9])|(1[0-2]))-(((0?)[1-9])|([1-2][\d])|3[0-1]))";
			break;
		case "time":
			pattern = "(([0-1]?\d)|(2[0-3])):[0-5]\d";
			break;
		case "datetime":
			pattern = "([0-2]\d{3})\-([0-1]\d)\-([0-3]\d)T([0-5][0-9])\:([0-5]\d)\:([0-5]\d)(Z|([\-\+]([0-1]\d)\:\d{2}))";
			break;
		case "credit":
			pattern = "\d{13,16}";
			break;
		default: pattern = null;
	}
	return new RegExp('^' + pattern.replace(/(^\^|\$$)/g,'') + '$');
}
function convertDate(date) { /*converts a date into Y-m-d format*/
	/**
	*[TODO]Enable this to work with more date formats.
	*/
	var year,
	month,
	day;
	date = date.replace(/\//g, '-') .split('-');
	if (date[0].length === 4) {
		year = date[0];
		month = date[1];
		day = date[2];
	} 
	else {
		month = date[0];
		day = date[1];
		year = date[2];
	}
	if (!isNaN(month) && !isNaN(day) && !isNaN(year)) {
		if (month.length === 1) {
			month = '0' + month;
		}
		if (day.length === 1) {
			day = '0' + day;
		}
		if (year.length === 2) {
			year = '20' + year;
		}
		date = year + '-' + month + '-' + day;
		return date;
	}
	return false;
}
function convertTime(time) { /*Convert standard to military time, and modify for */
	var h = parseInt(time.slice(0, time.indexOf(':')));
	if (h === 0) {
		h = 12;
	}
	/*Requires a select with options AM & PM with values 0 & 12 by id 'AMPM'*/

	h = String(h + parseInt($('#AMPM') .options[$('#AMPM') .selectedIndex].value) + (12 * (Math.ceil(h / 12 % 1) - 1)));
	/*/Hours, plus 12 if PM, -12 if perfectly divisible by 12... for military time, 00=12 AM & 12=12 PM*/
	var m = String(time.slice(time.indexOf(':') + 1));
	if (h.length === 1) {
		h = '0' + h;
	}
	if (m.length === 1) {
		m = '0' + m;
	}
	if (h <= 23 && m <= 59) {
		return h + ':' + m;
	}
	return false;
}
function getForm(form) { /*Old function to verify form and get form data*/
	/* Reads all form values and returns GET/POST format*/
	if (verifyForm(form)) {
		var formData = '';
		var fields = document.forms[form];
		for (let i = 0; i < fields.length; i++) {
			if (fields[i].type !== 'submit' && fields[i].type !== 'reset' && fields[i].value) {
				/*/Avoid capturing non-input data*/
				formData += '&' + fields[i].name + '=' + encodeURI(fields[i].value);
			}
		}
		formData = formData.slice(1);
		/*Removes leading '&'*/
		console.log(formData);
		return formData;
	}
	return false;
}
Element.prototype.validate = function () { /*Attempting to add HTML5 form validation to unsupported browsers*/
	/**
	*TODO high and low might be for a date/time as well as number
	*		Need a good method of converting all varieties of dates to use this.
	*/
	var hasData = true,
	matches = true,
	low = false,
	high = false,
	validity;
	if ((typeof this.required !== 'boolean') && (!!this.hasAttribute('required'))) {
		hasData = (!!this.value);
		(hasData) ? console.log('Required met')  : console.log('Required not met');
	}
	if ((typeof this.pattern !== 'string') && (!!this.getAttribute('pattern')) && (!!this.value)) {
		matches = new RegExp('^' + this.getAttribute('pattern').replace(/(^\^|\$$)/g,'') + '$') .test(this.value);
		(matches) ? console.log('Matches pattern')  : console.log('Doesn\'t match pattern');
	}
	if ((typeof this.max !== 'string') && (!!this.hasAttribute('max'))) {
		high = (parseFloat(this.value) > parseFloat(this.getAttribute('max')));
		(!high) ? console.log('Max met')  : console.log('Max not met');
	}
	if ((typeof this.min !== 'string') && (!!this.hasAttribute('min'))) {
		low = (parseFloat(this.value) < parseFloat(this.getAttribute('min')));
		(!low) ? console.log('Min met')  : console.log('Min not met');
	}
	validity = hasData && matches && !low && !high;
	(validity) ? this.removeClass('invalid')  : this.addClass('invalid');
	return validity;
}
function fullscreenElement() { /*Returns boolean value for if something is fullscreen or not*/
	return document.fullscreenElement || document.mozFullScreenElement || document.webkitFullscreenElement || false;
}
function rand(low, high) { /*Random number generator... accepts a range, which is defaulted to 1, 6*/
	if (typeof low !== 'number') {
		low = 1;
	}
	if (typeof high !== 'number') {
		high = low + 5;
	}
	if (low > high) {
		console.error('Bad min/max for roll()');
		return 0;
	}
	return (low + Math.floor(Math.random() * (1 + high - low)));
}
function toggleFullscreen(e) { /*Opens/closes fullscreen*/
	(isFullscreen()) ? fullscreen(e)  : closeFullscreen(e);
}
function closeFullscreen() {
	if (document.requestFullScreen) {
		document.requestFullScreen();
	} 
	else if (document.mozCancelFullScreen) {
		document.mozCancelFullScreen();
	} 
	else if (document.webkitCancelFullScreen) {
		document.webkitCancelFullScreen();
	} 
	else if (document.msCancelFullScreen) {
		document.msCancelFullScreen();
	} 
	else {
		$(fullscreenElement) .removeClass('fullscreen');
	}
}
function isFullscreen() {/*Returns boolean value for if something is fullscreen*/
	var e = document.fullscreenElement || document.mozFullScreenElement || document.webkitFullscreenElement || $('.fullscreen') [0],
	fullscreen;
	((e)) ? fullscreen = true : fullscreen = false;
	return fullscreen;
}
Object.prototype.keyPressed = function() { /*Converts keycode from event.keyPressed to keyChar*/
	var char;
	switch (this) {
	case 9:
		char = 'Tab';
		break
	case 17:
		char = 'Control'
	case 40:
		char = 'Down';
	case 18:
		char = 'Alt';
		break;
	case 20:
		char = 'Caps';
		break;
	case 16:
		char = 'Shift';
		break;
	case 39:
		char = 'Right';
		break;
	case 37:
		char = 'Left';
		break;
	case 32:
		char = 'Space';
		break;
	case 27:
		char = 'Escape';
		break;
	default:
		char = String.fromCharCode(this);
	}
	return char;
}
function online() { /*Boolean for if there is a network connection*/
	var connect;
	(!!navigator.onLine) ? connect = navigator.onLine : connect = true;
	return connect;
}
function keyListeners() { /*Old function... listen for all key presses*/
	if (window.addEventListener) {
		window.addEventListener('keydown', function (event) {
			notify({
				title:'Pressed',
				body:event.keyCode.keyPressed()
			});
		}, false);
	}
}
Object.prototype.isArray = function () { /*Return false becase arrays will be handled by Array.prototype.isArray*/
	return false;
}
Array.prototype.isArray = function () { /*Inverse of above*/
	return true;
}
String.prototype.isString = function () { /*Similar logic to above, but for strings*/
	return true;
}
Object.prototype.isString = function () {/*...*/
	return false;
}
function GPS() { /*Saves GPS data to sessionStorage*/
	/**
	*TODO Find a way of returning GPS data instead of saving to sessionStorage
	*/
	if (navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(function (pos) {
			gps = pos.coords;
			console.log(gps);
			sessionStorage.latitude = gps.latitude;
			sessionStorage.longitude = gps.longitude;
			sessionStorage.speed = gps.speed;
			sessionStorage.heading = gps.heading;
			sessionStorage.altitude = gps.altitude;
			sessionStorage.accuracy = gps.accuracy;
			$(html) .dataset('longitude', gps.longitude) .dataset('latitude', gps.latitude);
			notify({
				title: 'Location',
				body: 'Latitude: ' + gps.latitude + '\nLongitude: ' + gps.longitude,
				icon: '/images/Icons/map.png'
			});
		});
	}
}
function supports(type) { /*Feature detection. Returns boolean value of suport for type*/
	/**
	* A series of tests to determine support for a given feature
	* Defaults to testing support for an element of tag (type)
	* Which works by testing if the browser considers it unknown element type
	*/
	var supports,
	prefixes = [/*Array of vendor prefixes*/
		'',
		'moz',
		'webkit',
		'ms',
		'o'
	],
	/*Shorten for CSS properties*/
	style = document.documentElement.style;
	switch (type.toLowerCase()) {
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
		supports = (!!window.CSSSupportsRule);
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
	case 'test':
		supports = (!!window.DNE);
		break;
	default:
		supports = (document.createElement(type.toLowerCase()) .toString() !== document.createElement('DNE').toString());
	}
	return supports;
}
function notify(options) { /*Creates a notification, with alert fallback*/
	/**
	*TODO Directly create notification instead of defining function by which method is supported
	*/
	var title, sendNotification;
	if (typeof options === 'string') {
		options = {
			body: options
		};
	}(options.title) ? title = options.title : title = document.title + ' says:';
	(!options.body) ? options.body = '' : null;
	(!options.icon) ? options.icon = $('link[rel=icon]')[0].href : null;
	if ('Notification' in window) {
		if(Notification.permission.toLowerCase() === 'default'){
			Notification.requestPermission(function(){
				(Notification.permission.toLowerCase() === 'granted') ? notify(options) : alert(title + '\n' +options.body);
			});
		}
		sendNotification = function (title, options) {
			return new Notification(title, options);
		};
	} else if ('mozNotification' in navigator) {
		sendNotification = function (title, options) {
			return navigator.mozNotification.createNotification(title, options.body, options.icon) .show();
		};
	}
	else if('notifications' in window) {
		if(window.notifications.checkPermission != 1){
			window.notifications.requestPermission();
		}
		sendNotification = function(title, options){
			return window.notifications.createNotification(options.icon, options.title, options.body). show();
		}
	} else {
		sendNotification = function (title, options) {
			alert(title + '\n' + options.body);
		};
	}
	var notification = sendNotification(title, options);
	if (!!notification) {
		(!!options.onclick) ? notification.onclick = options.onclick : null;
		(!!options.onshow) ? notification.onshow = options.onshow : null;
		(!!options.onclose) ? notification.onclose = options.onclose : null;
		(!!options.onerror) ? notification.onerror = options.onerror : notification.onerror=function(error){console.error(error)};
		return notification;
	}
};
/*AppCache updater*/
$(window) .load(function (e) { /*Check for appCache updates if there is a manifest set*/
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
