<menu type="context" id="wysiwyg_menu">
	<!--https://developer.mozilla.org/en-US/docs/Midas-->
	<menu label="Create">
		<menu label="Headings">
			<menuitem label="H1" data-editor-command="heading" data-editor-value="H1"></menuitem>
			<menuitem label="H2" data-editor-command="heading" data-editor-value="H2"></menuitem>
			<menuitem label="H3" data-editor-command="heading" data-editor-value="H3"></menuitem>
			<menuitem label="H4" data-editor-command="heading" data-editor-value="H4"></menuitem>
			<menuitem label="H5" data-editor-command="heading" data-editor-value="H5"></menuitem>
			<menuitem label="H6" data-editor-command="heading" data-editor-value="H6"></menuitem>
		</menu>
		<menu label="List">
			<menuitem label="Ordered" data-editor-command="insertorderedlist"></menuitem>
			<menuitem label="Unordered" data-editor-command="insertunorderedlist"></menuitem>
		</menu>
		<menuitem label="Link" data-editor-command="createlink" data-prompt="Enter link location"></menuitem>
		<menuitem label="Image" data-editor-command="insertimage" data-prompt="Enter image location"></menuitem>
		<menuitem label="Citation" data-editor-command="inserthtml" data-selection-to="cite"></menuitem>
		<menuitem label="Quote" data-editor-command="inserthtml" data-selection-to="q"></menuitem>
		<menuitem label="Code" data-editor-command="inserthtml" data-selection-to="code"></menuitem>
		<menuitem label="Figure" data-editor-command="inserthtml" data-selection-to="figure"></menuitem>
		<menuitem label="Figure Caption" data-editor-command="inserthtml" data-selection-to="figcaption"></menuitem>
		<menuitem label="Highlighted Text" data-editor-command="inserthtml" data-selection-to="mark"></menuitem>
		<menuitem label="Custom HTML" data-editor-command="inserthtml" data-prompt="Enter the HTML to insert."></menuitem>
	</menu>
	<menu label="Text Style">
		<menuitem label="Bold" data-editor-command="bold"></menuitem>
		<menuitem label="Italics" data-editor-command="italic"></menuitem>
		<menuitem label="Underline" data-editor-command="underline"></menuitem>
		<menuitem label="Strike Through" data-editor-command="strikethrough"></menuitem>
		<menuitem label="Big" data-editor-command="big"></menuitem>
		<menuitem label="Small" data-editor-command="small"></menuitem>
		<menuitem label="Superscript" data-editor-command="superscript"></menuitem>
		<menuitem label="Subscript" data-editor-command="subscript"></menuitem>
		<menu label="Font">
			<menuitem label="Larger" data-editor-command="increasefontsize"></menuitem>
			<menuitem label="Smaller" data-editor-command="decreasefontsize"></menuitem>
			<menu label="Font Family">
				<menuitem label="Alice" data-editor-command="fontname" data-editor-value="Alice"></menuitem>
				<menuitem label="Web Symbols" data-editor-command="fontname" data-editor-value="Web Symbols"></menuitem>
				<menuitem label="Acme" data-editor-command="fontname" data-editor-value="Acme"></menuitem>
				<menuitem label="GNUTypewriter" data-editor-command="fontname" data-editor-value="GNUTypewriter"></menuitem>
				<menuitem label="PressStart" data-editor-command="fontname" data-editor-value="PressStart"></menuitem>
				<menuitem label="GNUTypewriter" data-editor-command="fontname" data-editor-value="GNUTypewriter"></menuitem>
				<menuitem label="Other?" data-editor-command="fontname" data-prompt="What font would you like to use?"></menuitem>
			</menu>
		</menu>
	</menu>
	<menu label="Indentation">
		<menuitem label="Increase" data-editor-command="indent"></menuitem>
		<menuitem label="Decrease" data-editor-command="outdent"></menuitem>
	</menu>
	<menu label="Justify">
		<menuitem label="Center" data-editor-command="justifycenter"></menuitem>
		<menuitem label="Left" data-editor-command="justifyleft"></menuitem>
		<menuitem label="Right" data-editor-command="justifyright"></menuitem>
		<menuitem label="Full" data-editor-command="justifyfull"></menuitem>
	</menu>
	<menu label="Special Characters">
		<menu label="Punctuation">
			<?php foreach(['ldquo', 'ldquo', 'lsquo', 'rsquo', 'rdquo', 'sbquo', 'laquo', 'raquo', 'iquest', 'ndash', 'mdash', '#133', 'dagger', 'Dagger'] as $character):?>
			<menuitem label="&<?=$character?>;" data-editor-command="inserthtml" data-editor-value="&<?=$character?>;"></menuitem>
			<?php endforeach?>
		</menu>
		<menu label="Legal">
			<?php foreach(['copy', 'trade', 'reg'] as $character):?>
			<menuitem label="&<?=$character?>;" data-editor-command="inserthtml" data-editor-value="&<?=$character?>;"></menuitem>
			<?php endforeach?>
		</menu>
		<menu label="Currency">
			<?php foreach(['curren', 'cent', 'pound', 'yen', 'euro'] as $character):?>
			<menuitem label="&<?=$character?>;" data-editor-command="inserthtml" data-editor-value="&<?=$character?>;"></menuitem>
			<?php endforeach?>
		</menu>
		<menu label="Mathematics">
			<menuitem label="&#8734;" data-editor-command="inserthtml" data-editor-value="&#8734;"></menuitem>
			<menu label="Fractions">
				<?php foreach(['frac12', 'frac13', 'frac14', 'frac18', 'frac38', 'frac58', 'frac34', 'frac78'] as $character):?>
				<menuitem label="&<?=$character?>;" data-editor-command="inserthtml" data-editor-value="&<?=$character?>;"></menuitem>
				<?php endforeach?>
			</menu>
			<menu label="Operators">
				<?php foreach(['plusmn', 'times', '#8729', 'divide', '#8730', '#8800', '#8776', '#8804', '#8805', '#8747', '#8721', '#8706', '#8710', '#131', 'deg'] as $character):?>
				<menuitem label="&<?=$character?>;" data-editor-command="inserthtml" data-editor-value="&<?=$character?>;"></menuitem>
				<?php endforeach?>
			</menu>
			<menu label="Exponents">
				<?php foreach(range(1, 3) as $character):?>
				<menuitem label="&sup<?=$character?>;" data-editor-command="inserthtml" data-editor-value="&<?=$character?>;"></menuitem>
				<?php endforeach?>
			</menu>
		</menu>
		<menu label="Other">
			<?php foreach(['spades', 'clubs', 'diams', 'hearts', '#9792', '#9794', 'larr', 'rarr', 'uarr', 'darr', '#9833','#9834', '#9836', '#9837', '#9839'] as $character):?>
			<menuitem label="&<?=$character?>;" data-editor-command="inserthtml" data-editor-value="&<?=$character?>;"></menuitem>
			<?php endforeach?>
		</menu>
	</menu>
	<menuitem label="Horizontal Rule" data-editor-command="inserthorizontalrule"></menuitem>
	<menu label="Selection">
		<menuitem label="Select All" data-editor-command="selectall"></menuitem>
		<menuitem label="Clear Formatting" data-editor-command="removeformat"></menuitem>
		<menuitem label="Remove Links" data-editor-command="unlink"></menuitem>
	</menu>
	<menu label="History">
		<menuitem label="Undo" data-editor-command="undo"></menuitem>
		<menuitem label="Redo" data-editor-command="redo"></menuitem>
	</menu>
</menu>