'use strict';
window.addEventListener('load', function(){ /*Cannot rely on $(window).load() to work, so use this instead*/
	var html = $(document.documentElement),
		body = $(document.body),
		head = $(document.head);
		html.removeClass('no-js').addClass('js');
	['svg', 'audio', 'video', 'canvas', 'menuitem', 'dataset', 'classList', 'connectivity', 'visibility', 'notifications', 'ApplicationCache', 'indexedDB', 'CSSgradients', 'transitions', 'animations',  'CSSvars', 'CSSsupports', 'CSSmatches', 'querySelectorAll', 'workers', 'promises', 'ajax', 'FormData'].forEach(function(support){
		(supports(support)) ? html.addClass(support) : html.addClass('no-' + support);
	});
	(supports('connectivity') && !navigator.onLine) ? html.addClass('offline') : html.addClass('online');
	
	document.body.bootstrap();
	body.watch({
		childList: function(){
			this.addedNodes.forEach(function(node){
				node.bootstrap();
			});
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
								request: 'load_menu=' + menu
							}).then(handleJSON, console.error);
						}
						this.target.removeAttribute('data-menu');
					}
					break;
				case 'contextmenu':
					(this.oldValue !== '') && $('menu#' + this.oldValue).delete();
					break;
			}
		}
	}, [
		'subtree',
		'attributeOldValue'
	], [
		'data-menu',
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
			icon: 'images/Icons/network-server.png'
		});
	}).offline(function(){
		$('fieldset').each(function(fieldset){
			fieldset.disabled = true;
		});
		notify({
			title: 'Network:',
			body: 'offline',
			icon: 'images/Icons/network-server.png'
		});
	});
});

