{* ================================================================================ *}
{* ============================ Просмотр категории FAQ ============================ *}
{* ================================================================================ *}

<div class="con_heading">{$pagetitle}</div>

{* ============================ Подкатегории ============================ *}
{if $is_subcats}
	{if $id>0}
		<div class="faq_subcats">
	{else}
		<div class="faq_cats">
	{/if}
		<table cellspacing="5" width="100%">
			{foreach key=tid item=subcat from=$subcats}
				<tr>
					<td width="32" valign="top"><img src="/components/faq/images/folder.gif" border="0" /></td>
					<td width="" valign="">				    
						<div style="margin:0px;"><a href="/faq/{$subcat.id}">{$subcat.title}</a></div>
						{if $subcat.description}
							<div style="margin:0px;">{$subcat.description}</div>
						{/if}
					</td>				
				</tr>
			{/foreach}
		</table>
	</div>
{/if}

{* ============================ Список вопросов ============================ *}
{if $is_quests}
    {if $id==0}
        <h1 class="con_heading">{$LANG.LAST_QUESTIONS}</h1>
    {/if}
	{foreach key=tid item=quest from=$quests}
		<div class="faq_quest">
			<table cellspacing="5" cellpadding="0" border="0" width="100%">
				<tr>
					<td width="20" valign="top"><img src="/components/faq/images/quest.gif" border="0" /></td>
					<td width="" valign="middle">
						<div><a href="/faq/quest{$quest.id}.html">{$quest.quest}</a></div>
						<div class="faq_questdate">{$quest.pubdate}</div>
						<div class="faq_questcat"><a href="/faq/{$quest.cid}">{$quest.cat_title}</a></div>					
					</td>	
				</tr>
			</table>
		</div>
	{/foreach}
	{if $id > 0} {$pagebar} {/if}
{/if}
	
{* ============================ Ссылка "Задать вопрос" ============================ *}
<div class="faq_send_quest">
	<table cellspacing="3" cellpadding="0" border="0">
		<tr>
			<td width="16" valign="top"><img src="/components/faq/images/sendquest.gif" border="0" /></td>
			<td width="" valign="middle">
				<a href="/faq/sendquest{if $id>0}{$id}{/if}.html">{$LANG.SET_QUESTION}</a>
			</td>	
		</tr>
	</table>
</div>