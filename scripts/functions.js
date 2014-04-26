'use strict';
var body = document.body,
html = document.documentElement;
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
			return ($(sel) .has(this));
		}
	}
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
/*===================================================Listener Functions======================================================*/

NodeList.prototype.listen = function (event, callback) {
	/*Listeners, the easy way and with onEvent fallback*/
	/**
	* TODO handle this as a JSON object. $().listen({success:..., fail:...})
	*/
	var e;
	this.each(function (e) {
		(html.addEventListener) ? e.addEventListener(event, callback, true)  : e['on' + event] = callback;
	});
	return this;
};
/*Listeners per event tyoe*/
NodeList.prototype.click = function (callback) {
	return this.listen('click', callback);
};
NodeList.prototype.dblclick = function (callback) {
	return this.listen('dblclick', callback);
};
NodeList.prototype.contextmenu = function (callback) {
	return this.listen('contextmenu', callback);
};
NodeList.prototype.keypress = function (callback) {
	return this.listen('keypress', callback);
};
NodeList.prototype.keyup = function (callback) {
	return this.listen('keyup', callback);
};
NodeList.prototype.keydown = function (callback) {
	return this.listen('keydown', callback);
};
NodeList.prototype.mouseenter = function (callback) {
	return this.listen('mouseenter', callback);
};
NodeList.prototype.mouseleave = function (callback) {
	return this.listen('mouseleave', callback);
};
NodeList.prototype.mouseover = function (callback) {
	return this.listen('mouseover', callback);
};
NodeList.prototype.mouseout = function (callback) {
	return this.listen('mouseout', callback);
};
NodeList.prototype.mousemove = function (callback) {
	return this.listen('mousemove', callback);
};
NodeList.prototype.mousedown = function (callback) {
	return this.listen('mousedown', callback);
};
NodeList.prototype.mouseup = function (callback) {
	return this.listen('mouseup', callback);
};
NodeList.prototype.input = function (callback) {
	return this.listen('input', callback);
};
NodeList.prototype.change = function (callback) {
	return this.listen('change', callback);
};
NodeList.prototype.submit = function (callback) {
	return this.listen('submit', callback);
};
NodeList.prototype.reset = function (callback) {
	return this.listen('reset', callback);
};
NodeList.prototype.select = function (callback) {
	return this.listen('select', callback);
};
NodeList.prototype.focus = function (callback) {
	return this.listen('focus', callback);
};
NodeList.prototype.blur = function (callback) {
	return this.listen('blur', callback);
};
NodeList.prototype.resize = function (callback) {
	return this.listen('resize', callback);
};
NodeList.prototype.updateready = function (callback) {
	return this.listen('updateready', callback);
};
NodeList.prototype.ready = function (callback) {
	return this.listen('DOMContentLoaded', callback);
};
NodeList.prototype.load = function (callback) {
	return this.listen('load', callback);
};
NodeList.prototype.unload = function (callback) {
	return this.listen('unload', callback);
};
NodeList.prototype.beforeunload = function (callback) {
	return this.listen('beforeunload', callback);
};
NodeList.prototype.abort = function (callback) {
	return this.listen('abort', callback);
};
NodeList.prototype.error = function (callback) {
	return this.listen('error', callback);
};
NodeList.prototype.scroll = function (callback) {
	return this.listen('scroll', callback);
};
NodeList.prototype.playing = function (callback) {
	/*For Audio/Video*/
	var e;
	this.forEach(function (e) {
		/*Does not work with listeners. Use onEvent by default*/
		e.onplay = callback;
	});
	return this;
};
NodeList.prototype.paused = function (callback) {
	/*Ditto ^*/
	var e;
	this.forEach(function (e) {
		e.onpause = callback;
	});
	return this;
};
NodeList.prototype.pagehide = function (callback) {
	return this.listen('pagehide', callback);
};
NodeList.prototype.drag = function (callback) {
	return this.listen('drag', callback);
};
NodeList.prototype.visibilitychange = function (callback) {
	/*Event for tab show/hide*/
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
NodeList.prototype.offline = function (callback) {
	return this.listen('offline', callback);
};
NodeList.prototype.online = function (callback) {
	return this.listen('online', callback);
};
NodeList.prototype.networkChange = function (callback) {
	return this.online(callback) .offline(callback);
};
NodeList.prototype.visibilitychange = function (callback) {
	return this.listen('visibilitychange', callback);
};
NodeList.prototype.popstate = function (callback) {
	/*History.back event*/
	return this.listen('popstate', callback);
};
Object.prototype.$ = function (e) {
	return this.queurySelectorAll(e);
}
/*====================================================================================================================*/

Object.prototype.log = function () {
	/*Use instead of console.log(this)*/
	console.log(this);
	return this;
}
Object.prototype.isaN = function () {
	/*Boolean... is this a number?*/
	return parseFloat(this) == this;
}
Element.prototype.after = function (content) {
	/*Insert a node after this*/
	/**
	* TODO Use clones instead?
	* Currently only has an effect on final object in array
	*/
	this.insertAdjacentHTML('afterend', content);
	return this;
};
Element.prototype.before = function (content) {
	/* Inserts a node before this*/
	/**
	* TODO See after prototype
	*/
	this.insertAdjacentHTML('beforebegin', content);
	return this;
};
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
		console.log(data);
	});
}
NodeList.prototype.each = Array.prototype.forEach;
NodeList.prototype.indexOf = Array.prototype.indexOf;
function $(e) {
	/*returns querySelectorAll in array format, as a zQ*/
	var results;
	try {
		(typeof e === 'string') ? results = document.querySelectorAll(e)  : results = [
			e
		];
		return results;
	}
	catch (error) {
		console.error(error);
		console.error('No results for ' + e);
	}
}
Element.prototype.values = function () {
	var inputs = this.querySelectorAll('input:not([type=submit]):not([type=reset]),select,textarea'),
	results = [
		'form=' + this.name
	],
	val;
	inputs.each(function (input) {
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
	var title,
	sendNotification;
	if (typeof options === 'string') {
		options = {
			body: options
		};
	}(options.title) ? title = options.title : title = document.title + ' says:';
	(!options.body) ? options.body = '' : null;
	(!options.icon) ? options.icon = $('link[rel=icon]') [0].href : null;
	if ('Notification' in window) {
		if (Notification.permission.toLowerCase() === 'default') {
			Notification.requestPermission(function () {
				(Notification.permission.toLowerCase() === 'granted') ? notify(options)  : alert(title + '\n' + options.body);
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
	else if ('notifications' in window) {
		if (window.notifications.checkPermission != 1) {
			window.notifications.requestPermission();
		}
		sendNotification = function (title, options) {
			return window.notifications.createNotification(options.icon, options.title, options.body) .show();
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
		(!!options.onerror) ? notification.onerror = options.onerror : notification.onerror = function (error) {
			console.error(error)
		};
		return notification;
	}
};
/*AppCache updater*/
//$(window) .load(function (e) { /*Check for appCache updates if there is a manifest set*/
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
	var supports,
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
		case 'promises':
			supports = ('Promise' in window);
			break;
		case 'ajax':
			supports = ('XMLHttpRequest' in window);
			break;
		case 'cssvars':
			supports = (!!CSS.supports('var-x','x'));
			break;
		default:
			supports = (document.createElement(type.toLowerCase()) .toString() !== document.createElement('DNE') .toString());
	}
	return supports;
}
