<h1 class="con_heading">{$pagetitle}</h1>
<form action="{$action}" method="post" enctype="multipart/form-data">
	<table cellpadding="5">
		<tr>
            <td height="30"><span>{$LANG.CAT_BOARD}:</span></td>
            <td>
                <select name="category_id" id="category_id" class="text-input" style="width:407px" onchange="getRubric();">
                    <option value="0">-- {$LANG.SELECT_CAT} --</option>
                    {$catslist}
                </select>
            </td>
		</tr>
		<tr>
			<td width="180">
				<span>{$LANG.TITLE}:</span>
			</td>
			<td height="35">
				<select name="obtype" id="obtype" style="width:120px">
					<option value="0">-- {$LANG.SELECT_CAT} --</option>
				</select>
				<input name="title" type="text" id="title" class="text-input" style="width:280px" maxlength="250"  value="{$item.title|escape:'html'}"/>
			</td>
		</tr>
		<tr id="from_search">
			<td></td>
			<td height="35">
				<input name="title_fake" type="text" id="title_fake" maxlength="250"  value=""/>
			</td>
		</tr>
		<tr class="proptable">
			<td>
				<span>{$LANG.CITY}:</span>
			</td>
			<td height="35" valign="top">
				<input name="city_ed" class="text-input" type="text" id="city_ed" style="width:184px" value="{$item.city|escape:'html'}"/> {$LANG.OR_SELECTING} {$cities}
			</td>
		</tr>
		<tr id="before_form">
			<td valign="top">
				<span>{$LANG.TEXT_ADV}:</span>
			</td>
			<td height="100" valign="top">
				<textarea name="content" class="text-input" style="width:403px" rows="5" id="content">{$item.content|escape:'html'}</textarea>
			</td>
		</tr>
        {if $formsdata}
        	{foreach key=tid item=form from=$formsdata}
            <tr class="cat_form">
                <td valign="top">
                    <span>{$form.title}:</span>
                </td>
                <td valign="top">
                    {$form.value}
                </td>
            </tr>
            {/foreach}
        {/if}
		{if $cfg.photos && $cat.is_photos}
			<tr>
				<td><span>{$LANG.PHOTO}:</span></td>
				<td><input name="Filedata" type="file" id="picture" style="width:407px;" /></td>
			</tr>
		{/if}
		{if $form_do == 'edit'}
			<tr>
				<td height="35"><span>{$LANG.PERIOD_PUBL}:</span></td>
				<td height="35">{$item.pubdays} {$LANG.DAYS}, {$LANG.DAYS_TO} {$item.pubdate}.</td>
			</tr>
		{elseif $cfg.srok}
			<tr>
				<td><span>{$LANG.PERIOD_PUBL}:</span></td>
				<td>
					<select name="pubdays" id="pubdays">
						<option value="5">5</option>
						<option value="10" selected="selected">10</option>
						<option value="14">14</option>
						<option value="30">30</option>
						<option value="50">50</option>
					</select> {$LANG.DAYS}
				</td>
			</tr>
		{/if}
        {if $cfg.extend && $form_do == 'edit' && !$item.published && $item.is_overdue}
        	{if $cfg.srok}
                <tr>
                    <td height="35"><span>{$LANG.ADV_EXTEND}:</span></td>
                    <td height="35">
                        <select name="pubdays" id="pubdays">
                            <option value="5">5</option>
                            <option value="10" selected="selected">10</option>
                            <option value="14">14</option>
                            <option value="30">30</option>
                            <option value="50">50</option>
                        </select>  {$LANG.DAYS}</td>
                </tr>
            {else}
                <tr>
                    <td height="35"><span>{$LANG.ADV_EXTEND}:</span></td>
                    <td height="35">{$LANG.ADV_EXTEND_SROK} {$item.pubdays} {$LANG.DAYS}</td>
                </tr>
            {/if}
        {/if}

        {if $form_do == 'edit' && $item.is_vip}
			<tr>
				<td height="35"><span>{$LANG.VIP_STATUS}:</span></td>
				<td height="35">до {$item.vipdate}</td>
			</tr>
        {/if}

		{if $is_admin || ($is_billing && $cfg.vip_enabled && ($form_do=='add' || ($form_do=='edit' && $cfg.vip_prolong)))}
			<tr>
				<td>
                    <span>{$LANG.MARK_AS_VIP}:</span>
                    <div style="color:gray">
                        VIP-объявления выделяются цветом и всегда находятся в начале списка
                    </div>
                </td>
				<td valign="top" style="padding-top:5px">
                    <select id="vipdays" name="vipdays" {if !$is_admin}onchange="calculateVip()"{/if}>
                        {section name=vipdays start=0 loop=$cfg.vip_max_days+1 step=1}
                            <option value="{$smarty.section.vipdays.index}">
                                {$smarty.section.vipdays.index}
                            </option>
                        {/section}
                    </select>
                    {$LANG.DAYS}

                    {if !$is_admin}
                        <input type="hidden" id="vip_day_cost" name="vip_day_cost" value="{$cfg.vip_day_cost}" />
                        <input type="hidden" id="balance" name="balance" value="{$balance}" />
                        <div id="vip_cost" style="margin-top:10px;display: none">
                            Стоимость: <span>0</span> баллов
                        </div>

                        <script type="text/javascript">
                            {literal}
                                function calculateVip(){

                                    var days = $('#vipdays').val();
                                    var cost = $('#vip_day_cost').val();

                                    if (Number(days)==0){
                                        $('#vip_cost').hide().find('span').html('0');
                                    } else {
                                        var summ = days * cost;
                                        $('#vip_cost').show().find('span').html(summ);
                                    }

                                }

                                function checkBalance(){
                                    var cost    = Number($('#vip_cost span').html());
                                    var balance = Number($('#balance').val());

                                    if (balance < cost){
                                        alert('На вашем балансе не достаточно средств\nдля покупки VIP-статуса на указанный срок');
                                        return false;
                                    } else {
                                        return true;
                                    }
                                }
                            {/literal}
                        </script>
                    {/if}

				</td>
			</tr>
		{/if}
        {if !$is_user}
        <tr>
            <td valign="top" class="">
                <div><strong>{$LANG.SECUR_SPAM}: </strong></div>
                <div><small>{$LANG.SECUR_SPAM_TEXT}</small></div>
            </td>
            <td valign="top" class="">
                {php}echo cmsPage::getCaptcha();{/php}
            </td>
        </tr>
        {/if}
		<tr>
			<td height="40" colspan="2" valign="middle">
				<input name="submit" type="submit" id="submit" style="margin-top:10px;font-size:18px" value="{$LANG.SAVE_ADV}" {if $is_admin || ($is_billing && $cfg.vip_enabled)}onclick="if(!checkBalance())return false;"{/if} />
			</td>
		</tr>
	</table>
</form>
{literal}
<script type="text/javascript">
	function getRubric(){
		$("#category_id").attr("disabled", "");
		$("#obtype").attr("disabled", "disabled");
		var category_id = $('select[name=category_id]').val();
		if(category_id != 0){
			$.post("/components/board/ajax/get_rubric.php", {value: category_id, obtype: '{/literal}{$item.obtype}{literal}'}, function(data) {
				$("#obtype").attr("disabled", "");
				$("#obtype").html(data);
			});
			{/literal}
			{if $form_do == 'add' && !$cat.id}
			{literal}
			$.post("/components/board/ajax/get_form.php", {value: category_id}, function(dataform) {
				if(dataform!=1){
					$('.cat_form').remove();
					$("#before_form").after(dataform);
				}else{
					$('.cat_form').remove();
				}
			});
			{/literal}
			{/if}
			{literal}
		} else {
			$("#obtype").html('<option value="0">-- {/literal}{$LANG.SELECT_CAT}{literal} --</option>');
			$("#obtype").attr("disabled", "disabled");
			$('.cat_form').remove();
		}
	}
	$(document).ready(function() {
		$('#title').focus();
		$('#from_search').hide();
		getRubric();
	});
</script>
{/literal}