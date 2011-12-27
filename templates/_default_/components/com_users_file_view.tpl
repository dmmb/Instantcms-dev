{* ================================================================================ *}
{* ==================== Список файлов ============================================= *}
{* ================================================================================ *}
{strip}
<div class="con_heading"><a href="{profile_url login=$usr.login}">{$usr.nickname}</a> &rarr; {$LANG.FILES}</div>
{if $is_files}
<div class="usr_files_orderbar">
  <table width="100%" cellspacing="0" cellpadding="2">
    <tr>
      {$pagination}
      <td width="15">&nbsp;</td>
      <td width="80"><strong>{$LANG.FILE_COUNT}: </strong>{$total_files}</td>
      {if $myprofile}
      		{if $cfg.filessize}
          <td width="130"><strong>{$LANG.FREE}: </strong>{$free_mb} {$LANG.MBITE}</td>
            {else}
            	<td width="130"></td>
          	{/if}
          <td width="16"><img src="/components/users/images/upload.gif" border="0" /></td>
          <td width="100"><a href="addfile.html">{$LANG.UPLOAD_FILES}</a></td>
      {/if}
      {if $total_files > 1}
      <td align="right"><form name="orderform" method="post" action="" style="margin:0px">
          <input type="button" class="usr_files_orderbtn" onclick="orderPage('pubdate')" name="order_date" value="{$LANG.ORDER_BY_DATE}" {if $orderby=='pubdate'} disabled {/if} />
          <input type="button" class="usr_files_orderbtn" onclick="orderPage('filename')" name="order_title" value="{$LANG.ORDER_BY_NAME}" {if $orderby=='filename'} disabled {/if} />
          <input type="button" class="usr_files_orderbtn" onclick="orderPage('filesize')" name="order_size" value="{$LANG.ORDER_BY_SIZE}" {if $orderby=='filesize'} disabled {/if} />
          <input type="button" class="usr_files_orderbtn" onclick="orderPage('hits')" name="order_hits" value="{$LANG.ORDER_BY_DOWNLOAD}" {if $orderby=='hits'} disabled {/if} />
          <input id="orderby" type="hidden" name="orderby" value="{$orderby}"/>
        </form></td>
      {else}
      <td>&nbsp;</td>
      {/if}
      </tr>
  </table>
</div>

<form name="listform" id="listform" action="" method="post">
  <table width="100%" cellspacing="0" cellpadding="5" style="border:solid 1px gray">
    <tr>
      <td class="usr_files_head" width="20" align="center">#</td>
      <td class="usr_files_head" width="" colspan="2">{$LANG.FILE_NAME} {if $orderby=='filename'} &darr; {/if}</td>
      {if $myprofile}
      <td class="usr_files_head" width="100" align="center">{$LANG.VISIBILITY}</td>
      {/if}
      <td class="usr_files_head" width="100">{$LANG.SIZE} {if $orderby=='filesize'}&darr;{/if}</td>
      <td class="usr_files_head" width="120">{$LANG.CREATE_DATE} {if $orderby=='pubdate'}&darr;{/if}</td>
      <td class="usr_files_head" width="80" align="center">{$LANG.DOWNLOAD_HITS} {if $orderby=='hits'}&darr;{/if}</td>
      {if $myprofile || $is_admin}
      <td class="usr_files_head" width="16">&nbsp;</td>
      {/if}
      </tr>

    {foreach key=tid item=file from=$files}
        <tr>
        {if $myprofile}
          <td class="{$file.class}" align="center" valign="top"><input id="fileid{$file.rownum}" type="checkbox" name="files[]" value="{$file.id}"/></td>
        {else}
          <td class="{$file.class}" align="center" valign="top">{$file.rownum}</td>
        {/if}
          <td class="{$file.class}" width="16" valign="top">{$file.fileicon}</td>
          <td class="{$file.class}" valign="top"><a href="{$file.filelink}">{$file.filename}</a>
            <div class="usr_files_link">{$file.filelink}</div></td>
          {if $myprofile}
          	{if $file.allow_who == 'all'}
          <td class="{$file.class}" align="center"><img src="/components/users/images/yes.gif" border="0" title="{$LANG.FILE_VIS_ALL}"/></td>
          	{else}
          <td class="{$file.class}" align="center"><img src="/components/users/images/no.gif" border="0" title="{$LANG.FILE_HIDEN}"/></td>
            {/if}
          {/if}

          <td class="{$file.class}">{$file.mb} {$LANG.MBITE}</td>
          <td class="{$file.class}">{$file.pubdate}</td>
          <td class="{$file.class}" align="center">{$file.hits}</td>
          {if $myprofile || $is_admin}
          <td class="{$file.class}" align="center"><a href="/users/{$usr.id}/delfile{$file.id}.html"><img src="/components/users/images/delete.gif" border="0" alt="{$LANG.DELETE_FILE}"/></a></td>
          {/if}
          </tr>
    {/foreach}
  </table>
  {if $myprofile}
  <div style="margin-top:6px">
    <input type="button" class="usr_files_orderbtn" name="delete_btn" id="delete_btn" onclick="delFiles()" value="{$LANG.DELETE}"/>
    <input type="button" class="usr_files_orderbtn" name="hide_btn" id="delete_btn" onclick="pubFiles(0)" value="{$LANG.HIDE}"/>
    <input type="button" class="usr_files_orderbtn" name="show_btn" id="delete_btn" onclick="pubFiles(1)" value="{$LANG.SHOW}"/>
  </div>
  {/if}
</form>
{else}
	<p>{$LANG.USER_NO_UPLOAD}</p>
		{if $myprofile}
			<a href="addfile.html">{$LANG.UPLOAD_FILE_IN_ARCHIVE}</a>
		{/if}
{/if}
{/strip}