{* ================================================================================ *}
{* ========================= Просмотр рубрики каталога ============================ *}
{* ================================================================================ *}

<h1 class="con_heading">{$cat.title}</h1>
{if $cat.description}
	<div class="con_description">{$cat.description}</div>
{/if}

{if $subcats}
	<div class="uc_subcats">{$subcats}</div>
{/if}

{if $alphabet} {$alphabet} {/if}

<div id="shop_toollink_div">
	<a id="shop_searchlink" href="/catalog/{$menuid}/{$cat.id}/search.html">Поиск по рубрике</a>
	{if $cat.view_type=='shop'} {$shopcartlink}	{/if}
</div>

{if $cat.showsort} {$orderform} {/if}

{if $itemscount>0}

	{if $page>1} <p>Страница {$page}</p> {/if}
	
	{if $search_details} {$search_details} {/if}
	
		{foreach key=tid item=item from=$items}
				
			{if $cat.view_type=='list' || $cat.view_type=='shop'}
				<div class="catalog_list_item">
					<table border="0" cellspacing="2" cellpadding="0" id="catalog_item_table"><tr>
						<td valign="top" align="center" id="catalog_list_itempic" width="110">							
								{if $item.imageurl}
									<a class="lightbox-enabled" rel="lightbox" href="/images/catalog/{$item.imageurl}">
										<img alt="{$item.title}" src="/images/catalog/small/{$item.imageurl}.jpg" border="0" />
									</a>
								{else}
									<a href="/catalog/{$menuid}/item{$item.id}.html">
										<img alt="{$item.title}" src="/images/catalog/small/nopic.jpg" border="0" />								
									</a>										
								{/if}
							{if $cat.view_type=='shop'}
								<div id="shop_small_price">
									<span>{$item.price}</span> руб.
								</div>
							{/if}
						</td>
						<td class="uc_list_itemdesc" align="left" valign="top">
							<div>
								<a class="uc_itemlink" href="/catalog/{$menuid}/item{$item.id}.html">{$item.title}</a> 
								{if $item.is_new}
									<span class="uc_new"><img src="/images/ratings/new.gif" border="0"/></span>
								{/if}									
							</div>
							{if $cat.is_ratings}
								<div class="uc_rating">{$item.rating}</div>
							{/if}

							<div class="uc_itemfieldlist">
								{foreach key=field item=value from=$item.fields}
									{if !strstr($field, '/~l~/')}
										<div class="uc_itemfield"><strong>{$field}</strong>: {$value}
									{else}
										{$value}
									{/if}
								{/foreach}
							</div>
							{if $cat.showtags}
								<div class="uc_tagline"><strong>Тэги:</strong> {$item.tagline}</div>
							{/if}

							{if $cat.view_type=='list'}
								{if $cat.showmore}
									<a href="/catalog/{$menuid}/item{$item.id}.html">Подробнее...</a> 
								{/if}										
							{else}
								<div id="shop_list_buttons">
									<a href="/catalog/{$menuid}/item{$item.id}.html" title="Подробнее">
										<img src="/components/catalog/images/shop/more.jpg" border="0" alt="Подробнее"/>
									</a> 
									<a href="/catalog/{$menuid}/addcart{$item.id}.html" title="Добавить в корзину">
										<img src="/components/catalog/images/shop/addcart.jpg" border="0" alt="Добавить в корзину"/>
									</a>
								</div>
							{/if}
						</td>											
					</tr></table>
				</div>
			{/if}
					
			{if $cat.view_type=='thumb'}
				<div class="uc_thumb_item">
					<table border="0" cellspacing="2" cellpadding="0" width="100%">
						<tr><td height="110" align="center" valign="middle">
							<a href="/catalog/{$menuid}/item{$item.id}.html">
								{if $item.imageurl}
									<img alt="{$item.title}" src="/images/catalog/small/{$item.imageurl}.jpg" border="0" />
								{else}
									<img alt="{$item.title}" src="/images/catalog/small/nopic.jpg" border="0" />								
								{/if}
							</a>
						</td></tr>
						<tr><td align="center" valign="middle">
							<a class="uc_thumb_itemlink" href="/catalog/{$menuid}/item{$item.id}.html">{$item.title}</a>								
						</td></tr>						
					</table>
				</div>				
			{/if}															
		{/foreach}
		
		{$pagebar}
{/if}


