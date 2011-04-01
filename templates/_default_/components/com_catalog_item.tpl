{* ========================================================================= *}
{* ==================== ����� ������ �������������� �������� =============== *}
{* ========================================================================= *}

{* =========================== ������ �� ������� =========================== *}
{if $cat.view_type=='shop'}
	<div id="shop_toollink_div">
		{$shopCartLink}
    </div>
{/if}
			
{* =============================== ��������� =============================== *}
<div class="con_heading">{$item.title}</div>

<table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:10px"><tr>
	<td align="left" valign="top" width="10" class="uc_detailimg">

        <div>
		{if strlen($item.imageurl)>4}
                <a class="lightbox-enabled" rel="lightbox" href="/images/catalog/{$item.imageurl}" target="_blank">
                    <img alt="{$item.title}" src="/images/catalog/medium/{$item.imageurl}.jpg" border="0" />
                </a>
        {else}
                <img src="/images/catalog/medium/nopic.jpg" border="0" />
        {/if}
        </div>
		
		
    </td>
	
{* ========================== �������������� ������ ========================= *}
    <td class="uc_list_itemdesc" align="left" valign="top" class="uc_detaildesc">

        <ul class="uc_detaillist">
        	<li class="uc_detailfield"><strong>{$LANG.ADDED_BY}: </strong> {$getProfileLink}</li>
			{foreach key=field item=value from=$fields}
                {if $value}
                    {if strstr($field, '/~l~/')}
                        <li class="uc_detailfield">{$value}</li>
                    {else}
                        <li class="uc_detailfield"><strong>{$field}: </strong>{$value}</li>
                    {/if}
                {/if}
			{/foreach}
		</ul>
							
{* ====================================== ����  ============================== *}
							
        {if $cat.view_type=='shop'}
			<div id="shop_price">
                <span>{$LANG.PRICE}:</span> {$item.price} {$LANG.RUB}
            </div>
        
			{* ======================== ������ �������� � �������  ======================= *}
		
			<div id="shop_ac_itemdiv">
                <a href="/catalog/addcart{$item.id}.html" title="{$LANG.ADD_TO_CART}" id="shop_ac_item_link">
					<img src="/components/catalog/images/shop/addcart.jpg" border="0" alt="{$LANG.ADD_TO_CART}"/>
                </a>
            </div>
        {/if}

{* ======================= ������ ������� ��������� ========================= *}
		
        {if $item.on_moderate}
		
                <div id="shop_moder_form">
                    <p class="notice">{$LANG.WAIT_MODERATION}:</p>
                    <table cellpadding="0" cellspacing="0" border="0"><tr>
                    <td>
                            <form action="/catalog/moderation/accept{$item.id}.html" method="POST">
                                <input type="submit" name="accept" value="{$LANG.MODERATION_ACCEPT}"/>
                            </form>
                          </td>
                    <td>
                            <form action="/admin/index.php" target="_blank" method="GET">
                                <input type="hidden" name="view" value="components" />
                                <input type="hidden" name="do" value="config" />
                                <input type="hidden" name="link" value="catalog" />
                                <input type="hidden" name="opt" value="edit_item" />
                                <input type="hidden" name="item_id" value="{$item.id}" />
                                <input type="submit" name="accept" value="{$LANG.EDIT}"/>
                            </form>
                          </td>
                    <td>
                            <form action="/catalog/moderation/reject{$item.id}.html" method="POST">
                                 <input type="submit" name="accept" value="{$LANG.MODERATION_REJECT}"/>
                            </form>
                          </td>
                    </tr></table>
					
                </div>
        {/if}
		
	</td>
</tr></table>


{* =================================== ���� ============================= *}

{if ($cat.showtags) && ($tagline)}
    <div class="uc_detailtags"><strong>{$LANG.TAGS}: </strong>{$tagline}</div>
{/if}
			
{* ================================== ������� ============================ *}

{if $cat.is_ratings}
	{$ratingForm}
{/if}

