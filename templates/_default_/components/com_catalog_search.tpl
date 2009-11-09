{* ================================================================================ *}
{* ==================== Форма поиска по рубрике каталога ========================== *}
{* ================================================================================ *}

<h1 class="con_heading">{$LANG.SEARCH_IN_CAT}</h1>
<div class="con_description">
	<strong>{$LANG.CAT}:</strong> <a href="/catalog/{$menuid}/{$cat.id}">{$cat.title}</a>
</div>

<table width="100%" border="0" cellpadding="10" style="background-color:#EBEBEB; border:solid 1px gray">	
	<tr>
		<td>
			<strong>{$LANG.ADVICE}:</strong> {$LANG.ADVICE_TEXT} <br /><br/>
			<strong>%</strong> - {$LANG.ANY_SEQ_LETTERS}<br />
			<strong>?</strong> - {$LANG.ANY_ONE_LETTER}
		 </td>
	</tr>
</table>		  				

<p><strong>{$LANG.FILL_FIELDS}:</strong></p>
<form action="/catalog/{$menuid}/{$id}/search.html" name="searchform" method="post" >
	<table width="100%" border="0" cellspacing="5">	
		<tr>
			<td width="160" valign="top">{$LANG.TITLE}: </td>
			<td valign="top"><input style="border: solid 1px gray" name="title" type="text" id="title" size="35" value="" /></td>
		</tr>
	</table>		  
	{foreach key=tid item=value from=$fstruct}	
		<table width="100%" border="0" cellspacing="5">	
			<tr>
				<td width="160" valign="top">{$value}: </td>
				<td valign="top"><input style="border: solid 1px gray" name="fdata[{$tid}]" type="text" id="fdata[]" size="35" value="" /> </td>
			</tr>
		</table>		  
	{/foreach}
	<table width="100%" border="0" cellspacing="5">	
		<tr>
			<td width="160" valign="top">Тэги, через пробел: </td>
			<td valign="top"><input style="border: solid 1px gray" name="tags" type="text" id="tags" size="35" value="" /><br/><?php echo tagsList($id);?></td>
		</tr>
	</table>		  
	<p>
		<input type="submit" name="gosearch" value="{$LANG.SEARCH_IN_CAT}" />
		<input type="button" onclick="window.history.go(-1);" name="cancel" value="{$LANG.CANCEL}" />
	</p>
</form>