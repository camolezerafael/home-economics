<div class="col-lg-4 col-md-6 mt-4 mb-4">
	<div class="card z-index-2 ">
		<div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
			<div class="{{$color}} border-radius-lg py-3 pe-1">
				<div class="chart">
					<canvas id="{{$name}}" class="chart-canvas" height="170"></canvas>
				</div>
			</div>
		</div>
		<div class="card-body">
			<h6 class="mb-0 ">{{$legend}}</h6>

			@if(!empty($sublegend))
				{{$sublegend}}
			@endif

			{{$slot}}

			@if(!empty($footer))
				<hr class="dark horizontal">
				<div class="d-flex">
					{{$footer}}
				</div>
			@endif
		</div>
	</div>
</div>

<script>
	const {{Str::of($name)->camel()}} = document.getElementById('{{$name}}').getContext("2d");

	new Chart({{Str::of($name)->camel()}}, {
		type: "{!! $type !!}",
		data: {
			labels: ['{!!  implode("','",$labels) !!}'],
			datasets: [{
				label: "{{$dataLabel}}",
				tension: 0,
				pointRadius: 5,
				pointBackgroundColor: "rgba(255, 255, 255, .8)",
				pointBorderColor: "transparent",
				borderColor: "rgba(255, 255, 255, .8)",
				borderWidth: 4,
				borderRadius: 4,
				borderSkipped: false,
				backgroundColor: "transparent",
				fill: true,
				data: [{{implode(',',$data)}}],
				maxBarThickness: 6
			}, ],
		},
		options: {
			responsive: true,
			maintainAspectRatio: false,
			plugins: {
				legend: {
					display: false,
				}
			},
			interaction: {
				intersect: false,
				mode: 'index',
			},
			scales: {
				y: {
					grid: {
						drawBorder: false,
						display: true,
						drawOnChartArea: true,
						drawTicks: false,
						borderDash: [5, 5],
						color: 'rgba(255, 255, 255, .2)'
					},
					ticks: {
						display: true,
						suggestedMin: 0,
						suggestedMax: 500,
						beginAtZero: true,
						padding: 10,
						font: {
							size: 14,
							weight: 300,
							family: "Roboto",
							style: 'normal',
							lineHeight: 2
						},
						color: "#fff"
					},
				},
				x: {
					grid: {
						drawBorder: false,
						display: true,
						drawOnChartArea: true,
						drawTicks: false,
						borderDash: [5, 5],
						color: 'rgba(255, 255, 255, .2)'
					},
					ticks: {
						display: true,
						color: '#f8f9fa',
						padding: 10,
						font: {
							size: 14,
							weight: 300,
							family: "Roboto",
							style: 'normal',
							lineHeight: 2
						},
					}
				},
			},
		},
	});
</script>
