import InputLabel from '@/Components/InputLabel.jsx'
import TextInput from '@/Components/TextInput.jsx'
import Select from '@/Components/Select.jsx'
import { useEffect, useState } from 'react'
import { Button } from '@headlessui/react'
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/react/16/solid/index.js'

export default function  Filters( { className = '', ...props } ) {
	const filters = {
		date: props.f_date,
		account: props.f_acc,
		status: props.f_pay,
	}

	const [filtersValues, setFiltersValues] = useState(filters);
	const [selectedAccounts, setSelectedAccounts] = useState(null);

	const handleMonthChange = (type) => {
		if(filtersValues.date){
			let date = new Date(filtersValues.date + '01T00:00:00')
			console.log(date)

			switch(type){
				case 'NEXT':
					date.setMonth(date.getMonth() + 1)
					date = date.toISOString().split('T')[0].substr(0,7);
					setFiltersValues({...filtersValues, date: [date]})
					break;
				case 'PREV':
					date.setMonth(date.getMonth() - 1)
					date = date.toISOString().split('T')[0].substr(0,7);
					setFiltersValues({...filtersValues, date: [date]})
					break;
				default:
					break;
			}
		}
	}

	// useEffect(() => {
	// 	setFiltersValues(filtersValues)
	// }, [filtersValues])

	return (
		<>
			<div className="mb-2 mt-4">
				<InputLabel htmlFor="f_date" value="Period"/>
				<div className="flex flex-row items-center">
					<Button type='button' className="w-10 h-9 rounded-l-md bg-gray-300 box-border flex items-center justify-center" onClick={()=> handleMonthChange('PREV')}>
						<ChevronLeftIcon className="text-gray-500 hover:text-gray-700 w-8"/>
					</Button>
					<TextInput
						id="f_date"
						type="month"
						name="f_date"
						value={ props.f_date || initialValues.date || '' }
						// onChange={ e => props.setData( props.fieldName, e.target.value ) }
						className="flex-grow text-xs rounded-none h-9"
						isFocused={ true }
					/>
					<Button type='button' className="w-10 h-9 rounded-r-md bg-gray-300 box-border flex items-center justify-center"  onClick={()=> handleMonthChange('NEXT')}>
						<ChevronRightIcon className="text-gray-500 hover:text-gray-700 w-8"/>
					</Button>
				</div>
			</div>

			<div className="mb-2">
				<InputLabel htmlFor="f_acc" value="Select Account"/>
				{/*<PrimeReactProvider value={{ unstyled: true, pt: {} }}>*/}
				{/*	<MultiSelect*/}
				{/*		value={selectedAccounts}*/}
				{/*		optionLabel="name"*/}
				{/*		onChange={(e) => setSelectedAccounts(e.value)}*/}
				{/*		options={dataAccounts}*/}
				{/*		placeholder="All Accounts"*/}
				{/*		className="mt-1 w-full text-xs border-1 rounded-md" />*/}
				{/*</PrimeReactProvider>*/}
				<Select
					id="f_acc"
					name="f_acc"
					// onChange={ e => props.setData( props?.f_acc, e.target.value ) }
					className="mt-1 w-full text-xs"
					data={ props.comboAccounts }
					selected={ 'all' }
				/>

			</div>

			<div className="mb-0">
				<InputLabel htmlFor="f_pay" value="Status"/>
				<Select
					id="f_pay"
					name="f_pay"
					// onChange={ e => props.setData( props?.f_acc, e.target.value ) }
					className="mt-1 w-full text-xs"
					data={ props.comboPaid }
					selected={ 'all' }
				/>
			</div>
		</>
	)
}
