[email]: <mailto:shgysk8zer0@gmail.com> "Email Developer"
[issues]: <https://github.com/shgysk8zer0/chriszuber/issues/new> "Report Bugs, request enhancements, etc"
[github_repo]: <https://github.com/shgysk8zer0/chriszuber> "Main Repo"
[travis-ci]: https://travis-ci.org/shgysk8zer0/chriszuber.svg?branch=master

[apache-download]: <http://httpd.apache.org/download.cgi> "Download Apache"
[php-download]: <http://php.net/> "Download PHP"
[mysql-download]: <http://dev.mysql.com/downloads/> "Download MySQL"
[myth]: <http://www.myth.io> "Myth Homepage"
[node]: <http://nodejs.org/> "Node.js Homepage"
[ubuntu-download]: <http://www.ubuntu.com/download> "Download Ubuntu"
[firefox-download]: <https://www.mozilla.org/en-US/firefox/new/> "Download Mozilla Firefox"
[chrome-download]: <https://www.google.com/chrome/browser/> "Download Google Chrome"

[openclipart]: <https://openclipart.org/>
[openfontlibrary]: <http://openfontlibrary.org/>
[creative-commons]: <http://creativecommons.org/licenses/by-sa/4.0/>

[css-flex]: <http://css-tricks.com/snippets/css/a-guide-to-flexbox/>
[css-supports]: <https://developer.mozilla.org/en-US/docs/Web/CSS/@supports>
[css-vars]: <https://developer.mozilla.org/en-US/docs/Web/CSS/Using_CSS_variables>
[css-import]: <https://developer.mozilla.org/en-US/docs/Web/CSS/@import> "@import - CSS | MDN"
[media-queries]: <https://developer.mozilla.org/en-US/docs/Web/Guide/CSS/Media_queries>
[css-grads]: <https://developer.mozilla.org/en-US/docs/Web/CSS/linear-gradient>
[mdn-woff]: <https://developer.mozilla.org/en-US/docs/Web/CSS/font>
[mdn-viewport-units]: <https://developer.mozilla.org/en-US/docs/Web/CSS/length>

[mdn-els]: <https://developer.mozilla.org/en-US/docs/HTML/Element>
[mdn-inputs]: <https://developer.mozilla.org/en-US/docs/Web/HTML/Element/Input>
[mdn-svg]: <https://developer.mozilla.org/en-US/docs/Web/SVG>
[mdn-datalist]: <https://developer.mozilla.org/en-US/docs/Web/HTML/Element/datalist>
[mdn-contenteditable]: https://developer.mozilla.org/en-US/docs/Web/Guide/HTML/Content_Editable
[w3-contextmenu]: <http://www.w3schools.com/tags/att_global_contextmenu.asp>

[CSP]: <https://developer.mozilla.org/en-US/docs/Web/Security/CSP>

[history-api]: <http://diveintohtml5.info/history.html>
[mdn-qsa]: <https://developer.mozilla.org/en-US/docs/Web/API/Document.querySelectorAll>
[mdn-classlist]: <https://developer.mozilla.org/en-US/docs/Web/API/Element.classList>
[mdn-dataset]: <https://developer.mozilla.org/en-US/docs/Web/API/HTMLElement.dataset>
[mdn-storage]: <https://developer.mozilla.org/en-US/docs/Web/Guide/API/DOM/Storage>
[mdn-json-parse]: <https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/JSON/parse>
[mdn-xhr]: <https://developer.mozilla.org/en-US/docs/Web/API/XMLHttpRequest>
[mdn-formdata]: <https://developer.mozilla.org/en-US/docs/Web/Guide/Using_FormData_Objects>
[mdn-event-listener]: <https://developer.mozilla.org/en-US/docs/Web/API/EventTarget.addEventListener>
[mdn-filereader]: <https://developer.mozilla.org/en-US/docs/Web/API/FileReader>
[mdn-promises]: <https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Promise>
[mdn-mutation-observer]: <https://developer.mozilla.org/en-US/docs/Web/API/MutationObserver>

[apache-headers]: <http://httpd.apache.org/docs/2.2/mod/mod_headers.html>
[apache-mime]: <http://httpd.apache.org/docs/2.2/mod/mod_mime.html>
[apache-rewrite]: <http://httpd.apache.org/docs/2.2/mod/mod_rewrite.html>

[php-pdo]: <http://php.net/manual/en/book.pdo.php>
[php-stm]: <http://php.net/manual/en/pdo.prepared-statements.php>
[php-mcrypt]: <http://php.net/manual/en/book.mcrypt.php>

[schema]: <http://schema.org/>
[structured-data]: <http://www.google.com/webmasters/tools/richsnippets>

[json_response]: <https://github.com/shgysk8zer0/core/blob/master/json_response.php>
[WYSIWYG]: <http://chriszuber.com/posts/html5+wysiwyg+editor+using+contenteditable>

# Super User ![Build Status][travis-ci]

