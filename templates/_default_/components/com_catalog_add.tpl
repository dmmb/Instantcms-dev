{* ================================================================================ *}
{* ===================== Добавление предприятия =================================== *}
{* ================================================================================ *}

<script type="text/javascript">
{if $is_admin}
{literal}
		$(document).ready(function() {
			$('#title').focus();
			
			$("#cat_id").change(function () {
		
				var cat_id = "";
				$("#cat_id option:selected").each(function () {
					cat_id = $(this).val();
				});
				if(cat_id != 0) {{/literal}
					$("#add_form").attr("action", '/catalog/'+cat_id+'/submit.html');{literal}
				} else {{/literal}
					$("#add_form").attr("action", "/catalog/0/submit.html");{literal}
				}
        
        })
        .change();
			
		});
{/literal}
{/if}
    {literal}
    function showPrices(){

        var url  = "/templates/vobuhove/fulldesc.html";

        rep_win = window.open(url,"Window1", "menubar=yes,width=600,toolbar=no,location=no,scrollbars=yes");

    }
    {/literal}
</script>


<div class="con_heading">
    {if $do=='add_item'}{$LANG.ADD_ITEM}{/if}
    {if $do=='edit_item'}{$LANG.EDIT_ITEM}{/if}
</div>

<div id="configtabs">

    <div id="form">
        <form id="add_form" method="post" action="/catalog/{$cat_id}/submit.html" enctype="multipart/form-data">
        <table cellpadding="5" cellspacing="0" style="margin-bottom:10px">
            <tr>
                <td width="210">
                    <strong>{$LANG.TITLE}:</strong>
                </td>
                <td><input type="text" name="title" id="title" value="{$item.title|escape:'html'}" style="width:250px"/></td>
            </tr>
            {if $is_admin}
            <tr>
                <td width="210">
                    <strong>{$LANG.CAT}:</strong>
                </td>
                <td><select  style="width:250px" name="cat_id" id="cat_id">{$cats}</select></td>
            </tr>
            {/if}
            <tr>
                <td width="">
                    <strong>{$LANG.IMAGE}:</strong>
                </td>
                <td>
                    {if $do=='edit_item' && $item.imageurl}
                        <div style="margin-bottom:4px;">
                            <a href="/images/catalog/{$item.imageurl}" target="_blank">{$item.imageurl}</a>
                        </div>
                    {/if}
                    <table border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td><input name="imgfile" type="file" id="imgfile" size="16" /></td>
                            {if $do=='edit_item' && $item.imageurl}
                                <td style="padding-left:15px">
                                    <label>
                                        <input type="checkbox" value="1" name="delete_img" /> 
                                        {$LANG.DELETE}
                                    </label>                                    
                                </td>
                            {/if}
                        </tr>
                    </table>
                </td>
            </tr>
            {if $cat.view_type=='shop'}
            <tr>
                <td width="">
                    <strong>{$LANG.PRICE}:</strong>
                </td>
                <td>
                    <input type="text" name="price" value="{$item.price|escape:'html'}" style="width:250px"/>
                </td>
            </tr>
            <tr>
                <td width="">
                    <strong>{$LANG.CAN_MANY}:</strong>
                </td>
                <td>
                    <input type="radio" name="canmany" value="1"> {$LANG.YES}
                    <input type="radio" name="canmany" value="0" checked="checked"> {$LANG.NO}
                </td>
            </tr>
            {/if}
            <tr>
                <td width="">
                    <strong>{$LANG.TAGS}:</strong><br/>
                    <span class="hint">{$LANG.KEYWORDS}</span>
                </td>
                <td>
                    <input type="text" name="tags" value="{$item.tags|escape:'html'}" style="width:250px"/>
                </td>
            </tr>            
        </table>
        {foreach key=id item=field from=$fields}
            <table width="100%" border="0" cellspacing="5">
                <tr>
                    {if $field.ftype=='link' || $field.ftype == 'text'}
                    <td width="214" valign="top">
                        <strong>{$field.title}:</strong>
                        {if $field.ftype=='link'} <br/><span class="hint">{$LANG.TYPE_LINK}</span>{/if}
                        {if $field.makelink} <br/><span class="hint">{$LANG.COMMA_SEPARATE}</span>{/if}
                    </td>
                    <td>
                        <input style="width:250px" name="fdata[{$id}]" type="text" id="fdata" size="" value="{if $field.value}{$field.value|escape:'html'}{/if}"/>
                    </td>
                    {else}
                        <td width="214" valign="top"><strong>{$field.title}:</strong></td>
                        <td>
                            {wysiwyg name="fdata[$id]" value=$field.value height=300 width='98%' toolbar='Basic'}
                        </td>
                    {/if}
                </tr>
            </table>
        {/foreach}
        {if $cfg.premod && !$is_admin}
            <p style="margin-top:15px;color:red">
                {$LANG.ITEM_PREMOD_NOTICE}
            </p>
        {/if}
        <p style="margin-top:15px">
            <input type="hidden" name="opt" value="{if $do=='add_item'}add{else}edit{/if}" />
            {if $do=='edit_item'}
                <input type="hidden" id="item_id" name="item_id" value="{$item.id}" />
            {/if}
            <input type="submit" name="submit" value="{$LANG.SAVE}" style="font-size:18px" />
            <input type="button" name="back" value="{$LANG.CANCEL}"  style="font-size:18px" onClick="window.history.go(-1)" />
        </p>
        </form>
    </div>

</div>
