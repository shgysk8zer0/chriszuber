<?php
	$els = [
		"Strong" => "strong",
		"Emphasis" => "em",
		"Deleted Text" => "del",
		"Inserted Text" => "ins",
		"Sample Text" => "samp",
		"Keyboard" => "kbd",
		"Variable" => "var",
		"Quote" => "q",
		"Citation" => "cite",
		"Highlighted Text" => "mark"
	];
	$fonts = [
		"Alice",
		"Web Symbols",
		"Acme",
		"PressStart",
		"GNUTypewriter",
		"Comfortaa",
		"Chancery",
		"Intuitive"
	];

	$colors = [
		'Red' => 'red',
		'Green' => 'green',
		'Blue' => 'blue',
		'Cyan' => 'cyan',
		'Magenta' => 'magenta',
		'Yellow' => 'yellow',
		'Orange' => 'orange',
		'Purple' => 'purple',
		'Pink' => 'pink',
		'Black' => 'black',
		'White' => 'white',
		'White Smoke' => 'whitesmoke',
		'Azure' => 'azure',
		'Gray' => 'gray',
		'Dim Gray' => 'dimgray',
		'Dark Gray' => 'darkgray',
		'Light Gray' => 'lightgray',
		'DarkSlateGray' => 'darkslategray',
		'Slate Gray' => 'slategray',
		'Ivory' => 'ivory',
		'Snow' => 'snow',
		'Lavender' => 'lavender',
		'Aqua' => 'aqua',
		'Navy' => 'navy',
		'Lime' => 'lime',
		'Lime Green' => 'limegreen',
		'Coral' => 'coral',
		'Crimson' => 'crimson',
		'Maroon' => 'maroon',
		'Tomato' => 'tomato',
		'Fuchsia' => 'fuchsia',
		'Salmon' => 'salmon',
		'Gold' => 'gold',
		'Green-Yellow' => 'greenyellow'
	];

	$special_characters = [
		'Punctuation' => [
			'ldquo',
			'rdquo',
			'lsquo',
			'rsquo',
			'laquo',
			'raquo',
			'sbquo',
			'iquest',
			'ndash',
			'mdash',
			'#133',
			'dagger',
			'Dagger'
		],
		'Legal' => [
			'copy',
			'trade',
			'reg'
		],
		'Currency' => [
			'curren',
			'cent',
			'pound',
			'yen',
			'euro'
		],
		'Fractions' => [
			'frac12',
			'frac13',
			'frac14',
			'frac18',
			'frac38',
			'frac58',
			'frac34',
			'frac78'
		],
		'Operators' => [
			'plusmn',
			'times',
			'#8729',
			'divide',
			'#8730',
			'#8800',
			'#8776',
			'#8804',
			'#8805',
			'#8747',
			'#8721',
			'#8706',
			'#8710',
			'#131',
			'deg'
		],
		'Other' => [
			'spades',
			'clubs',
			'diams',
			'hearts',
			'#9792',
			'#9794',
			'larr',
			'rarr',
			'uarr',
			'darr',
			'#9833',
			'#9834',
			'#9836',
			'#9837',
			'#9839'
		]
	];
