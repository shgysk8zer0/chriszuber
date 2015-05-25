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
		<button type="submit" title="Submit" data-icon="."></button>
		<button type="reset" title="Clear From" data-icon="*"></button>
	</fieldset>
</form>
