'use strict';
var target = document.body;
window.addEventListener('load', function(){ /*Cannot rely on $(window).load() to work, so use this instead*/
	var html = $('html'),
		body = $('body'),
		head = $('head');
		cache = new cache();
		html.removeClass('no-js').addClass('js');
	['svg', 'audio', 'video', 'canvas', 'menuitem', 'dataset', 'classList', 'connectivity', 'visibility', 'notifications', 'ApplicationCache', 'indexedDB', 'localStorage', 'sessionStorage', 'CSSgradients', 'transitions', 'animations',  'CSSvars', 'CSSsupports', 'CSSmatches', 'querySelectorAll', 'workers', 'promises', 'ajax', 'FormData'].forEach(function(support){
		(supports(support)) ? html.addClass(support) : html.addClass('no-' + support);
	});
	(supports('connectivity') && !navigator.onLine) ? html.addClass('offline') : html.addClass('online');
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
				case 'contextmenu': {
					var menu = this.target.attr('contextmenu');
					(this.oldValue !== '') && $('menu#' + this.oldValue).delete();
					if(menu && menu !== '') {
						if(!$('menu#'+ menu).found){
							ajax({
								url: document.baseURI,
								request: 'load_menu=' + menu.replace(/\_menu$/ ,''),
								cache: this.target.data('cache')
							}).then(
								JSON.parse,
								console.error
							).then(
								handleJSON,
								console.error
							);
						}
					}
				} break;
				case 'data-request': {
					(this.oldValue !== '') && this.target.addEventListener('click', function() {
						if(!this.data('confirm') || confirm(this.data('confirm'))){
							ajax({
								url: this.data('url')|| document.baseURI,
								request: (this.data('prompt')) ? this.data('request') + '&prompt_value=' + encodeURIComponent(prompt(this.data('prompt'))) : this.data('request'),
								cache: el.data('cache')
							}).then(
								JSON.parse,
								console.error
							).then(
								handleJSON,
								console.error
							);
						}
					});
				}break;
				case 'contextmenu': {
					(this.oldValue !== '') && $('menu#' + this.oldValue).delete();
				}break;
				case 'data-dropzone': {
					document.querySelector(this.target.data('dropzone')).DnD(this.target);
				} break;
				default: {
					console.error('Unhandled attribute in watch', this);
				}
			}
		}
	}, [
		'subtree',
		'attributeOldValue'
	], [
		'data-request',
		'data-dropzone',
		'contextmenu',
		'contenteditable',
		'datalist'
	]);
	$(window).networkChange(function(){
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
	if(typeof sessionStorage.nonce !== 'string') {
		ajax({
			request: 'request=nonce'
		}).then(
			JSON.parse,
			console.error
		).then(
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
		node.querySelectorAll('*').forEach(function(child) {
			child.addEventListener('contextmenu', function(event){
				target = event.target;
			});
		});
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
							JSON.parse,
							console.error
						).then(
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
					JSON.parse,
					console.error
					).then(
						handleJSON,
						console.error
					);
				}
			});
		}
		node.query('a[href^="' + document.location.origin + '"]:not([target="_blank"]):not([download])').forEach(function(a) {
			a.addEventListener('click', function(event) {
				event.preventDefault();
				ajax({
					url: this.href,
					type: 'GET',
					history: this.href,
					cache: this.data('cache')
				}).then(
					JSON.parse,
					console.error
				).then(
					handleJSON,
					console.error
				);
			});
		});
		node.query('form[name]').forEach(function(form){
			form.addEventListener('submit', function(event){
				event.preventDefault();
				ajax({
					url: this.action || document.baseURI,
					type: this.method || 'POST',
					contentType: this.enctype,
					form: this
				}).then(
					JSON.parse,
					console.error
				).then(
					handleJSON,
					console.error
				);
			});
			if(form.name === 'new_post') {
				var retain = setInterval(function(){
					ajax({
						url: document.baseURI,
						request: 'action=keep-alive'
					}).then(
						JSON.parse,
						console.error
					).then(
						handleJSON,
						console.error
					);
				}, 60000);
				form.addEventListener('submit', function(){
					clearInterval(retain);
				});
			}
		});
		node.query('[data-dropzone]') .forEach(function (el) {
			document.querySelector(el.data('dropzone')).DnD(el);
		});
		node.query('#wysiwyg_menu menuitem[data-editor-command]').forEach(function(item) {
			item.addEventListener('click', function() {
				var arg = null;
				if(this.data('editor-value')) {
					arg = this.data('editor-value');
				}
				else if(this.data('prompt')) {
					arg = prompt(this.data('prompt'));
				}
				document.execCommand(this.data('editor-command'), null, arg);
			})
		});
		node.query('script:not([src])').forEach(function(script) {
			eval(script.textContent);
		});
		node.query('[data-link]').forEach(function(link) {
			link.addEventListener('click', function(){
				ajax({
					url: this.data('link'),
					type: 'GET',
					history: this.data('link'),
					cache: this.data('cache')
				}).then(
					JSON.parse,
					console.error
				).then(
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
						JSON.parse,
						console.error
					).then(
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
				JSON.parse,
				console.error
			).then(
				handleJSON,
				console.error
			);
		});
		node.query('[data-dropzone]') .forEach(function (finput) {
			document.querySelector(finput.data('dropzone')).DnD(finput)
		});
		node.query('.clock').forEach(function(el) {
			el.worker_clock();
		});
		node.query('[autofocus]').forEach(function(input) {
			input.focus();
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
		node.query('menuitem[label="Edit Post"]').forEach(function(el) {
			el.addEventListener('click', function() {
				var form = document.createElement('form'),
					article = document.querySelector('article'),
					title = document.querySelector('article header h1'),
					keywords = document.querySelector('article header nav'),
					content = document.querySelector('article section'),
					submit = document.createElement('button'),
					nonce = document.createElement('input'),
					fieldset = document.createElement('fieldset'),
					legend = document.createElement('legend'),
					oldTitle = document.createElement('input'),
					tags = [],
					description = document.createElement('textarea');
				form.name = 'edit_post';
				form.method = 'POST';
				form.action = document.baseURI;
				form.attr('contextmenu', 'wysiwyg_menu');
				keywords.querySelectorAll('a').forEach(function(tag) {
					tags.push(tag.textContent);
				});
				description.name = 'description';
				description.value = document.querySelector('meta[name="description"]').content;
				description.required = true;
				description.maxLength = 160;
				description.placeholder = 'Description will appear in searches. 160 character limit';
				legend.textContent = 'Update Post';
				nonce.type = 'hidden';
				nonce.name = 'nonce';
				nonce.required = true;
				nonce.readonly = true;
				nonce.value = sessionStorage.nonce;
				submit.type = "Submit";
				submit.textContent = 'Update Post';
				title.attr('contenteditable', 'true');
				title.data('input-name','title');
				keywords.attr('contenteditable', 'true');
				keywords.data('input-name', 'keywords');
				keywords.innerHTML = tags.join(', ');
				content.attr('contenteditable', 'true');
				content.data('input-name', 'content');
				content.data('dropzone', 'main');
				oldTitle.name = 'old_title';
				oldTitle.type = 'hidden';
				oldTitle.readonly = true;
				oldTitle.required = true;
				oldTitle.value = title.textContent;
				fieldset.append(legend, article, oldTitle, nonce, description, submit);
				form.appendChild(fieldset);
				document.querySelector('main').appendChild(form);
				var retain = setInterval(function(){
					ajax({
						url: document.baseURI,
						request: 'action=keep-alive'
					}).then(
						JSON.parse,
						console.error
					).then(
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
}
Element.prototype.worker_clock=function(){
	var clockWorker = new Worker(document.baseURI + 'scripts/workers/clock.js'),
		time = this;
	clockWorker.addEventListener('message', function(e){
		time.textContent = e.data.norm;
		time.setAttribute('datetime', e.data.datetime);
	});
	clockWorker.postMessage('');
}
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
				let file = e.dataTransfer.files[i],
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
				}
			console.log(file);
			}
		};
		return false;
	}
}