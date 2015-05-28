/*Cannot rely on $(window).load() to work, so use this instead*/
window.addEventListener('load', function()
{
	"use strict";
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
	'workers', 'promises', 'ajax', 'FormData'].forEach(function(feat)
	{
		document.documentElement.classList.pick(feat, 'no-' + feat, supports(feat));
	});
	document.documentElement.classList.pick('offline', 'online', (supports('connectivity') && !navigator.onLine));
	setTimeout(
		function()
		{
			body.results.bootstrap();
		}, 100
	);
	body.watch({
		childList: function()
		{
			this.addedNodes.bootstrap();
		},
		attributes: function()
		{
			switch (this.attributeName) {
				case 'contextmenu':
					var menu = this.target.attr('contextmenu');
					if (this.oldValue !== '') {
						$('menu#' + this.oldValue).delete();
					}
					if (menu && menu !== '') {
						if (! $('menu#'+ menu).found) {
							ajax({
								url: document.baseURI,
								request: 'load_menu=' + menu.replace(/\_menu$/ ,''),
								cache: this.target.data('cache')
							}).then(
								handleJSON,
								function(err)
								{
									console.error(err);
								}
							);
						}
					}
					break;

				case 'contextmenu':
					if (this.oldValue !== '') {
						$('menu#' + this.oldValue).delete();
					}
					break;

				case 'open':
					if (
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
					if (this.oldValue !== '') {
						this.target.addEventListener('click', function()
						{
							if (! this.data('confirm') || confirm(this.data('confirm'))) {
								ajax({
									url: this.data('url')|| document.baseURI,
									request: (this.data('prompt'))
										? this.data('request') + '&prompt_value=' + encodeURIComponent(prompt(this.data('prompt')))
										: this.data('request'),
									cache: el.data('cache')
								}).then(
									handleJSON,
									function(err)
									{
										console.error(err);
									}
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
	$(window).networkChange(function()
	{
		$('html').toggleClass('online', navigator.onLine).toggleClass('offline', !navigator.onLine);
	}).online(function()
	{
		$('fieldset').each(function(fieldset)
		{
			fieldset.removeAttribute('disabled');
		});
		notify({
			title: 'Network:',
			body: 'online',
			icon: 'images/icons/network-server.png'
		});
	}).offline(function()
	{
		$('fieldset').each(function(fieldset)
		{
			fieldset.disabled = true;
		});
		notify({
			title: 'Network:',
			body: 'offline',
			icon: 'images/icons/network-server.png'
		});
	});
	if (!sessionStorage.hasOwnProperty('nonce')) {
		ajax({
			request: 'request=nonce'
		}).then(
			handleJSON,
			function(err)
			{
				console.error(err);
			}
		);
	}
});
NodeList.prototype.bootstrap = function()
{
	"use strict";
	this.forEach(function(node)
	{
		if (node.nodeType !== 1) {
			return this;
		}
		if (! supports('details')) {
			node.query('details > summary').forEach(function(details)
			{
				details.addEventListener('click', function() {
					if (this.parentElement.hasAttribute('open')) {
						this.parentElement.removeAttribute('open');
					} else {
						this.parentElement.setAttribute('open', '');
					}
				});
			});
		}
		if (supports('menuitem')) {
			node.query('[contextmenu]').forEach(function(el)
			{
				var menu = el.attr('contextmenu');
				if (menu && menu !== '') {
					if (! $('menu#'+ menu).found) {
						ajax({
							url: document.baseURI,
							request: 'load_menu=' + menu.replace(/\_menu$/ ,''),
							cache: el.data('cache')
						}).then(
							handleJSON,
							function(err)
							{
								console.error(err);
							}
						);
					}
				}
			});
		}
		if (supports('datalist')) {
			node.query('[list]').forEach(function(list)
			{
				if (!$('#' + list.getAttribute('list')).found) {
					ajax({
						request: 'datalist=' + list.getAttribute('list'),
						type: 'POST'
					}).then(
						handleJSON,
						function(err)
						{
							console.error(err);
						}
					);
				}
			});
		}
		if (! supports('picture')) {
			node.query('picture').forEach(function(picture)
			{
				if ('matchMedia' in window) {
					var sources = picture.querySelectorAll('source[media][srcset]');
					for (var n = 0; n < sources.length; n++) {
						if (matchMedia(sources[n].getAttribute('media')).matches) {
							picture.getElementsByTagName('img')[0].src = sources[n].getAttribute('srcset');
							break;
						}
					}
				} else {
					picture.getElementsByTagName('img')[0].src = picture.querySelector('source[media][srcset]').getAttribute('srcset');
				}
			});
		}
		node.query('[autofocus]').forEach(function(input)
		{
			input.focus();
		});
		node.query('a[href]:not([target="_blank"]):not([download]):not([href*="\#"])').filter(
			isInternalLink
		).forEach(function(a)
		{
			a.addEventListener('click', function(event)
			{
				event.preventDefault();
				if (typeof ga === 'function') {
					ga('send', 'pageview', this.href);
				}
				ajax({
					url: this.href,
					type: 'GET',
					history: this.href,
					cache: this.data('cache')
				}).then(
					handleJSON,
					function(err)
					{
						console.console.error(err);
					}
				);
			});
		});
		node.query('form[name]').forEach(function(form)
		{
			form.addEventListener('submit', function(event)
			{
				event.preventDefault();
				if (! this.data('confirm') || confirm(this.data('confirm'))) {
					ajax({
						url: this.action || document.baseURI,
						type: this.method || 'POST',
						contentType: this.enctype,
						form: this
					}).then(
						handleJSON,
						function(err)
						{
							console.error(err);
						}
					);
				}
			});
			if (form.name === 'new_post') {
				var retain = setInterval(function()
				{
					ajax({
						url: document.baseURI,
						request: 'action=keep-alive'
					}).then(
						handleJSON,
						function(err)
						{
							console.error(err);
						}
					);
				}, 60000);
				form.addEventListener('submit', function()
				{
					clearInterval(retain);
				});
			}
		});
		node.query('[data-show]').forEach(function(el)
		{
			el.addEventListener('click', function()
			{
				document.querySelector(this.data('show')).show();
			});
		});
		node.query('[data-show-modal]').forEach(function(el)
		{
			el.addEventListener('click', function()
			{
				document.querySelector(this.data('show-modal')).showModal();
			});
		});
		node.query('[data-scroll-to]').forEach(function(el)
		{
			el.addEventListener('click', function()
			{
				document.querySelector(this.data('scroll-to')).scrollIntoView();
			});
		});
		node.query('[data-import]').forEach(function(el)
		{
			el.HTMLimport();
		});
		node.query('[data-close]').forEach(function(el)
		{
			el.addEventListener('click', function()
			{
				document.querySelector(this.data('close')).close();
			});
		});
		node.query('fieldset button[type=button].toggle').forEach(function(toggle)
		{
			toggle.addEventListener('click', function()
			{
				this.ancestor('fieldset').querySelectorAll('input[type=checkbox]').forEach(function(checkbox)
				{
					checkbox.checked = ! checkbox.checked;
				});
			});
		});
		node.query('[data-must-match]').forEach(function(match)
		{
			match.pattern = new RegExp(document.querySelector('[name="' + match.data('must-match') + '"]').value).escape();
			document.querySelector('[name="' + match.data('must-match') + '"]').addEventListener('change', function()
			{
				document.querySelector('[data-must-match="' + this.name + '"]').pattern = new RegExp(this.value).escape();
			});
		});
		node.query('[data-dropzone]') .forEach(function (el)
		{
			document.querySelector(el.data('dropzone')).DnD(el);
		});
		node.query('input[data-equal-input]').forEach(function(input)
		{
			input.addEventListener('input', function()
			{
				document.querySelectorAll('input[data-equal-input="' + this.data('equal-input') + '"]').forEach(function(other)
				{
					if (other !== input) {
						other.value = input.value;
					}
				});
			});
		});
		node.query('[data-editor-command]').forEach(function(item)
		{
			item.addEventListener('click', function()
			{
				var arg = null;
				if (this.data('editor-value')) {
					arg = this.data('editor-value');
				} else if (this.data('prompt')) {
					arg = prompt(this.data('prompt'));
				} else if (this.data('selection-to')) {
					var createdEl = document.createElement(this.data('selection-to'));
					createdEl.textContent = getSelection().toString();
					arg = createdEl.outerHTML;
				}
				document.execCommand(this.data('editor-command'), null, arg);
			});
		});
		node.query('[data-request]').forEach(function(el)
		{
			el.addEventListener('click', function(event)
			{
				event.preventDefault();
				if (!this.data('confirm') || confirm(this.data('confirm'))) {
					ajax({
						url: this.data('url')|| document.baseURI,
						request: (this.data('prompt'))
							? this.data('request') + '&prompt_value=' + encodeURIComponent(prompt(this.data('prompt')))
							: this.data('request'),
						history: this.data('history') || null,
						cache: el.data('cache')
					}).then(
						handleJSON,
						function(err)
						{
							console.error(err);
						}
					);
				}
			});
		});
		node.query('[data-dropzone]') .forEach(function (finput)
		{
			document.querySelector(finput.data('dropzone')).DnD(finput);
		});
		node.query('[data-fullscreen]').forEach(function(el)
		{
			el.addEventListener(el.data('fullscreen'), function()
			{
				this.requestFullscreen();
			});
		});
		node.query('[data-delete]').forEach(function(el)
		{
			el.addEventListener('click', function()
			{
				document.querySelectorAll(this.data('delete')).forEach(function(remove)
				{
					remove.parentElement.removeChild(remove);
				});
			});
		});
		node.query('menuitem[label="Edit Post"]').forEach(function(el)
		{
			el.addEventListener('click', function()
			{
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
				var retain = setInterval(function()
				{
					ajax({
						url: document.baseURI,
						request: 'action=keep-alive'
					}).then(
						handleJSON,
						function(err)
						{
							console.error(err);
						}
					);
				}, 60000);
				form.addEventListener('submit', function()
				{
					clearInterval(retain);
				});
			});
		});
		node.query('[label="Clear Cache"]').forEach(function(el)
		{
			el.addEventListener('click', function()
			{
				if (! this.data('confirm') || confirm(this.data('confirm'))) {
					cache.clear();
				}
			});
		});
	});
	return this;
};
Element.prototype.worker_clock = function()
{
	"use strict";
	var clockWorker = new Worker(document.baseURI + 'scripts/workers/clock.js'),
		time = this;
	clockWorker.addEventListener('message', function(e)
	{
		time.textContent = e.data.norm;
		time.setAttribute('datetime', e.data.datetime);
	});
	clockWorker.postMessage('');
};
function notifyLocation()
{
	"use strict";
	getLocation({
		enableHighAccuracy: true,
		maximumAge: 0
	}).then(function(pos)
	{
		console.log(pos.coords);
		notify({
			title: 'Your current location:',
			body: 'Longitude: ' + pos.coords.longitude + "\nLatitude: " + pos.coords.latitude,
			icon: document.baseURI + 'images/icons/map.png'
		});
	});
}
Element.prototype.DnD = function(sets)
{
	"use strict";
	this.ondragover = function(event)
	{
		this.classList.add('receiving');
		return false;
	};
	this.ondragend = function(event)
	{
		this.classList.remove('receiving');
		return false;
	};
	this.ondrop = function(e)
	{
		this.classList.remove('receiving');
		e.preventDefault();
		console.log(e);
		if (e.dataTransfer.files.length) {
			for (var i=0; i < e.dataTransfer.files.length; i++) {
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
				reader.addEventListener('progress', function(event)
				{
					if (event.lengthComputable) {
						progress.value = event.loaded / event.total;
					}
				});
				reader.onload = function(event)
				{
					progress.parentElement.removeChild(progress);
					console.log(event);
					if (typeof sets !== 'undefined') {
						switch (sets.tagName.toLowerCase()) {
							case 'input':
							case 'textarea':
								sets.value = event.target.result;
								break;

							case 'img':
								sets.src = event.target.result;
								break;
							default:
								if (/image\/*/.test(file.type)) {
									document.execCommand('insertimage', null, event.target.result);
								}
								else if (/text\/*/.test(file.type)) {
									sets.innerHTML = event.target.result;
								}
						}
					}
				};
				reader.onerror = function(event)
				{
					progress.parentElement.removeChild(progress);
					console.error(event);
				};
			console.log(file);
			}
		}
		return false;
	};
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
};
window.addEventListener('popstate', function()
{
	"use strict";
	ajax({
		url: location.pathname,
		type: 'GET'
	}).then(
		handleJSON,
		function(err)
		{
			console.error(err);
		}
	);
});
