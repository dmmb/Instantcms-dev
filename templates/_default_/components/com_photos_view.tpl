{* ================================================================================ *}
{* ========================= Просмотр раздела с фотографиями ====================== *}
{* ================================================================================ *}
{strip}
{* =============================================================================== *}
{* ======================= ссылки на лучшие и последние фото ===================== *}
{* =============================================================================== *}

	{if $id == $root.id && $cfg.showlat}
		<div class="photo_toolbar">
			<table border="0" cellspacing="0" cellpadding="0">
			  <tr>
				<td width="23"><img src="/templates/_default_/images/icons/calendar.png" /></td>
				<td style="padding-right: 10px"><a href="/photos/latest.html">{$LANG.LAST_UPLOADED}</a></td>
				<td width="23"><img src="/templates/_default_/images/icons/rating.png" /></td>
				<td><a href="/photos/top.html">{$LANG.BEST_PHOTOS}</a></td>
			  </tr>
			</table>
		</div>
	{/if}

<h1 class="con_heading">{$pagetitle} {if $total_foto}({$total_foto}){/if}</h1>

<div class="clear"></div>

{* =============================================================================== *}
{* ======================= список категорий ====================================== *}
{* =============================================================================== *}
	{if $is_subcats}
    	{if ($cfg.tumb_view == '2' ||  $cfg.tumb_view == '3') && (!$album.NSDiffer || !$cfg.tumb_club)}
        {assign var="col" value="1"}
        	{foreach key=tid item=cat from=$subcats}
            {if $col==1}<div class="photo_row">{/if}
            	<div class="photo_album_tumb">
                	<div class="photo_container">
                    	{if $cat.iconurl}
                    	<a href="/photos/{$cat.id}"><img class="photo_album_img" src="/images/photos/small/{$cat.iconurl}" alt="{$cat.title}" border="0" /></a>
                        {else}
                        <a href="/photos/{$cat.id}"><img class="photo_album_img" src="/images/photos/no_image.png" alt="{$cat.title}" border="0" width="{$cat.thumb1}px" /></a>
                        {/if}
                    </div>
                    <div class="photo_txt">
                    	<ul>
                        	<li class="photo_album_title"><a href="/photos/{$cat.id}">{$cat.title}</a> ({$cat.content_count}{if $cat.subtext} {$cat.subtext}{/if})</li>
                            {if $cat.description}<li>{$cat.description}</li>{/if}
                        </ul>
                    </div>
                    {if $cat.today_count}<div class="photo_container_today">+{$cat.today_count}</div>{/if}
                </div>
             
             {if $col==$cfg.maxcols}<div class="blog_desc"></div></div> {assign var="col" value="1"} {else} {math equation="z + 1" z=$col assign="col"}  {/if}
             
            {/foreach}
            {if $col>1} 
                <div class="blog_desc"></div></div>
            {/if}
        {else}
            {assign var="col" value="1"}
            <table class="categorylist" style="margin-bottom:10px" cellspacing="3" width="100%" border="0">
            {foreach key=tid item=cat from=$subcats}
                {if $col==1} <tr> {/if}
                    <td width="20" valign="top" style="padding-top:5px">
                        <img src="/templates/_default_/images/icons/folder.png" border="0" />
                    </td>
                    <td width="" valign="top">
                       <div><a href="/photos/{$cat.id}" class="photo_subcat">{$cat.title}</a> ({$cat.content_count}{$cat.subtext})</div>
                        {if $cat.description} <div>{$cat.description}</div>{/if}
                    </td>
                    {if $col==$maxcols} </tr> {assign var="col" value="1"} {else} {math equation="x + 1" x=$col assign="col"} {/if}
            {/foreach}
            {if $col>1} 
                <td colspan="{math equation="x - y + 1" x=$col y=$maxcols}">&nbsp;</td></tr>
            {/if}
            </table>
    	{/if}
    {/if}
{* =============================================================================== *}
{* ======================= список фотографий ===================================== *}
{* =============================================================================== *}
{if $can_add_photo}
	<a class="photo_add_link" href="/photos/{$album.id}/addphoto.html">{$LANG.ADD_PHOTO_TO_ALBUM}</a>
{/if}
{if $messages}
    <div class="sess_messages">
        {foreach key=id item=message from=$messages}
            {$message}
        {/foreach}
    </div>
{/if}
		
