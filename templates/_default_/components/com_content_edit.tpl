{* ================================================================================ *}
{* ================= Редактирование/создание статьи =============================== *}
{* ================================================================================ *}

<div class="con_heading">{$pagetitle}</div>

<form id="addform" name="addform" method="post" action="" enctype="multipart/form-data">
	<table width="605" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td>
				<table width="700" border="0" cellspacing="5" class="proptable">
					<tr>
						<td width="" valign="top">
							<strong>Заголовок статьи:</strong><br />
							<span class="hinttext">{$LANG.SHOW_ON_SITE}</span>
						</td>
						<td width="350" valign="top">
						 	<input name="title" type="text" id="title" style="width:350px" value="{$mod.title}" />
						</td>
					</tr>
					<tr>
						<td valign="top">
							<strong>Теги статьи:</strong><br />
							<span class="hinttext">{$LANG.KEYWORDS_TEXT}</span>
						</td>
						<td valign="top">
							<input name="tags" type="text" id="tags" style="width:350px" value="{$mod.tags}" />
							<script type="text/javascript">
								{$autocomplete_js}
							</script>
						</td>
					</tr>
                    {if $cfg.img_users}
					<tr>
						<td valign="top" style="padding-top:8px">
							<strong>Фотография:</strong>
						</td>
						<td>
                            {if $mod.image}
                                <div style="padding-bottom:10px">
                                    <img src="/images/photos/small/{$mod.image}" border="0" />
                                </div>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td width="16"><input type="checkbox" id="delete_image" name="delete_image" value="1" /></td>
                                        <td><label for="delete_image">Удалить фотографию</label></td>
                                    </tr>
                                </table>
                            {/if}
							<input type="file" name="picture" style="width:350px" />
						</td>
					</tr>
                    {/if}
					{if $do=='addarticle'}
					<tr>
						<td valign="top">
							<strong>{$LANG.CAT}:</strong><br />
							<div><span class="hinttext">{$LANG.WHERE_LOCATE_ARTICLE}</span></div>
							{if $is_admin}
								<div style="margin-top:10px"><span class="hinttext">{$LANG.FOR_ADD_ARTICLE_ON} <a href="/admin/index.php?view=cats">{$LANG.IN_CONFIG}</a> {$LANG.FOR_ADD_ARTICLE_ON_TEXT}</span></div>
							{/if}
						</td>
						<td valign="top">
							<select name="category_id" size="8" id="category_id" style="width:350px">
								{$pubcats}
							</select>
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
	<table width="100%" border="0">
		<tr>
			<td>
                <h3>{$LANG.ARTICLE_ANNOUNCE}</h3>
				<div>{wysiwyg name='description' value=$mod.description height=200 width='90%' toolbar='Basic'}</div>

				<h3>{$LANG.ARTICLE_TEXT}</h3>
				<div>{wysiwyg name='content' value=$mod.content height=450 width='90%' toolbar='Admin'}</div>
			</td>
		</tr>
	</table>
				  
	{if $do=='editarticle'}
		{$add_notice}
	{/if}

    <script type="text/javascript">
        {literal}
            function submitArticle(){
                if (!$('input#title').val()){ alert('Укажите заголовок статьи'); return false; }
        {/literal}
            {if $do=='addarticle'}
                {literal}
                    if (!$('select#category_id').val()){ alert('Выберите раздел'); return false; }
                {/literal}
            {/if}
        {literal}
                $('form#addform').submit();
            }
        {/literal}
    </script>

	<p>
        <input name="add_mod" type="hidden" value="1" />
		<input name="savebtn" type="button" onclick="submitArticle()" id="add_mod" {if $do=='addarticle'} value="{$LANG.ADD_ARTICLE}" {else} value="{$LANG.SAVE_CHANGES}" {/if} />
		<input name="back" type="button" id="back" value="{$LANG.CANCEL}" onclick="window.history.back();"/>
		{if $do=='editarticle'}
			<input name="id" type="hidden" value="{$mod.id}" />
		{/if}
	</p>
</form>
