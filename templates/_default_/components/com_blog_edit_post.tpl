<form style="margin-top:15px" action="" method="post" name="msgform" enctype="multipart/form-data">
	{if !$blog.showcats}
		<input type="hidden" name="cat_id" value="0"/>
	{/if}
	<table width="100%" border="0" cellpadding="6" cellspacing="0">
		<tr>
			<td width="160"><strong>{$LANG.TITLE_POST}: </strong></td>
		  	<td><input name="title" type="text" id="title" style="width:400px" value="{$mod.title}"/></td>
		</tr>

		{if $blog.showcats}
			<tr>
				<td><strong>{$LANG.BLOG_CAT}:</strong></td>
				<td>
					<select name="cat_id" id="cat_id" style="width:400px">
						<option value="0" {if !isset($mod.cat_id) || $mod.cat_id==0}  selected {/if}>{$LANG.WITHOUT_CAT}</option>
						{$cat_list}
					</select>
				</td>
			</tr>
		{/if}
		
		{if $myblog}
			<tr>
				<td><strong>{$LANG.SHOW_POST}:</strong></td>
				<td>
					<select name="allow_who" id="allow_who" style="width:400px">
						<option value="all" {if !isset($mod.allow_who) || $mod.allow_who=='all'} selected {/if}>{$LANG.TO_ALL}</option>
						<option value="friends" {if $mod.allow_who=='friends'} selected {/if}>{$LANG.TO_MY_FRIENDS}</option>
						<option value="nobody" {if $mod.allow_who=='nobody'} selected {/if}>{$LANG.TO_ONLY_ME}</option>
					</select>
				</td>
			</tr>
		{else}
			<input type="hidden" name="allow_who" value="all" />
		{/if}
		
		<tr>
			<td><strong>{$LANG.YOUR_MOOD}:</strong></td>
			<td><input name="feel" type="text" id="feel" style="width:400px" value="{$mod.feel}"/></td>
		</tr>
		<tr>
			<td><strong>{$LANG.PLAY_MUSIC}:</strong></td>
			<td><input name="music" type="text" id="music" style="width:400px" value="{$mod.music}"/></td>
		</tr>			
		<tr>
			<td colspan="2">
				<div class="usr_msg_bbcodebox">{$bb_toolbar}</div>
				{$smilies}
				{$autogrow}
				<div><textarea class="ajax_autogrowarea" name="content" id="message">{$msg}</textarea></div>
                <div style="margin-top:12px;margin-bottom:15px;">
                    <strong>{$LANG.IMPORTANT}:</strong> {$LANG.CUT_TEXT},
                    <a href="javascript:addTagCut('message');">{$LANG.ADD_CUT_TAG}</a> {$LANG.BETWEEN}.
                </div>
			</td>
		</tr>
		<tr>
			<td>
				<strong>{$LANG.TAGS}:</strong><br />
				<span><small>{$LANG.KEYWORDS}</small></span>
			</td>
			<td>
				<input name="tags" type="text" id="tags" style="width:400px" value="{$tagline}"/>
				
				<script type="text/javascript">
					{$autocomplete_js}
				</script>
			</td>
		</tr>
	</table>
	<p>
		<input name="goadd" type="submit" id="goadd" value="{$LANG.SAVE_POST}" />
		<input name="cancel" type="button" onclick="window.history.go(-1)" value="{$LANG.CANCEL}" />
	</p>
</form>