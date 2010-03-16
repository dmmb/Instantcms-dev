{* ================================================================================ *}
{* ===================== Добавление предприятия =================================== *}
{* ================================================================================ *}

<script type="text/javascript">
    {literal}
    function showPrices(){

        var url  = "/templates/vobuhove/fulldesc.html";

        rep_win = window.open(url,"Window1", "menubar=yes,width=600,toolbar=no,location=no,scrollbars=yes");

    }
    {/literal}
</script>


<div class="con_heading">{$LANG.ADD_ITEM}</div>

<div id="configtabs">

    <div id="form">
        <form method="post" action="/catalog/{$menuid}/{$cat_id}/submit.html" enctype="multipart/form-data">
        <table cellpadding="5" cellspacing="0" style="margin-bottom:10px">
            <tr>
                <td width="210">
                    <strong>{$LANG.TITLE}:</strong>
                </td>
                <td><input type="text" name="title" value="{$item.title}" style="width:250px"/></td>
            </tr>
            <tr>
                <td width="">
                    <strong>{$LANG.IMAGE}:</strong>
                </td>
                <td><input name="imgfile" type="file" id="imgfile" size="16" /></td>
            </tr>
            {if $cat.view_type=='shop'}
            <tr>
                <td width="">
                    <strong>{$LANG.PRICE}:</strong>
                </td>
                <td>
                    <input type="text" name="price" value="{$item.price}" style="width:250px"/>
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
                    <input type="text" name="tags" value="{$item.tags}" style="width:250px"/>
                </td>
            </tr>            
        </table>
        {foreach key=id item=field from=$fields}
            <table width="650" border="0" cellspacing="5">
                <tr>
                    {if $field.ftype=='link' || $field.ftype == 'text'}
                    <td width="214">
                        <strong>{$field.value}:</strong>
                        {if $field.ftype=='link'} <br/><span class="hint">{$LANG.TYPE_LINK}</span>{/if}
                        {if $field.makelink} <br/><span class="hint">{$LANG.COMMA_SEPARATE}</span>{/if}
                    </td>
                    <td>
                        <input style="width:250px" name="fdata[{$id}]" type="text" id="fdata" size="" {if $fdata}value="{php}echo strip_tags($fdata[$id]);{/php}"{/if}/>
                    </td>
                    {else}
                    <td colspan="2">
                        <div style="padding-bottom:8px;"><strong>{$field.value}:</strong></div>
                        {wysiwyg name='fdata[]' value=$fdata.$id height=200 width='98%' toolbar='Basic'}
                    </td>
                    {/if}
                </tr>
            </table>
        {/foreach}
        {if $cfg.premod && !$is_admin}
            <p style="margin-top:15px">
                {$LANG.ITEM_PREMOD_NOTICE}
            </p>
        {/if}
        <p style="margin-top:15px">
            <input type="submit" name="submit" value="{$LANG.SAVE}" style="font-size:18px" />
            <input type="button" name="back" value="{$LANG.CANCEL}"  style="font-size:18px" onClick="window.history.go(-1)" />
        </p>
        </form>
    </div>

</div>
