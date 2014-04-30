window.addEventListener('load', function () {
	var html = $(document.documentElement),
		body = $(document.body),
		head = $(document.head);
	['svg', 'audio', 'video', 'canvas', 'dataset', 'connectivity', 'visibility', 'notifications', 'ApplicationCache', 'indexedDB', 'transitions', 'animations',  'CSSvars', 'CSSsupports', 'CSSmatches', 'querySelectorAll', 'workers', 'promises', 'ajax', 'FormData'].forEach(function(support){
		(supports(support)) ? html.addClass(support) : html.addClass('no-' + support);
	});
	document.body.bootstrap();
	body.watch({
		childList: function(){
			this.addedNodes.bootstrap();
		},
	}, [
		'subtree'
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