{* ================================================================================ *}
{* ========================= форма сортировки фотографий ========================== *}
{* ================================================================================ *}
{strip}
	<form action="" method="POST">
    	<div class="photo_sortform">
    		<table cellspacing="2" cellpadding="2" >
	 			<tr>
					<td>{$LANG.SORTING_PHOTOS}: </td>
					<td valign="top">
                    <select name="orderby" id="orderby">
                        <option value="title" {if $orderby=='title'} selected {/if}>{$LANG.ORDERBY_TITLE}</option>
                        <option value="pubdate" {if $orderby=='pubdate'} selected {/if}>{$LANG.ORDERBY_DATE}</option>
                        <option value="rating" {if $orderby=='rating'} selected {/if}>{$LANG.ORDERBY_RATING}</option>
                        <option value="hits" {if $orderby=='hits'} selected {/if}>{$LANG.ORDERBY_HITS}</option>
					</select>
                    <select name="orderto" id="orderto">
						<option value="desc" {if $orderto=='desc'} selected {/if}>{$LANG.ORDERBY_DESC}</option>
						<option value="asc" {if $orderto=='asc'} selected {/if}>{$LANG.ORDERBY_ASC}</option>
					</select>
					<input type="submit" value=">>" />
					</td>
				</tr>
			</table>
       </div>
    </form>
{/strip}