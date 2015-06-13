<!--
	Template for posts/articles

	@param string %TITLE%
	@param string %TAGS%
	@param string %CONTENT%
	@param string %AUTHOR%
	@param string %AUTHOR_URL%
	@param string %DATE%
	@param string %DATETIME%
	@param string %HOME%
	@param string %LICENSE%
	@param string %COMMENTS%
-->
		<article>
			<header class="sticky">
				<h1 itemprop="headline">%TITLE%</h1>
				<nav>
					<details>
						<summary title="Tags" data-icon=","></summary>
						<div itemprop="keywords">%TAGS%</div>
					</details>
				</nav>
			</header>
			<section itemprop="text">%CONTENT%</section>
			<footer>
				%LICENSE%
				%COMMENTS%
<!--
	Navigation Section: How will I get previous and next?
	Try something like :
	$DB->prepare("
		SELECT `url` AS `next`
		FROM `posts`
		WHERE `id` = (
			SELECT `id`
			FROM `posts`
			WHERE `title` = :title
		) + 1
	")
				<hr />
				<a href="%PREV%" rel="prev" title="Previous"></a>
				<a href="%HOME%" rel="bookmark" title="home" class="logo"><img src="images/icons/home.svgz"/ alt="Home"></a>
				<a href="%NEXT%" rel="next" title="Next"></a>
-->
			</footer>
		</article>
