<h1>Super User</h1>

<p><img src="//chriszuber.com/super-user.svgz" alt="Super User" title="Super User" />
<strong><em>The world of blogging in the HTML5 world.</em></strong></p>

<blockquote>
  <p>The aim of this project is to provide the best in HTML5, CSS3, JavaScript, and SVG with minimal effort,
providing support for new elements such as <code>&lt;dialog&gt;</code> &amp; <code>&lt;details&gt;</code> where required.
Create a <em>very</em> fast, <em>very</em> SEO friendly blog without worrying about plugins, extensions, etc.
Designed to be easy enough to use without knowledge of coding (Like a Word document), but without sacrificing
the power and control that developers want.</p>
</blockquote>

<h2>Features</h2>

<ul>
<li><strong>Attribute-centric Programming</strong>: allows you to focus on the markup and content rather than JavaScript or CSS. Content authors can do their work without being required to to worry about things that are a developer's or designer's job. Knowledge of HTML and a few attributes may be helpful, but are not required.</li>
<li>Full AJAX navigation, with <a href="http://diveintohtml5.info/history.html">history</a>. Navigation is almost instant and no reloading required. The page can be updated in an infinite number of ways, but only the updates are sent in responses. Back/forward buttons navigate as usual.</li>
<li>Responsive Design - Default CSS uses <a href="http://css-tricks.com/snippets/css/a-guide-to-flexbox/">flexbox</a> and viewport units. The page will resize and adjust according to the size it is given to be displayed in.</li>
<li>Feature Detection - Detect for support in CSS using <a href="https://developer.mozilla.org/en-US/docs/Web/CSS/@supports">@supports</a> as well as in JavaScript + classes.</li>
<li><a href="https://github.com/shgysk8zer0/chriszuber/blob/master/classes/json_response.php">json_response class</a> dedicated to making responses to AJAX request both powerful and easy to write.</li>
<li>Security on both client and server end using <a href="https://developer.mozilla.org/en-US/docs/Web/Security/CSP">Content-Security-Policy</a> and <a href="http://php.net/manual/en/pdo.prepared-statements.php">PDO prepared statements</a> (no SQL injection) among many other security features</li>
<li><a href="http://chriszuber.com/posts/html5+wysiwyg+editor+using+contenteditable">WYSIWYG editor</a> - little or not HTML/CSS knowledge required for authors. Drag-'n-Drop images (currently as data-uri).</li>
<li>Designed for "modern browsers" - while this does mean limited support of Internet Explorer, it allows for a much better experience for most of the rest of users. Polyfills and fallbacks are in the works, but the aim of this is to provide the best of what the web has to offer, and that unfortunately excludes some outdated browsers.</li>
<li>SEO built in using <a href="http://schema.org/">microdata</a> - search engines will know what to do with different sections of a page because it is explicity told what the title of the article is, who wrote it, when it was written, who commented on it, etc. Example <a href="http://www.google.com/webmasters/tools/richsnippets?url=chriszuber.com">here</a></li>
<li>Templates are easy to write and use - just write some regular HTML and put your placeholders in caps surrounded by "%"s.</li>
<li><em>Most</em> class methods are chainable - faster and easier coding</li>
<li>Updating design made easy thanks to <a href="https://developer.mozilla.org/en-US/docs/Web/CSS/Using_CSS_variables">CSS variables</a>. Use <a href="http://myth.io">Myth</a> (<strong>not included</strong>) when unsupported or in production</li>
<li>Developer friendly - Things like backing up and restoring a database are as easy as clicking a button in the developer menu, which is available to admin users everywhere via contextmenu (no navigation required, Firefox only at this point). Also provides easy access to debugging info, and the ability to print out debugging info to the browser's console makes tracking down bugs relatively easy. Lastly, PHP errors are saved to a database with file, line, and error message included, and can be searched, filtered, and even fixed from the browser!</li>
<li><p>Free/Libre/Open/Creative Commons/Public Domain… whatever applies to what you're refering to. All artwork, icons, and code are free to use for personal use. Special thanks to <a href="https://openclipart.org/">OpenClipart</a> and <a href="http://openfontlibrary.org/">Open Font Library</a>. All posts are automatically licensed under a <a href="http://creativecommons.org/licenses/by-sa/4.0/">Creative Commons 4.0 license</a>.</p>

<h2>Forks</h2></li>
<li><p><a href="https://github.com/shgysk8zer0/chriszuber" title="Main Repo">Main Repo</a></p></li>
</ul>

<h2>Creating your Repo</h2>

<p><strong><em>Update addresses to SSH as needed and available</em></strong></p>

<pre><code>git init
git remote add origin {Your Repo Address}
git remote add project_manager git://github.com/shgysk8zer0/chriszuber.git
git pull project_manager master
git push --set-upstream origin master
</code></pre>

<h2>Contact</h2>

<ul>
<li><a href="mailto:shgysk8zer0@gmail.com" title="Email Developer">Email Developer</a></li>
<li><a href="https://github.com/shgysk8zer0/chriszuber/issues" title="Report Bugs, request enhancements, etc">Issues Page</a></li>
</ul>

<h2>Other Info</h2>

<h3>Tested Using:</h3>

