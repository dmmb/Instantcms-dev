{* ================================================================================ *}
{* ================= ��������������/�������� ������ =============================== *}
{* ================================================================================ *}

<div class="con_heading">{$pagetitle}</div>

<form id="addform" name="addform" method="post" action="" enctype="multipart/form-data">
    <div class="bar" style="padding:15px 10px">
	<table width="605" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td>
				<table width="700" border="0" cellspacing="5" class="proptable">
					<tr>
						<td width="230" valign="top">
							<strong>��������� ������:</strong><br />
							<span class="hinttext">{$LANG.SHOW_ON_SITE}</span>
						</td>
						<td valign="top">
						 	<input name="title" type="text" class="text-input" id="title" style="width:350px" value="{$mod.title|escape:'html'}" />
						</td>
					</tr>
					<tr>
						<td valign="top">
							<strong>���� ������:</strong><br />
							<span class="hinttext">{$LANG.KEYWORDS_TEXT}</span>
						</td>
						<td valign="top">
							<input name="tags" type="text" class="text-input" id="tags" style="width:350px" value="{$mod.tags|escape:'html'}" />
							<script type="text/javascript">
								{$autocomplete_js}
							</script>
						</td>
					</tr>
					{if $do=='addarticle'}
					<tr>
						<td valign="top">
							<strong>{$LANG.CAT}:</strong><br />
							<div><span class="hinttext">{$LANG.WHERE_LOCATE_ARTICLE}</span></div>
							{if $is_admin}
								<div style="margin-top:10px"><span class="hinttext">{$LANG.FOR_ADD_ARTICLE_ON} <a href="/{$adminDir}/index.php?view=cats">{$LANG.IN_CONFIG}</a> {$LANG.FOR_ADD_ARTICLE_ON_TEXT}</span></div>
							{/if}
						</td>
						<td valign="top">
							<select name="category_id" id="category_id" style="width:357px">
                                {foreach key=p item=pubcat from=$pubcats}
                                    <option value="{$pubcat.id}">
                                        {'--'|str_repeat:$pubcat.NSLevel} {$pubcat.title}
                                        {if $is_billing && $pubcat.cost && $dynamic_cost}
                                            ({$LANG.BILLING_COST}: {$pubcat.cost|spellcount:$LANG.BILLING_POINT1:$LANG.BILLING_POINT2:$LANG.BILLING_POINT10})
                                        {/if}
                                    </option>
                                {/foreach}
							</select>
						</td>
					</tr>
					{/if}
                    {if $cfg.img_users}
					<tr>
						<td valign="top" style="padding-top:8px">
							<strong>����������:</strong>
						</td>
						<td>
                            {if $mod.image}
                                <div style="padding-bottom:10px">
                                    <img src="/images/photos/small/{$mod.image}" border="0" />
                                </div>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td width="16"><input type="checkbox" id="delete_image" name="delete_image" value="1" /></td>
                                        <td><label for="delete_image">������� ����������</label></td>
                                    </tr>
                                </table>
                            {/if}
							<input type="file" name="picture" style="width:350px" />
						</td>
					</tr>
                    {/if}
                    {if $do=='editarticle'}
                        <input type="hidden" name="category_id" value="{$mod.category_id}" />
                    {/if}
				</table>
			</td>
		</tr>
	</table>
    </div>
	<table width="100%" border="0">
		<tr>
			<td>
                <h3>{$LANG.ARTICLE_ANNOUNCE}</h3>
				<div>{wysiwyg name='description' value=$mod.description height=200 width='100%' toolbar='Basic'}</div>

				<h3>{$LANG.ARTICLE_TEXT}</h3>
				<div>{wysiwyg name='content' value=$mod.content height=450 width='100%' toolbar='Admin'}</div>
			</td>
		</tr>
	</table>

	{if $do=='editarticle'}
		{$add_notice}
	{/if}

    <script type="text/javascript">
        {literal}
            function submitArticle(){
                if (!$('input#title').val()){ alert('������� ��������� ������'); return false; }
        {/literal}
            {if $do=='addarticle'}
                {literal}
                    if (!$('select#category_id').val()){ alert('�������� ������'); return false; }
                {/literal}
            {/if}
        {literal}
                $('form#addform').submit();
            }
        {/literal}
    </script>

	<p style="margin-top:15px">
        <input name="add_mod" type="hidden" value="1" />
		<input name="savebtn" type="button" onclick="submitArticle()" id="add_mod" {if $do=='addarticle'} value="{$LANG.ADD_ARTICLE}" {else} value="{$LANG.SAVE_CHANGES}" {/if} />
		<input name="back" type="button" id="back" value="{$LANG.CANCEL}" onclick="window.history.back();"/>
		{if $do=='editarticle'}
			<input name="id" type="hidden" value="{$mod.id}" />
		{/if}
	</p>
</form>
