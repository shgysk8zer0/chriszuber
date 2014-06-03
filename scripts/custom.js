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
			html.results.bootstrap();
		}, 100
	);
	body.watch({
		childList: function(){
			this.addedNodes.bootstrap();
		},
		attributes: function(){
			switch(this.attributeName) {
				case 'data-menu':
					var menu = this.target.data('menu');
					if(menu && menu !== '') {
						this.target.setAttribute('contextmenu', menu + '_menu');
						if(!$('menu#'+ menu + '_menu').found){
							ajax({
								url: document.baseURI,
								request: 'load_menu=' + menu,
								cache: this.target.data('cache')
							}).then(
								handleJSON,
								console.error
							);
						}
						this.target.removeAttribute('data-menu');
					}
					break;
				case 'data-request':
					(this.oldValue !== '') && this.target.addEventListener('click', function() {
						if(!this.data('confirm') || confirm(this.data('confirm'))){
							ajax({
								url: this.data('url')|| document.baseURI,
								request: (this.data('prompt')) ? this.data('request') + '&prompt_value=' + encodeURIComponent(prompt(this.data('prompt'))) : this.data('request'),
								cache: el.data('cache')
							}).then(
								handleJSON,
								console.error
							);
						}
					});
					break;
				case 'data-ajax':
				case 'data-ajax-request':
					ajax({
						url: this.target.data('ajax') || document.baseURI,
						request: this.target.data('ajax-request') || null,
						type: this.target.data('type') || 'POST',
						cache: this.target.data('cache')
					}).then(
						handleJSON,
						console.error
					);
					break;
				case 'contextmenu':
					(this.oldValue !== '') && $('menu#' + this.oldValue).delete();
					break;
				default:
					console.error('Unhandled attribute in watch', this);
			}
		}
	}, [
		'subtree',
		'attributeOldValue'
	], [
		'data-menu',
		'data-request',
		'data-ajax',
		'data-ajax-request',
		'contextmenu'
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
		/*node.$('*').contextmenu(function(event){
			console.log(event);
		});*/
		node.querySelectorAll('*').forEach(function(child) {
			child.addEventListener('contextmenu', function(event){
				target = event.target;
			});
		});
		node.query('[label="Text Style"] > [data-style]').forEach(function(item) {
			item.addEventListener('click', function() {
				var sel = new selection();
				sel.replace(sel.text[this.data('style')]());
			})
		});
		node.query('a[href^="' + document.location.origin + '"]').forEach(function(a) {
			a.addEventListener('click', function(event) {
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
		node.query('form').forEach(function(el){
			el.addEventListener('submit', function(event){
				event.preventDefault();
				this.ajaxSubmit().then(handleJSON, console.error);

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
		node.query('table[data-sql-table] tr[data-sql-id] input[name]').forEach(function(input){
			input.addEventListener('change', function(){
				ajax({
					request:'table=' + encodeURIComponent(this.ancestor('table').data('sql-table')) + '&id=' + encodeURIComponent(this.ancestor('tr').data('sql-id')) + '&name=' + encodeURIComponent(this.name) + '&value=' + encodeURIComponent(this.value) + '&nonce=' + sessionStorage.nonce
				}).then(
					handleJSON,
					console.error
				);
			});
		});
		if(supports('menuitem')){
			node.query('[data-menu]').forEach(function(el){
				var menu = el.data('menu');
				el.setAttribute('contextmenu', menu + '_menu');
				el.removeAttribute('data-menu');
				if($('menu#'+menu + '_menu').length === 0){
					ajax({
						url: document.baseURI,
						request: 'load_menu=' + menu,
						cache: el.data('cache')
					}).then(
						handleJSON,
						console.error
					);
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
		node.query('script').forEach(function(script) {
			(script.textContent!== '') && eval(script.textContent);
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
		node.query('menuitem[label="Edit Post"]').forEach(function(el) {
			el.addEventListener('click', function() {
				var form = document.createElement('form');
				form.name = 'edit_post';
				form.method = 'POST';
				form.action = document.baseURI;
				form.data('menu', 'wysiwyg');
				var title = document.querySelector('article header h1');
				var keywords = document.querySelector('article header nav');
				var content = document.querySelector('article section');
				var submit = document.createElement('button');
				var nonce = document.createElement('input');
				var fieldset = document.createElement('fieldset');
				var legend = document.createElement('legend');
				var oldTitle = document.createElement('input');
				var tags = [];
				keywords.querySelectorAll('a').forEach(function(tag) {
					tags.push(tag.textContent);
				});
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
				oldTitle.name = 'old_title';
				oldTitle.type = 'hidden';
				oldTitle.readonly = true;
				oldTitle.required = true;
				oldTitle.value = title.textContent;
				fieldset.appendChild(legend);
				fieldset.appendChild(document.querySelector('article'));
				fieldset.appendChild(oldTitle);
				fieldset.appendChild(nonce);
				fieldset.appendChild(submit);
				form.appendChild(fieldset);
				document.querySelector('main').appendChild(form);
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
	var clock=document.createElement('time'),
	clockWorker=new Worker(document.baseURI + 'scripts/workers/clock.js');
	this.appendChild(clock);
	clockWorker.addEventListener('message',function(e){
		clock.textContent = e.data.norm;
		clock.setAttribute('datetime',e.data.datetime);
	});
	clockWorker.postMessage('');
}
