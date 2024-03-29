@props(['f_date', 'f_acc', 'f_pay', 'comboAccounts', 'comboPaid'])

<div class="card shadow-dark border-2">
	<div class="card-header mb-0 pb-0">
		<div
			class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n5 position-absolute">
			<i class="fa-solid fa-funnel-dollar opacity-10"></i>
		</div>
		<div class="text-end pt-1">
			<p class="text-sm text-bold mb-0 text-capitalize">{{__('Filters')}}</p>
		</div>
	</div>
	<div class="card-footer mt-0 pt-0">
		<form method="GET" id="form-filters">
			<div>
				<label for="f_date" class="w-100">{{__('Period')}}
					<div class="input-group input-group-dynamic">
						<button type="button" class="btn btn-secondary btn-sm" id="btn-date-dec">
							<i class="fa-solid fa-chevron-left"></i>
						</button>

						<input class="form-control form-control-sm px-2 text-sm" style="height: 2.35em"
							   name="f_date" id="f_date" type="month" min="2020-01"
							   value="{{ $f_date }}" aria-label="Date filter to show moviments"/>

						<button type="button" class="btn btn-secondary btn-sm" id="btn-date-inc">
							<i class="fa-solid fa-chevron-right"></i>
						</button>
					</div>
				</label>
			</div>

			<x-pages.transaction.part.filter-combo :options="$comboAccounts" name="f_acc" :default="$f_acc" label="Selected Account"/>

			<x-pages.transaction.part.filter-combo :options="$comboPaid" name="f_pay" :default="$f_pay" label="Status"/>

		</form>
	</div>
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
