{* ================================================================================ *}
{* ============================== Просмотр вопроса FAQ ============================ *}
{* ================================================================================ *}

<div class="con_heading">{$LANG.QUESTION_VIEW}</div>
				
<table cellspacing="5" cellpadding="0" border="0" width="100%">
	<tr>
		<td width="35" valign="top"><img src="/templates/_default_/images/icons/big/faq_quest.png" border="0" /></td>
		<td width="" valign="top">
			<div class="faq_questtext">{$quest.quest}</div>
            <div class="faq_questuser"><a href="{profile_url login=$quest.login}">{$quest.nickname}</a></div>
			<div class="faq_questdate">{$quest.pubdate}</div>
		</td>	
	</tr>
</table>

<table cellspacing="5" cellpadding="0" border="0" width="100%" style="margin:15px 0px;">
	<tr>
		<td width="35" valign="top">
			<img src="/templates/_default_/images/icons/big/faq_answer.png" border="0" />
		</td>
		<td width="" valign="top">
			<div class="faq_answertext">{$quest.answer}</div>
			<div class="faq_questdate">{$quest.answerdate}</div>
		</td>	
	</tr>
</table>

{comments target='faq' target_id=$quest.id}