window.addEventListener('load', function(){ /*Cannot rely on $(window).load() to work, so use this instead*/
	'use strict';
	var html = $(document.documentElement),
		body = $(document.body),
		head = $(document.head);
	html.removeClass('no-js').addClass('js');
	['svg', 'audio', 'video', 'canvas', 'menuitem', 'dataset', 'connectivity', 'visibility', 'notifications', 'ApplicationCache', 'indexedDB', 'transitions', 'animations',  'CSSvars', 'CSSsupports', 'CSSmatches', 'querySelectorAll', 'workers', 'promises', 'ajax', 'FormData'].forEach(function(support){
		(supports(support)) ? html.addClass(support) : html.addClass('no-' + support);
	});
	document.body.bootstrap();
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
						if($('menu#'+ menu + '_menu').length === 0){
							ajax({
								url: document.baseURI,
								request: 'load_menu=' + menu
							}).then(handleXHRjson, console.error);
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
	]);
	$(window).online(function(){
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
Element.prototype.worker_clock=function(){
	var clock=document.createElement('time'),
	clockWorker=new Worker(document.baseURI + 'scripts/workers/clock.js');
	this.appendChild(clock);
	clockWorker.addEventListener('message',function(e){
		clock.innerHTML=e.data.norm;
		clock.setAttribute('datetime',e.data.datetime);
	});
	clockWorker.postMessage('');
}
