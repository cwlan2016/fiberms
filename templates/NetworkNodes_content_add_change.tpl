<form name="NetworkNode" id="NetworkNode" action="NetworkNodes.php" method="post">
<div>
<input type="hidden" value="{$mod}" name="mode" />
<input type="hidden" value="{$back}" name="back" />
<input name="id" type="hidden" value="{$id}"/>
	<table id="contable">
		<tr>
			<td><label class="events_anonce">Имя</label></td><td><input name="name" type="text" value="{$name}"/></td>
		</tr>
		<tr>
			<td><label class="events_anonce">Ящик</label></td><td> <select name="boxes">
			{html_options values=$combobox_box_values selected=$combobox_box_selected output=$combobox_box_text}
			</select>
			</td>
		</tr>		
		<tr>
			<td><label class="events_anonce">OpenGIS</label></td><td><input name="OpenGIS" type="text" value="{$OpenGIS}" /></td>
		</tr>
		<tr>
			<td><label class="events_anonce">GeoSpartical</label></td><td><input name="SettlementGeoSpatial" type="text" value="{$SettlementGeoSpatial}" /></td>
		</tr>
		<tr>
			<td><label class="events_anonce">Здание</label></td><td><input name="Building" type="text" value="{$Building}" /></td>
		</tr>
		<tr>
			<td><label class="events_anonce">Квартира</label></td><td><input name="Apartment" type="text" value="{$Apartment}" /></td>
		</tr>
		<tr>
			<td><label class="events_anonce">Примечание</label></td><td><textarea name="note" form="NetworkNode">{$note}</textarea></td>
		</tr>
		<tr>
			<th colspan="2">
			<input value="OK" type="submit" name="OkButton" /><br />
			</th>
		</tr>	</table>
</form>