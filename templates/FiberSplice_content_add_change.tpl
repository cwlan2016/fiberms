<form name="fibersplice" action="FiberSplice.php" method="post">
<input type="hidden" value="{$clid1}" name="clid1">
<input type="hidden" value="{$IsA}" name="IsA">
<input type="hidden" value="{$SpliceId}" name="SpliceId">
<input type="hidden" value="{$mod}" name="mode">
<input type="hidden" value="{$NetworkNodeId}" name="NetworkNodeId">
<input type="hidden" value="{$curr_fiber}" name="curr_fiber">
<input type="hidden" value="{$back}" name="back">
	<table id="contable">
		<tr>
		<td><label class="events_anonce">Кабель 1</label></td><td> <input type="text" value="{$cable1}" name="cable" readonly></td>
		</tr>
		<tr>
		<td><label class="events_anonce">Волокно 1</label></td><td> <input type="text" value="{$fiber1}" name="fiber" readonly></td>
		</tr>
		<tr>
		<td><label class="events_anonce">Кабель 2</label></td><td> <select name="CableLines" onChange="javascript: GetFiber(document.fibersplice.CableLines.value,document.fibersplice.NetworkNodeId.value,document.fibersplice.curr_fiber.value,3);"> {html_options values=$ComboBox_CableLines_values selected=$ComboBox_CableLines_selected output=$ComboBox_CableLines_text}</select></td>
		</tr>
		<tr>
		<td><label class="events_anonce">Волокно 2</label></td><td>{include file="FiberSplice_content_Fibers.tpl"}</td>
		</tr>
		<tr>
		<td><label class="events_anonce">Кассета <a href="FSO.php?mode=add&parent_back={$back|escape:'url'}">+</a></label></td><td> <select name="FibersSpliceOrganizer"> {html_options values=$ComboBox_FibersSpliceOrganizer_values selected=$Combobox_FibersSpliceOrganizer_selected output=$ComboBox_FibersSpliceOrganizer_text}</select></input></td>
		</tr>
		<tr>
		<td><label class="events_anonce">Затухание</label></td><td> <input type="text" value="{$attenuation}" name="attenuation"></input></td>
		</tr>
		<tr>
		<td><label class="events_anonce">Описание</label></td><td> <input type="textarea" value="{$note}" name="note"></input></td>
		</tr>
		<tr>
		<th colspan="2"><input value="OK" type="submit" name="ChangeButton" /></th>
		</tr>
	</table>
</form>