?>
<menu type="context" id="wysiwyg_menu">
	<!--https://developer.mozilla.org/en-US/docs/Midas-->
	<menu label="Attributes">
		<menuitem label="Add Class"></menuitem>
		<menuitem label="Remove Class"></menuitem>
		<menuitem label="Set Attribute"></menuitem>
		<menuitem label="Remove Attribute"></menuitem>
	</menu>
	<menu label="Create">
		<menu label="Headings">
			<?php foreach(range(1, 6) as $h):?>
			<menuitem label="H<?=$h?>" data-editor-command="heading" data-editor-value="H<?=$h?>"></menuitem>
			<?php endforeach?>
		</menu>
		<menu label="List">
			<menuitem label="Unordered" icon="images/octicons/svg/list-unordered.svg" data-editor-command="insertunorderedlist"></menuitem>
			<menuitem label="Ordered" icon="images/octicons/svg/list-ordered.svg" data-editor-command="insertorderedlist"></menuitem>
		</menu>
		<menuitem label="Link" icon="images/octicons/svg/link.svg" data-editor-command="createlink" data-prompt="Enter link location"></menuitem>
		<menuitem label="Image" icon="images/octicons/svg/file-media.svg" data-editor-command="insertimage" data-prompt="Enter image location"></menuitem>
		<menuitem label="Figure" data-editor-command="inserthtml" data-selection-to="figure"></menuitem>
		<menuitem label="Figure Caption" data-editor-command="inserthtml" data-selection-to="figcaption"></menuitem>
		<menuitem label="Code" icon="images/octicons/svg/code.svg" data-editor-command="inserthtml" data-selection-to="code"></menuitem>
		<menuitem label="Pre-formatted Text" data-editor-command="inserthtml" data-selection-to="pre"></menuitem>
		<menuitem label="Custom HTML" icon="images/octicons/svg/file-code.svg" data-editor-command="inserthtml" data-prompt="Enter the HTML to insert."></menuitem>
	</menu>
	<menu label="Text Style">
		<menu label="Font">
			<menu label="Size">
				<menuitem label="+" data-editor-command="increasefontsize"></menuitem>
				<menuitem label="-" data-editor-command="decreasefontsize"></menuitem>
			</menu>
			<menu label="Font Family">
				<?php foreach($fonts as $font):?>
				<menuitem label="<?=$font?>" data-editor-command="fontname" data-editor-value="<?=$font?>"></menuitem>
				<?php endforeach?>
				<menuitem label="Other?" data-editor-command="fontname" data-prompt="What font would you like to use?"></menuitem>
			</menu>
			<menu label="Font Color">
				<?php foreach($colors as $name => $color):?>
					<menuitem label="<?=$name?>" icon="data:image/svg+xml;utf8,<?=rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1 1"><rect width="1" height="1" fill="' . $color . '"/></svg>');?>" data-editor-command="forecolor" data-editor-value="<?=$color?>" ></menuitem>
				<?php endforeach?>
			</menu>
		</menu>
		<menuitem label="Paragraph" icon="images/octicons/svg/file-text.svg" data-editor-command="insertparagraph"></menuitem>
		<menuitem label="Blockquote" icon="images/octicons/svg/quote.svg" data-editor-command="formatblock" data-editor-value="BLOCKQUOTE"></menuitem>
		<menuitem label="Bold" data-editor-command="bold"></menuitem>
		<menuitem label="Italics" data-editor-command="italic"></menuitem>
		<menuitem label="Underline" data-editor-command="underline"></menuitem>
		<menuitem label="Strike Through" data-editor-command="strikethrough"></menuitem>
		<menuitem label="Big" data-editor-command="big"></menuitem>
		<menuitem label="Small" data-editor-command="small"></menuitem>
		<menuitem label="Superscript" data-editor-command="superscript"></menuitem>
		<menuitem label="Subscript" data-editor-command="subscript"></menuitem>
		<menu label="Other">
			<?php foreach($els as $label => $tag):?>
			<menuitem label="<?=$label?>" data-editor-command="inserthtml" data-selection-to="<?=$tag?>"></menuitem>
			<?php endforeach?>
		</menu>
	</menu>
	<menu label="Indentation">
		<menuitem label="Increase" icon="images/octicons/svg/move-right.svg" data-editor-command="indent"></menuitem>
		<menuitem label="Decrease" icon="images/octicons/svg/move-left.svg" data-editor-command="outdent"></menuitem>
	</menu>
	<menu label="Justify">
		<menuitem label="Center" data-editor-command="justifycenter"></menuitem>
		<menuitem label="Left" icon="images/octicons/svg/jump-left.svg" data-editor-command="justifyleft"></menuitem>
		<menuitem label="Right" icon="images/octicons/svg/jump-right.svg" data-editor-command="justifyright"></menuitem>
		<menuitem label="Full" data-editor-command="justifyfull"></menuitem>
	</menu>
	<menu label="Special Characters">
		<?php foreach($special_characters as $type => $characters):?>
			<menu label="<?=ucwords($type)?>">
			<?php foreach($characters as $character):?>
				<menuitem label="&<?=$character?>;" data-editor-command="inserthtml" data-editor-value="&<?=$character?>;"></menuitem>
			<?php endforeach?>
			</menu>
		<?php endforeach?>
	</menu>
	<menuitem label="Line Break" data-editor-command="inserthtml" data-editor-value="<br />"></menuitem>
	<menuitem label="Horizontal Rule" icon="images/octicons/svg/horizontal-rule.svg" data-editor-command="inserthorizontalrule"></menuitem>
	<menu label="Selection">
		<menuitem label="Select All" data-editor-command="selectall"></menuitem>
		<menuitem label="Clear Formatting" icon="images/octicons/svg/circle-slash.svg" data-editor-command="removeformat"></menuitem>
		<menuitem label="Remove Links" data-editor-command="unlink"></menuitem>
	</menu>
	<menu label="History">
		<menuitem label="Undo" icon="images/octicons/svg/history.svg" data-editor-command="undo"></menuitem>
		<menuitem label="Redo" icon="images/octicons/svg/history.svg" data-editor-command="redo"></menuitem>
	</menu>
</menu>