[![Join the chat at https://gitter.im/shgysk8zer0/chriszuber](https://badges.gitter.im/shgysk8zer0/chriszuber.svg)](https://gitter.im/shgysk8zer0/chriszuber?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

**_The world of blogging in the HTML5 world._**

>The aim of this project is to provide the best in HTML5, CSS3, JavaScript, and SVG with minimal effort,
providing support for new elements such as `<dialog>` & `<details>` where required.
Create a _very_ fast, _very_ SEO friendly blog without worrying about plugins, extensions, etc.
Designed to be easy enough to use without knowledge of coding (Like a Word document), but without sacrificing
the power and control that developers want.

## Features
* __Attribute-centric Programming__: allows you to focus on the markup and content rather than JavaScript or CSS. Content authors can do their work without being required to to worry about things that are a developer's or designer's job. Knowledge of HTML and a few attributes may be helpful, but are not required.
* Full AJAX navigation, with [history][history-api]. Navigation is almost instant and no reloading required. The page can be updated in an infinite number of ways, but only the updates are sent in responses. Back/forward buttons navigate as usual.
* Responsive Design - Default CSS uses [flexbox][css-flex] and viewport units. The page will resize and adjust according to the size it is given to be displayed in.
* Feature Detection - Detect for support in CSS using [@supports][css-supports] as well as in JavaScript + classes.
* [json_response class][json_response] dedicated to making responses to AJAX request both powerful and easy to write.
* Security on both client and server end using [Content-Security-Policy][CSP] and [PDO prepared statements][php-stm] (no SQL injection) among many other security features
* [WYSIWYG editor][WYSIWYG] - little or not HTML/CSS knowledge required for authors. Drag-'n-Drop images (currently as data-uri).
* Designed for "modern browsers" - while this does mean limited support of Internet Explorer, it allows for a much better experience for most of the rest of users. Polyfills and fallbacks are in the works, but the aim of this is to provide the best of what the web has to offer, and that unfortunately excludes some outdated browsers.
* SEO built in using [microdata][schema] - search engines will know what to do with different sections of a page because it is explicity told what the title of the article is, who wrote it, when it was written, who commented on it, etc. Example [here][structured-data]
* Templates are easy to write and use - just write some regular HTML and put your placeholders in caps surrounded by "%"s.
* _Most_ class methods are chainable - faster and easier coding
* Updating design made easy thanks to [CSS variables][css-vars]. Use [Myth][myth] (__not included__) when unsupported or in production
* Developer friendly - Things like backing up and restoring a database are as easy as clicking a button in the developer menu, which is available to admin users everywhere via contextmenu (no navigation required, Firefox only at this point). Also provides easy access to debugging info, and the ability to print out debugging info to the browser's console makes tracking down bugs relatively easy. Lastly, PHP errors are saved to a database with file, line, and error message included, and can be searched, filtered, and even fixed from the browser!
* Free/Libre/Open/Creative Commons/Public Domain… whatever applies to what you're referring to. All artwork, icons, and code are free to use for personal use. Special thanks to [OpenClipart][openclipart] and [Open Font Library][openfontlibrary]. All posts are automatically licensed under a [Creative Commons 4.0 license][creative-commons].

## Contact
* [Email Developer][email]
* [Issues Page][issues]

## Forks
* [Main Repo][github_repo]

## Install

	git clone git://github.com/shgysk8zer0/chriszuber.git {path}
	cd {path}
	git submodule init
	git submodule update

## Update

	git pull
	git submodule update

## In case of update conflicts

	git mergetool

## Creating your Repo
**_Update addresses to SSH as needed and available_**

First, fork from the [Main Repository][github_repo]

Copy your "clone URL"

*Install*

	git clone {clone URL}
	git remote add upstream git://github.com/shgysk8zer0/chriszuber.git

*Update*

	git pull upstream master
	git submodule update


## Other Info
### Tested Using:
* [Apache][apache-download] 2.4.7
* [PHP][php-download] 5.5.9
* [MySQL][mysql-download] 5.5.37
* [Ubuntu][ubuntu-download] 13.10 & 14.04 64bit
* [Firefox][firefox-download] 30+ & [Chrome][chrome-download] 35+ (problematic)

### Required PHP Modules:
* [PDO][php-pdo]
* date
* [mcrypt][php-mcrypt]

### Required Apache Modules:
* [mod_headers][apache-headers]
* [mod_mime][apache-mime]
* [mod_rewrite][apache-rewrite]

### Recommended for CSS editing

Uses [CSS-variables][css-vars] (currently Firefox 31+ only) in default stylesheet.
The [Node.js][node] plugin [Myth][myth] creates fully vendor-prefixed CSS from the default CSS,
replaces variables with their values, as well as combining CSS files using `@import`
while still allowing the original to be used as CSS where supported

*Installation and configurations for Ubuntu*

	sudo add-apt-repository ppa:richarvey/nodejs
	sudo apt-get install nodejs npm
	sudo ln -s /usr/bin/nodejs /usr/bin/node
	sudo npm install -g myth
Then to generate...

	myth -c stylesheets/default/combined.css stylesheets/default/output.css

### Required Browser Features:

*These features only found in current versions of Firefox and Chrome*
#### HTML5
* [New Element support][mdn-els] (header, main, footer, nav, progress, menu, datalist)
* [New input types][mdn-inputs] (email, number, date) with validation (pattern, required, min/max)
* [SVG][mdn-svg]
* [contenteditable][mdn-contenteditable], [contextmenu][w3-contextmenu] (Not supported in Chrome), and [list][mdn-datalist] attributes

#### JavaScript
* [querySelectorAll][mdn-qsa]
* [XMLHttpRequest][mdn-xhr]
* [Promises][mdn-promises]
* [Event Listeners][mdn-event-listener]
* [FileReader][mdn-filereader]
* [Mutation Observers][mdn-mutation-observer]
* [FormData][mdn-formdata]
* [JSON.parse][mdn-json-parse]
* [dataset][mdn-dataset]
* [classList][mdn-classlist]
* [local & sessionStorage][mdn-storage]

#### CSS
* [Variables][css-vars] for development
* [Flexbox][css-flex]
* [@media][media-queries], [@import][css-import], & [@supports][css-supports]
* [linear & radial-gradient][css-grads]
* [Woff fonts][mdn-woff]
* [Responsive Units][mdn-viewport-units] (rem, vw, etc)
