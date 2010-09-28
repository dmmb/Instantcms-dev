{* ================================================================================ *}
{* =========================== Запрос подтверждения действия ====================== *}
{* ================================================================================ *}

<div class="con_heading">{$confirm.title}</div>
<p>{$confirm.text}</p>
<div>
	<form action="{$confirm.action|default:''}" method="{$confirm.method|default:'POST'}">
            {$confirm.other}
			<input style="font-size:24px; width:100px" 				   
				   type="{$confirm.yes_button.type|default:'submit'}" 
				   name="{$confirm.yes_button.name|default:'go'}" 
				   value="{$confirm.yes_button.title|default:'Да'}"
				   onclick="{$confirm.yes_button.onclick|default:'true'}"	
			/> 
			<input style="font-size:24px; width:100px" 				   
				   type="{$confirm.no_button.type|default:'button'}" 
				   name="{$confirm.no_button.name|default:'cancel'}" 
				   value="{$confirm.no_button.title|default:'Нет'}"
				   onclick="{$confirm.no_button.onclick|default:'window.history.go(-1)'}"	
			/> 
	</form>
</div>