<ul>
<li>Apache/2.4.7</li>
<li>PHP 5.5.9</li>
<li>MySQL 5.5.37</li>
<li>Ubuntu 13.10 &amp; 14.04 64bit</li>
<li>Firefox 30+ &amp; Chrome 35+ (problematic)</li>
</ul>

<h3>Required PHP Modules:</h3>

<ul>
<li><a href="http://php.net/manual/en/book.pdo.php">PDO</a></li>
<li>date</li>
<li><a href="http://php.net/manual/en/book.mcrypt.php">mcrypt</a></li>
</ul>

<h3>Required Apache Modules:</h3>

<ul>
<li><a href="http://httpd.apache.org/docs/2.2/mod/mod_headers.html">mod_headers</a></li>
<li><a href="http://httpd.apache.org/docs/2.2/mod/mod_mime.html">mod_mime</a></li>
<li><a href="http://httpd.apache.org/docs/2.2/mod/mod_include.html">mod_include</a></li>
<li><a href="http://httpd.apache.org/docs/2.2/mod/mod_rewrite.html">mod_rewrite</a></li>
</ul>

<h3>Recommended for CSS editing</h3>

<p>Uses <a href="https://developer.mozilla.org/en-US/docs/Web/CSS/Using_CSS_variables">CSS-variables</a> (currently Firefox 31+ only) in default stylesheet.
The <a href="http://nodejs.org/" title="Node.js Homepage">Node.js</a> plugin <a href="http://www.myth.io" title="Myth Homepage">Myth</a> creates fully vendor-prefixed CSS from the default CSS,
replaces variables with their values, as well as combining CSS files using @import
while still allowing the original to be used as CSS where supported</p>

<p><em>Installation and configurations for Ubuntu</em>
    sudo apt-get install nodejs npm
    sudo ln -s /usr/bin/nodejs /usr/bin/node
    sudo npm install -g myth
Then to generate...
    myth stylesheets/style.css stylesheets/style.out.css
    myth -c stylesheets/combined.css stylesheets/combined.out.css</p>

<h3>Required Browser Features:</h3>

<p><em>These features only found in current versions of Firefox and Chrome</em></p>

<h4>HTML5</h4>

<ul>
<li><a href="https://developer.mozilla.org/en-US/docs/HTML/Element">New Element support</a> (header, main, footer, nav, progress, menu, datalist)</li>
<li><a href="https://developer.mozilla.org/en-US/docs/Web/HTML/Element/Input">New input types</a> (email, number, date) with validation (pattern, required, min/max)</li>
<li><a href="https://developer.mozilla.org/en-US/docs/Web/SVG">SVG</a></li>
<li><a href="https://developer.mozilla.org/en-US/docs/Web/Guide/HTML/Content_Editable">contenteditable</a>, <a href="http://www.w3schools.com/tags/att_global_contextmenu.asp">contextmenu</a> (Not supported in Chrome), and <a href="https://developer.mozilla.org/en-US/docs/Web/HTML/Element/datalist">list</a> attributes</li>
</ul>

<h4>JavaScript</h4>

<ul>
<li><a href="https://developer.mozilla.org/en-US/docs/Web/API/Document.querySelectorAll">querySelectorAll</a></li>
<li><a href="https://developer.mozilla.org/en-US/docs/Web/API/XMLHttpRequest">XMLHttpRequest</a></li>
<li><a href="https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Promise">Promises</a></li>
<li><a href="https://developer.mozilla.org/en-US/docs/Web/API/EventTarget.addEventListener">Event Listeners</a></li>
<li><a href="https://developer.mozilla.org/en-US/docs/Web/API/FileReader">FileReader</a></li>
<li><a href="https://developer.mozilla.org/en-US/docs/Web/API/MutationObserver">Mutation Observers</a></li>
<li><a href="https://developer.mozilla.org/en-US/docs/Web/Guide/Using_FormData_Objects">FormData</a></li>
<li><a href="https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/JSON/parse">JSON.parse</a></li>
<li><a href="https://developer.mozilla.org/en-US/docs/Web/API/HTMLElement.dataset">dataset</a></li>
<li><a href="https://developer.mozilla.org/en-US/docs/Web/API/Element.classList">classList</a></li>
<li><a href="https://developer.mozilla.org/en-US/docs/Web/Guide/API/DOM/Storage">local &amp; sessionStorage</a></li>
</ul>

<h4>CSS</h4>

<ul>
<li><a href="https://developer.mozilla.org/en-US/docs/Web/CSS/Using_CSS_variables">Variables</a> for development</li>
<li><a href="https://developer.mozilla.org/en-US/docs/Web/Guide/CSS/Flexible_boxes">Flexbox</a></li>
<li><a href="https://developer.mozilla.org/en-US/docs/Web/Guide/CSS/Media_queries">@media</a> &amp; <a href="https://developer.mozilla.org/en-US/docs/Web/CSS/@supports">@supports</a></li>
<li><a href="https://developer.mozilla.org/en-US/docs/Web/CSS/linear-gradient">linear &amp; radial-gradient</a></li>
<li><a href="https://developer.mozilla.org/en-US/docs/Web/CSS/font">Woff fonts</a></li>
<li><a href="https://developer.mozilla.org/en-US/docs/Web/CSS/length">Responsive Units</a> (rem, vw, etc)</li>
</ul>
