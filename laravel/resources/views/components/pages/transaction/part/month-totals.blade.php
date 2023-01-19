@props(['color', 'title', 'line1', 'line2', 'percent', 'paid', 'estimated'])

<div class="card card-body bg-gradient-faded-light border-{{$color}} border-2">
	<div>{{$title}}</div>
	<table class="table table-borderless m-0 p-0">
		<tbody>
		<tr>
			<td class="align-bottom pt-3 pb-2 px-0" colspan="2">
				<div class="progress bg-gray-500 w-100" style="height: 1em;">
					<div class="progress-bar bg-gradient-{{$color}}" role="progressbar"
						 aria-valuenow="{{$percent}}" aria-valuemin="0" aria-valuemax="100"
						 style="width: {{$percent}}%; height: 1em;">{{$percent}}%
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td class="align-bottom text-xs text-bold p-0">{{$line1}}</td>
			<td class="align-bottom text-sm text-bold text-end p-0">@formatMoney($paid)</td>
		</tr>
		<tr>
			<td class="align-bottom text-xs text-bold p-0 opacity-8">{{$line2}}</td>
			<td class="align-bottom text-sm text-bold text-end p-0 opacity-8">
				@formatMoney($estimated)
			</td>
		</tr>
		</tbody>
	</table>
</div>
