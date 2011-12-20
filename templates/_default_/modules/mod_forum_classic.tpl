<table width="100%" cellspacing="0" cellpadding="5" border="0" >
{foreach key=tid item=thread from=$threads}
	<tr>
		<td style="font-size: 12px;" align="left" valign="top" width="">
			<div><a href="{$thread.topichref}" style="font-weight:bold">{$thread.topic}</a></div>
			<div class="thread_desc">{$thread.topicdesc}</div>
		</td>
		<td style="font-size: 12px;" class="" valign="top" width="120">
			<div><strong>Автор:</strong><br><a href="{$thread.starterhref}">{$thread.starter}</a>
		</td>		
		<td style="font-size: 12px;" class="" valign="top" width="200">
			<div><strong>Последнее сообщение: </strong></div>
			<div>{$thread.date} от <a href="{$thread.authorhref}">{$thread.author}</a></div>
		</td>
	</tr>
{/foreach}
</table>