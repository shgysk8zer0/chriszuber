'use strict';
var html = document.documentElement,
	body = document.body,
	head = document.head;
window.addEventListener('load',function(){
	html.classList.remove('no-js');
	['querySelectorAll', 'svg', 'audio', 'video', 'canvas', 'dataset', 'connectivity', 'visibility', 'notifications', 'ApplicationCache', 'indexedDB', 'transitions', 'animations', 'cssSupports', 'cssVars', 'workers', 'promises', 'ajax'].forEach(function(sup) {
		(supports(sup)) ? html.classList.add(sup) : html.classList.add('no-' + sup);
	});
	$('a.confirm[href]').click(function(event){
		if(!confirm('Go to ' + this.href)) {
			event.preventDefault();
		}
	});
});
