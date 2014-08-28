<?php
	if(!$DB->connected) return null;
	$contact = $DB->name_value('contact');
?>
<dialog id="contactDialog">
	<button type="button" title="Close Contact Info" data-close="#contactDialog"></button><br />
	<address itemprop="author" itemtype="http://schema.org/Person" itemscope>
		<div>
			<?php if(isset($contact->picture)):?><img itemprop="image" src="<?=URL?>/images/<?=$contact->picture?>" alt="Picture of Chris Zuber"/><br /><?php endif?>
			<?php if(isset($contact->first_name) and isset($contact->last_name)):?>
			<span itemprop="name">
				<b itemprop="givenName"><?=ucwords($contact->first_name)?></b>
				<b itemprop="familyName"><?=ucwords($contact->last_name)?></b><br />
			</span>
			<?php endif?>
			<?php if(isset($contact->job_title)):?><i itemprop="jobTitle"><?=ucwords($contact->job_title)?></i><?php endif?>
			<?php if(isset($contact->company_name)):?>- <b itemprop="worksFor">
				<a itemprop="url" title="<?=ucwords($contact->company_name)?> Homepage" href="<?=ucwords($contact->company_url)?>"><?=ucwords($contact->company)?></a>
			</b>
			<?php endif?>

		</div><br />
		<div>
			<?php if(isset($contact->cell_phone)):?><a href="tel:<?=$contact->cell_phone?>" target="_blank" title="Call my cell phone" itemprop="telephone"><?=$contact->cell_phone?> <?php include('images/icons/mobile_icon.svg')?></a><br /><?php endif?>
			<?php if(isset($contact->email)):?><a href="mailto:<?=$contact->email?>" target="_blank" title="Send me an email" itemprop="email"><?=$contact->email?> <?php include('images/icons/envelope.svg')?></a><br /><br /><?php endif?>
		</div>
		<?php if(isset($contact->street_address) or isset($contact->city) or isset($contact->state) or isset($contact->zip)):?>
		<div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
			<?php if(isset($contact->street_address)):?><span itemprop="streetAddress"><?=ucwords($contact->street_address)?></span><br /><?php endif?>
			<?php if(isset($contact->city)):?><span itemprop="addressLocality"><?=ucwords($contact->city)?></span>,<?php endif?>
			<?php if(isset($contact->state)):?><span itemprop="addressRegion"><?=strtoupper($contact->state)?></span><?php endif?>
			<?php if(isset($contact->zip)):?><span itemprop="postalCode"><?=$contact->zip?></span><br /><?php endif?>
		</div>
		<br /><br />
		<?php endif?>
	</address>
</dialog>
