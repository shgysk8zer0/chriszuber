/*Cannot rely on $(window).load() to work, so use this instead*/
function reportError(err) {
	console.error(err);
	notify({
		title: err.name,
		body: err.message,
		icon: 'images/octicons/svg/bug.svg'
	});
}
function notifyLocation() {
	'use strict';
	getLocation({
		enableHighAccuracy: true,
		maximumAge: 0
	}).then(function(pos) {
		console.log(pos.coords);
		notify({
			title: 'Your current location:',
			body: `Longitude: ${pos.coords.longitude}\nLatitude: ${pos.coords.latitude}`,
			icon: `${document.baseURI}images/icons/map.png`
		});
	});
}
self.addEventListener('load', () => {
	'use strict';
	var html = $('html'),
		$body = $('body'),
		$head = $('head');
		document.documentElement.classList.swap('no-js', 'js');
	['svg', 'audio', 'video', 'picture', 'canvas', 'menuitem', 'details',
	'dialog', 'dataset', 'HTMLimports', 'classList', 'connectivity',
	'visibility', 'notifications', 'ApplicationCache', 'indexedDB',
	'localStorage', 'sessionStorage', 'CSSgradients', 'transitions',
	'animations', 'CSSvars', 'CSSsupports', 'CSSmatches', 'querySelectorAll',
	'workers', 'promises', 'ajax', 'FormData'].forEach(feat => {
		document.documentElement.classList.pick(feat, `no-${feat}`, supports(feat));
	});
	document.documentElement.classList.pick('offline', 'online', (supports('connectivity') && !navigator.onLine));
	setTimeout(() => {
			$body.bootstrap();
		}, 100
	);
	$body.watch({
		childList: function() {
			$(this.addedNodes).bootstrap();
		},
		attributes: function() {
			switch (this.attributeName) {
				case 'contextmenu':
					var menu = this.target.getAttribute('contextmenu');
					if (this.oldValue !== '') {
						$(`menu#${this.oldValue}`).remove();
					}
					if (menu && menu !== '') {
						if (!$('menu#' + menu).found) {
							fetch(document.baseURI, {
								method: 'POST',
								headers: new Headers({Accept: 'application/json'}),
								body: new URLSearchParams(`load_menu=${menu.replace(/\_menu$/, '')}`),
								credentials: 'include'
							}).then(parseResponse).then(handleJSON).catch(function(exc) {
								console.error(exc);
							});
						}
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
					if ('import' in this.target.dataset) {
						this.target.HTMLimport();
					}
					break;

				case 'data-request':
					if (this.oldValue !== '') {
						this.target.addEventListener('click', function() {
							if (!('confirm' in this.dataset) || confirm(this.dataset.confirm)) {
								let url = new URL(this.dataset.url, document.baseURI);
								let headers = new Headers();
								let body = new URLSearchParams(this.dataset.request);
								headers.set('Accept', 'application/json');
								if ('prompt' in this.dataset) {
									body.set('prompt_value', prompt(this.dataset.prompt));
								}
								fetch(url, {
									method: 'POST',
									headers,
									body
								}).then(parseResponse).then(handleJSON).catch(reportError);
							}
						});
					}
					break;

				case 'data-dropzone':
					document.querySelector(this.target.dataset.dropzone).DnD(this.target);
					break;

				default:
					console.error(`Unhandled attribute in watch: "${this.attributeName}"`);
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
	$(window).networkChange(function() {
		$('html').toggleClass('online', navigator.onLine).toggleClass('offline', !navigator.onLine);
	}).online(function() {
		$('fieldset').each(function(fieldset) {
			fieldset.removeAttribute('disabled');
		});
		notify({
			title: 'Network:',
			body: 'online',
			icon: 'images/icons/network-server.png'
		});
	}).offline(function() {
		$('fieldset').each(function(fieldset) {
			fieldset.disabled = true;
		});
		notify({
			title: 'Network:',
			body: 'offline',
			icon: 'images/icons/network-server.png'
		});
	});
	if (!sessionStorage.hasOwnProperty('nonce')) {
		let url = new URL(document.baseURI);
		let headers = new Headers();
		let body = new URLSearchParams();
		headers.set('Accept', 'application/json');
		body.set('request', 'nonce');
		fetch(url, {
			headers,
			method: 'POST',
			body,
			credentials: 'include'
		}).then(parseResponse).then(handleJSON).catch(reportError);
	}
});
zQ.prototype.bootstrap = function() {
	'use strict';
	this.each(function(node) {
		if (node.nodeType !== 1) {
			return this;
		}
		if (!supports('details')) {
			node.query('details > summary').forEach(summary => {
				summary.addEventListener('click', click => {
					if (summary.parentElement.hasAttribute('open')) {
						summary.parentElement.removeAttribute('open');
					} else {
						summary.parentElement.setAttribute('open', '');
					}
				});
			});
		}
		if (supports('menuitem')) {
			/*
			 * This should be done via GET
			 */
			node.query('[contextmenu]').forEach(el => {
				let menu = el.getAttribute('contextmenu');
				if (menu && menu !== '') {
					if (!$(`menu#${menu}`).found) {
						let headers = new Headers();
						let url = new URL(document.baseURI);
						let body = new URLSearchParams();
						body.set('load_menu', menu.replace(/\_menu$/, ''));
						headers.set('Accept', 'application/json');
						fetch(url, {
							method: 'POST',
							headers,
							body,
							credentials: 'include'
						}).then(parseResponse).then(handleJSON).catch(reportError);
					}
				}
			});
		}
		if (supports('datalist')) {
			node.query('[list]').forEach(list => {
				if (!$('#' + list.getAttribute('list')).found) {
					let url = new URL(document.baseURI);
					let headers = new Headers();
					let body = new URLSearchParams();
					headers.set('Accept', 'application/json');
					body.set('datalist', list.getAttribute('list'))
					fetch(url, {
						method: 'POST',
						headers,
						body,
						credentials: 'include'
					}).then(parseResponse).then(handleJSON).catch(reportError);
				}
			});
		}
		if (!supports('picture')) {
			node.query('picture').forEach(function(picture) {
				if ('matchMedia' in window) {
					let sources = picture.querySelectorAll('source[media][srcset]');
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
		node.query('[autofocus]').forEach(input => input.focus());
		node.query(
			'a[href]:not([target="_blank"]):not([download]):not([href*="\#"])'
		).filter(link => link.origin === location.origin).forEach(a => {
			a.addEventListener('click', click => {
				click.preventDefault();
				let url = new URL(a.href, location.origin);
				let headers = new Headers();
				headers.set('Accept', 'application/json');
				if (typeof ga === 'function') {
					ga('send', 'pageview', a.href);
				}
				fetch(url, {
					method: 'GET',
					headers
				}).then(parseResponse).then(handleJSON).then(function(resp) {
					history.pushState({}, document.title, a.href);
					return resp;
				}).catch(reportError);
			});
		});
		node.query('form[name]').filter(
			form => new URL(form.action).origin === location.origin
		).forEach(form => {
			form.addEventListener('submit', submit => {
				submit.preventDefault();
				if (!('confirm' in submit.target.dataset) || confirm(submit.target.dataset.confirm)) {
					let body = new FormData(submit.target);
					let headers = new Headers();
					let url = new URL(submit.target.action, location.origin);
					body.append('nonce', sessionStorage.getItem('nonce'));
					body.append('form', submit.target.name);
					headers.set('Accept', 'application/json');
					fetch(url, {
							method: submit.target.method || 'POST',
							headers,
							body,
							credentials: 'include'
						}
					).then(parseResponse).then(handleJSON).catch(reportError);
				}
			});
			if (form.name === 'new_post') {
				let retain = setInterval(() => {
					let url = new URL(document.baseURI);
					let headers = new Headers();
					let body = new URLSearchParams();
					headers.set('Accept', 'application/json');
					body.set('action', 'keep-alive');
					fetch(url, {
						method: 'GET',
						headers,
						body,
						credentials: 'include'
					}).then(parseResponse).then(handleJSON).catch(reportError);
				}, 60000);
				form.addEventListener('submit', () => {
					clearInterval(retain);
				});
			}
		});
		node.query('[data-show]').forEach(el => {
			el.addEventListener('click', click => {
				document.querySelector(el.dataset.show).show();
			});
		});
		node.query('[data-show-modal]').forEach(el => {
			el.addEventListener('click', click => {
				document.querySelector(el.dataset.showModal).showModal();
			});
		});
		node.query('[data-scroll-to]').forEach(el => {
			el.addEventListener('click', click => {
				document.querySelector(el.dataset.scrollTo).scrollIntoView();
			});
		});
		node.query('[data-import]').forEach(el => {
			el.HTMLimport();
		});
		node.query('[data-close]').forEach(el => {
			el.addEventListener('click', click => {
				document.querySelector(el.dataset.close).close();
			});
		});
		node.query('fieldset button[type="button"].toggle').forEach(toggle => {
			toggle.addEventListener('click', click => {
				let fieldset = toggle.closest('fieldset');
				let checkboxes = Array.from(fieldset.querySelectorAll('input[type="checkbox"]'));
				checkboxes.forEach(checkbox => {
					checkbox.checked = !checkbox.checked;
				});
			});
		});
		node.query('[data-must-match]').forEach(match => {
			match.pattern = new RegExp(document.querySelector(`[name="${match.dataset.mustMatch}"]`).value).escape();
			document.querySelector(`[name="${match.dataset.mustMatch}"]`).addEventListener('change', change => {
				document.querySelector(`[data-must-match="${change.target.name}"]`).pattern = new RegExp(change.target.value).escape();
			});
		});
		node.query('[data-dropzone]') .forEach(function (el) {
			document.querySelector(el.dataset.dropzone).DnD(el);
		});
		node.query('input[data-equal-input]').forEach(input => {
			input.addEventListener('input', input => {
				$(`input[data-equal-input="${input.target.dataset.equalInput}"]`).each(other => {
					if (other !== input) {
						other.value = input.value;
					}
				});
			});
		});
		node.query('menu[type="context"]').forEach(WYSIWYG);
		node.query('[data-request]').forEach(el => {
			el.addEventListener('click', click => {
				click.preventDefault();
				if (!(el.dataset.hasOwnProperty('confirm')) || confirm(el.dataset.confirm)) {
					let url = new URL(el.dataset.url || document.baseURI);
					let headers = new Headers();
					let body = new URLSearchParams(el.dataset.request);
					headers.set('Accept', 'application/json');
					if ('prompt' in el.dataset) {
						body.set('prompt_value', prompt(el.dataset.prompt));
					}
					fetch(url, {
						method: 'POST',
						headers,
						body,
						credentials: 'include'
					}).then(parseResponse).then(handleJSON).catch(reportError);
				}
			});
		});
		node.query('[data-dropzone]').forEach(finput => {
			document.querySelector(finput.dataset.dropzone).DnD(finput);
		});
		node.query('[data-fullscreen]').forEach(el => {
			el.addEventListener('click', click => {
				if (fullScreen) {
					document.cancelFullScreen();
				} else {
					document.querySelector(el.dataset.fullscreen).requestFullScreen();
				}
			});
		});
		node.query('[data-delete]').forEach(function(el) {
			el.addEventListener('click', click => {
				$(el.dataset.delete).remove();
			});
		});
		node.query('menuitem[label="Edit Post"]').forEach(function(el) {
			el.addEventListener('click', function() {
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
				footer.remove();
				form.name = 'edit_post';
				form.method = 'POST';
				form.action = document.baseURI;
				form.setAttribute('contextmenu', 'wysiwyg_menu');
				description.name = 'description';
				description.value = document.querySelector('meta[name="description"]').content;
				description.required = true;
				description.maxLength = 160;
				description.placeholder = 'Description will appear in searches. 160 character limit';
				legend.textContent = 'Update Post';
				submit.type = 'Submit';
				submit.textContent = 'Update Post';
				title.setAttribute('contenteditable', 'true');
				title.dataset.inputName = 'title';
				keywords.setAttribute('contenteditable', 'true');
				keywords.dataset.inputName = 'keywords';
				keywords.setAttribute('open', '');
				content.setAttribute('contenteditable', 'true');
				content.dataset.inputName = 'content';
				content.dataset.dropzone = 'main';
				oldTitle.name = 'old_title';
				oldTitle.type = 'hidden';
				oldTitle.readonly = true;
				oldTitle.required = true;
				oldTitle.value = title.textContent;
				fieldset.append(legend, header, content, oldTitle, description, submit);
				form.appendChild(fieldset);
				article.appendChild(form);
				let retain = setInterval(() => {
					let url = new URL(docuemnt.baseURI);
					let headers = new Headers();
					let body = new URLSearchParams();
					headers.set('Accept', 'application/json');
					body.set('action', 'keep-alive');
					fetch(docuemnt.baseURI, {
						method: 'POST',
						headers,
						body,
						credentials: 'include'
					}).then(parseResponse).then(handleJSON).catch(reportError);
				}, 60000);
				form.addEventListener('submit', () => {
					clearInterval(retain);
				});
			});
		});
	});
	return this;
};
