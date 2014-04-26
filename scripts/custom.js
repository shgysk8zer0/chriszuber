'use strict';
var html = document.documentElement,
	body = document.body,
	head = document.head;
window.addEventListener('load',function(){
	html.classList.remove('no-js');
	['transitions'].forEach(function(sup) {
		(supports(sup)) ? html.classList.add(sup) : html.classList.add('no-' + sup);
	})
});