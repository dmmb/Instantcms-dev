{* ================================================================================ *}
{* ==================== Форма сортировки на доске объявлений ====================== *}
{* ================================================================================ *}

<form action="{$action_url}" method="POST">
	<div class="photo_sortform">
		<table cellspacing="2" cellpadding="2" >
			<tr>
				<td >Тип: </td>
				<td >
					<select name="obtype" id="obtype">
						<option value="" {if (empty($btype))} selected {/if}>Все типы</option>
						{$btypes}
					</select>
				</td>
				<td >Город: </td>
				<td >
					{$bcities}
				</td>
				<td >Сортировать: </td>
				<td >
					<select name="orderby" id="orderby">
						<option value="title" {if $orderby=='title'} selected {/if}>По алфавиту</option>
						<option value="pubdate" {if $orderby=='pubdate'} selected {/if}>По дате</option>
						<option value="hits" {if $orderby=='hits'} selected {/if}>По просмотрам</option>
						<option value="obtype" {if $orderby=='obtype'} selected {/if}>По типу</option>
						<option value="user_id" {if $orderby=='user_id'} selected {/if}>По автору</option>
					</select>
					<select name="orderto" id="orderto">';
						<option value="desc" {if $orderto=='desc'} selected {/if}>по убыванию</option>
						<option value="asc" {if $orderto=='asc'} selected {/if}>по возрастанию</option>
					</select>
					<input type="submit" value="Фильтр" />
				</td>		
			</tr>
		</table>
	</div>
</form>