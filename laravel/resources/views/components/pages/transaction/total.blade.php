<hr class="dark horizontal my-0">

<div class="card-footer d-flex">
	<div class="col-md-5"></div>
	<div class="col-md-2">
		<table class="table table-striped">
			<tbody>
			<tr>
				<td class="text-bold text-sm text-end align-middle">Total</td>
				<td class="text-bold">@formatMoney($paid + $unpaid)</td>
			</tr>
			<tr>
				<td class="text-bold text-xs text-end" style="color: #adb5bd;">Paid</td>
				<td class="text-bold text-xs" style="color: #adb5bd;">@formatMoney($paid)</td>
			</tr>
			<tr>
				<td class="text-bold text-xs text-end" style="color: #adb5bd;">{{ $labelTotal }}</td>
				<td class="text-bold text-xs" style="color: #adb5bd;">@formatMoney($unpaid)</td>
			</tr>
			</tbody>
		</table>
	</div>
</div>
