
<page>
	<div id="divPageBox">
		<div class="divPagination">
			Page [[page_cu]] of [[page_nb]]
		</div>
		
		<div class="divTitleText" style="width:115px; float: left;">
			Accountant Details
		</div>


			<table class="tableBlock" cellspacing="0" cellpadding="0" style="margin-top:-2px; font-size: 12px; margin-bottom:0px; ">
				<col width="312"><col width="312">
				<tbody>
					<tr>
						<td><strong>Period Know From:</strong><br />{{\Carbon\Carbon::parse($BPSSApplication->se_accountant_known_from)->format('d/m/Y')}}</td>
						<td><strong>Period Known To:</strong><br />{{\Carbon\Carbon::parse($BPSSApplication->se_accountant_known_to)->format('d/m/Y')}}</td>
					</tr>
					<tr>
						<td colspan="2"><strong>Name:</strong> {{$BPSSApplication->se_accountant_name}}</td>
					</tr>
					<tr>
						<td colspan="2"><strong>Address:</strong> {{$BPSSApplication->se_accountant_address}}</td>
					</tr>
					<tr>
						<td><strong>Town:</strong> {{$BPSSApplication->se_accountant_town}}</td>
						<td><strong>Postcode:</strong> {{$BPSSApplication->se_accountant_postcode}}</td>
					</tr>
					<tr>
						<td><strong>Email:</strong> {{$BPSSApplication->se_accountant_email}}</td>
						<td><strong>Contact Number:</strong> {{$BPSSApplication->se_accountant_contact_number}}</td>
					</tr>
				</tbody>
			</table>

	</div>
</page>
