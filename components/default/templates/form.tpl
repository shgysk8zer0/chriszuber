<!--
	Generic form tempalte
	@param string %NAME%
	@param string %ACTION%
	@param string %METHOD%
	@param string %INPUTS%
-->
<form name="%NAME%" action="%ACTION%" method="%METHOD%">
	<fieldset form="%NAME%">
		%INPUTS%
	</fieldset>
	<button type="submit" title="Submit" data-icon="."></button>
	<button type="reset" title="Clear From" data-icon="*"></button>
</form>
