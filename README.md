# Super User
![Super User](<http://chriszuber.com/super-user.svgz> "Super User")
**_The world of blogging in the HTML5 world._**

>The aim of this project is to provide the best in HTML5, CSS3, JavaScript, and SVG with minimal effort,
providing support for new elements such as `<dialog>` & `<details>` where required.
Create a _very_ fast, _very_ SEO friendly blog without worrying about plugins, extensions, etc.
Designed to be easy enough to use without knowledge of coding (Like a Word document), but without sacrificing
the power and control that developers want.

## Features
* __Attribute-centric Programming__: allows you to focus on the markup and content rather than JavaScript or CSS. Content authors can do their work without being required to to worry about things that are a developer's or designer's job. Knowledge of HTML and a few attributes may be helpful, but are not required.
* Full AJAX navigation, with [history](<http://diveintohtml5.info/history.html>). Navigation is almost instant and no reloading required. The page can be updated in an infinite number of ways, but only the updates are sent in responses. Back/forward buttons navigate as usual.
* Responsive Design - Default CSS uses [flexbox](<http://css-tricks.com/snippets/css/a-guide-to-flexbox/>) and viewport units. The page will resize and adjust according to the size it is given to be displayed in.
* Feature Detection - Detect for support in CSS using [@supports](<https://developer.mozilla.org/en-US/docs/Web/API/CSS.supports>) as well as in JavaScript + classes.
* [json_response class](<https://github.com/shgysk8zer0/chriszuber/blob/master/classes/json_response.php>) dedicated to making responses to AJAX request both powerful and easy to write.
* Security on both client and server end using [Content-Security-Policy](<https://developer.mozilla.org/en-US/docs/Web/Security/CSP>) and [PDO prepared statements](<http://php.net/manual/en/pdo.prepared-statements.php>) (no SQL injection) among many other security features
* [WYSIWYG editor](<http://chriszuber.com/posts/html5+wysiwyg+editor+using+contenteditable>) - little or not HTML/CSS knowledge required for authors. Drag-'n-Drop images (currently as data-uri).
* Designed for "modern browsers" - while this does mean limited support of Internet Explorer, it allows for a much better experience for most of the rest of users. Polyfills and fallbacks are in the works, but the aim of this is to provide the best of what the web has to offer, and that unfortunately excludes some outdated browsers.
* SEO built in using [microdata](<http://schema.org/>) - search engines will know what to do with different sections of a page because it is explicity told what the title of the article is, who wrote it, when it was written, who commented on it, etc. Example [here](<http://www.google.com/webmasters/tools/richsnippets?url=chriszuber.com>)
* Templates are easy to write and use - just write some regular HTML and put your placeholders in caps surrounded by "%"s.
* _Most_ class methods are chainable - faster and easier coding
* Updating design made easy thanks to [CSS variables](<https://developer.mozilla.org/en-US/docs/Web/CSS/Using_CSS_variables>). Use [Myth](<http://myth.io>) (__not included__) when unsupported or in production
* Developer friendly - Things like backing up and restoring a database are as easy as clicking a button in the developer menu, which is available to admin users everywhere via contextmenu (no navigation required, Firefox only at this point). Also provides easy access to debugging info, and the ability to print out debugging info to the browser's console makes tracking down bugs relatively easy. Lastly, PHP errors are saved to a database with file, line, and error message included, and can be searched, filtered, and even fixed from the browser!
* Free/Libre/Open/Creative Commons/Public Domain… whatever applies to what you're refering to. All artwork, icons, and code are free to use for personal use. Special thanks to [OpenClipart](<https://openclipart.org/>) and [Open Font Library](<http://openfontlibrary.org/>). All posts are automatically licensed under a [Creative Commons 4.0 license](<http://creativecommons.org/licenses/by-sa/4.0/>).
## Forks
* [Main Repo](<https://github.com/shgysk8zer0/chriszuber> "Main Repo")

## Creating your Repo
**_Update addresses to SSH as needed and available_**

	git init
	git remote add origin {Your Repo Address}
	git remote add project_manager git://github.com/shgysk8zer0/chriszuber.git
	git pull project_manager master
	git push --set-upstream origin master

## Contact
* [Email Developer](mailto:shgysk8zer0@gmail.com> "Email Developer")
* [Issues Page](<https://github.com/shgysk8zer0/chriszuber/issues> "Report Bugs, request enhancements, etc")

## Other Info
### Tested Using:
* Apache/2.4.7
* PHP 5.5.9
* MySQL 5.5.37
* Ubuntu 13.10 & 14.04 64bit
* Firefox 30+ & Chrome 35+ (problematic)

### Required PHP Modules:
* PDO
* date
* mcrypt

### Required Apache Modules:
* mod_headers
* mod_mime
* mod_include
* mod_rewrite

### Recommended for CSS editing

Uses CSS-variables (currently Firefox 31+ only) in default stylesheet.
The [Node.js](<http://nodejs.org/> "Node.js Homepage") plugin [Myth](<www.myth.io> "Myth Homepage") creates fully vendor-prefixed CSS from the default CSS,
replaces variables with their values, as well as combining CSS files using @import
while still allowing the original to be used as CSS where supported

*Installation and configurations for Ubuntu*

	sudo apt-get install nodejs npm
	sudo ln -s /usr/bin/nodejs /usr/bin/node
	sudo npm install -g myth

Then to generate...
	myth stylesheets/style.css stylesheets/style.out.css
	myth -c stylesheets/combined.css stylesheets/combined.out.css

### Required Browser Features:

*These features only found in current versions of Firefox and Chrome*
#### HTML5
* New Element support (header, main, footer, nav, progress, menu, datalist)
* New input types (email, number, date) with validation (pattern, required, min/max)
* SVG
* contenteditable, contextmenu (Not supported in Chrome), and list attributes

#### JavaScript
* querySelectorAll
* XMLHttpRequest
* Promises
* Event Listeners
* FileReader
* Mutation Observers
* FormData
* JSON.parse
* dataset
* classList
* local & sessionStorage

#### CSS
* Flexbox
* @media & @supports
* linear & radial-gradient
* Woff fonts
* Responsive Units (rem, vw, etc)
