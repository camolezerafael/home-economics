@props(['f_date', 'f_acc', 'f_pay', 'comboAccounts', 'comboPaid'])

<div class="me-3 my-3 text-start">
	<form method="GET" id="form-filters">

		<div class="d-inline-flex">
			<label for="f_date">{{__('Period')}}
				<div class="input-group input-group-dynamic">
					<button type="button" class="btn btn-secondary btn-sm" id="btn-date-dec">
						<i class="fas fa-chevron-left"></i>
					</button>

					<input class="form-control form-control-sm px-2 col-2" style="height: 2.7em"
						   name="f_date" id="f_date" type="month" min="2020-01"
						   value="{{ $f_date }}" aria-label="Date filter to show moviments"/>

					<button type="button" class="btn btn-secondary btn-sm" id="btn-date-inc">
						<i class="fas fa-chevron-right"></i>
					</button>
				</div>
			</label>
		</div>

		<x-pages.transaction.part.filter-combo :options="$comboAccounts" name="f_acc" :default="$f_acc" label="Selected Account"/>

		<x-pages.transaction.part.filter-combo :options="$comboPaid" name="f_pay" :default="$f_pay" label="Status"/>

	</form>
</div>


<script>
	document.getElementById('btn-date-dec').addEventListener(
		"click",
		() => {
			changeDateFilter(calcDateChange('DEC'))
		},
	);

	document.getElementById('btn-date-inc').addEventListener(
		"click",
		() => {
			changeDateFilter(calcDateChange('INC'))
		},
	);

	document.getElementById('f_date').addEventListener(
		"change",
		() => {
			document.getElementById('form-filters').submit()
		},
	);

	document.getElementById('f_acc').addEventListener(
		"change",
		() => {
			document.getElementById('form-filters').submit()
		},
	);

	document.getElementById('f_pay').addEventListener(
		"change",
		() => {
			document.getElementById('form-filters').submit()
		},
	);

	function calcDateChange(type) {
		const dt = document.getElementById('f_date').value.split('-')

		let month = parseInt(dt[1])
		let year = parseInt(dt[0])

		if (type === 'INC') {
			month += 1
		} else if (type === 'DEC') {
			month -= 1
		}

		if (month === 13) {
			month = 1
			year += 1
		} else if (month === 0) {
			month = 12
			year -= 1
		}

		return year.toString() + '-' + month.toString().padStart(2, '0')
	}

	function changeDateFilter(date) {
		document.getElementById('f_date').value = date
		document.getElementById('f_date').dispatchEvent(new Event("change"))
	}
</script>
