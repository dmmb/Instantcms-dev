<table cellspacing="4" border="0" width="100%">
{foreach key=aid item=quest from=$faq}	
	<tr>
		<td width="20" valign="top"><img src="/images/markers/faq.png" border="0" /></td>
		<td>
			<div class="mod_faq_quest">{$quest.quest}</div>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><span class="mod_faq_date">{$quest.date}</span> &mdash; <a href="{$quest.href}">���������...</a></td>
	</tr>
{/foreach}
</table>