{if $cons}
		{if $album.showtype == 'list'}
			{assign var="col" value="1"}
                <table width="100%" cellpadding="5" cellspacing="0" border="0">
					{foreach key=tid item=con from=$cons}
						{if $col==1} <tr> {/if}
							<td width="20" valign="top"><img src="/images/markers/photo.png" border="0" /></td>
							<td width="" valign="top">
									<a href="/photos/photo{$con.id}.html">{$con.title}</a>
							</td>	
							 {if $album.showdate}
								{assign var="fcols" value="6"}
								<td width="16" valign="top"><img src="/images/icons/comments.gif" alt="{$LANG.COMMENTS}" border="0"/></td>
								<td width="25" valign="top"><a href="/photos/photo{$con.id}.html#c" title="{$LANG.COMMENTS}">{$con.commentscount}</a></td>
								<td width="16" valign="top" class="photo_date_td"><img src="/images/icons/date.gif" alt="{$LANG.PUB_DATE}" /></td>
								<td width="70" align="center" valign="top" class="photo_date_td">{$con.fpubdate}</td>			
							 {else} 
								{assign var="fcols" value="2"}
							 {/if}
                             
               			{if $col==$maxcols_foto} </tr> {assign var="col" value="1"} {else} {math equation="x + 1" x=$col assign="col"} {/if}
                        {/foreach}
                        {if $col>1} 
                            <td colspan="{math equation="((x - y + 1) * z)" x=$col y=$maxcols_foto z=$fcols}">&nbsp;</td></tr>
                        {/if}
				</table>
       	{/if}
		{if $album.showtype != 'list'}
        	{assign var="col" value="1"}
			<div class="photo_gallery">
				<table cellpadding="0" cellspacing="0" border="0">
					{foreach key=tid item=con from=$cons}
						{if $col==1} <tr> {/if}
                        <td align="center" valign="middle">
							<div class="{$album.cssprefix}photo_thumb">
							<table width="100%" height="100" cellspacing="0" cellpadding="4">
							  	<tr>
							  		<td valign="middle" align="center" >
										<a class="lightbox-enabled" rel="lightbox-galery" href="{$con.photolink}" title="{$con.title}">
											<img class="photo_thumb_img" src="/images/photos/small/{$con.file}" alt="{$con.title}" border="0" />
										</a>
                                	</td>
                                </tr>
								<tr>
                                	<td align="center"><a href="{$con.photolink2}" title="{$con.title}">{$con.title}</a></td>
                                </tr>
						{if $con.published == 0}
								<tr id="moder{$con.id}">
                                	<td align="center">
										<div style="margin-top:4px">{$LANG.WAIT_MODERING}</div>
										<div><a href="javascript:publishPhoto({$con.id})" style="color:green">{$LANG.PUBLISH}</a> | <a href="/photos/delphoto{$con.id}.html" style="color:red">{$LANG.DELETE}</a></div>
									</td>
                                </tr>						
						{/if}
							</table>
						</div>
						
						</td>
                    {if $col==$maxcols_foto} </tr> {assign var="col" value="1"} {else} {math equation="x + 1" x=$col assign="col"} {/if}
                    {/foreach}
                    {if $col>1} 
                        <td colspan="{math equation="x - y + 1" x=$col y=$maxcols_foto}">&nbsp;</td></tr>
                    {/if}
					</table>
				</div>
			{/if}
{else}
{if $album.parent_id > 0}<p>{$LANG.NOT_PHOTOS_IN_ALBUM}</p>{/if}       
{/if}
                    
{$pagebar}

{/strip}