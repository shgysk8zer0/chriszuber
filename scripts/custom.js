window.addEventListener('load',function(){
	var html = $(document.documentElement),
		body = $(document.body),
		head = $(document.head);
	html.removeClass('no-js');
	for(let feature of ['querySelectorAll', 'svg', 'audio', 'video', 'canvas', 'dataset', 'connectivity', 'visibility', 'notifications', 'ApplicationCache', 'indexedDB', 'transitions', 'animations', 'cssSupports', 'cssVars', 'workers', 'promises', 'ajax']){
		(supports(feature)) ? html.addClass(feature) : html.addClass('no-' + sup);
	};
	(!document.documentElement.classList.contains('connectivity') && !navigator.onLine) ? html.addClass('offline')  : html.addClass('online');
	(!document.documentElement.classList.contains('visibility') && document.hidden) ? html.addClass('hidden')  : html.addClass('visible');
	$('a.confirm[href]').click(function(event){
		if(!confirm('Go to ' + this.href + '?')) {
			event.preventDefault();
		}
	});
	body.watch({
		childList: function(){
			console.log(this.addedNodes)
		},
		attributes: function(){
			console.log(this.attributeName, this.oldValue)
		},
		characterData: function(){
			console.log(this.oldValue)
		}
	}, [
		'subtree',
		'attributeOldValue',
		'characterDataOldValue'
	]);
});
