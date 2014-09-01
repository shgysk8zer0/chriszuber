<!--
	Template for error pages

	@param int %STATUS%
	@param string %MESSAGE%
	@param string %HOME%
-->
<div data-error="%STATUS%">
	<h2>Either you made a left instead of a right, or I divided by zero.</h2><br />
	<h3>%MESSAGE%</h3><br />
	<b>What should I do?</b>
	<ul>
		<li>Go back to the <a href="%HOME%" title="home">home page</a></li><br />
		<li>If you believe that I made an error, you may <a href="https://github.com/shgysk8zer0/chriszuber/issues" title="File a bug report">file a bug report</a></li><br />
		<li>You could try searching for what you're looking for <label for="tags_input" role="button" data-icon="L" data-scroll-to="#tags_input" title="Search Tags"></label></li><br />
		<li>Otherwise, just carry on as usual. Maybe you spelled something wrong, or maybe you followed a bad link.</li>
	</ul>
	<a href="%HOME%" title="Home"><img src="super-user.svgz" alt="Logo"/></a>
	<hr /><br />
	<i>For all of you nerds out there, here is a <code>print_r(parse_url())</code></i>
	<pre><code>
	%DUMP%
	</code></pre>
</div>
