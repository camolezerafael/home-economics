import InputLabel from '@/Components/InputLabel.jsx'
import TextInput from '@/Components/TextInput.jsx'
import Select from '@/Components/Select.jsx'
import { useState } from 'react'
import { Button } from '@headlessui/react'
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/react/16/solid/index.js'
import { router } from '@inertiajs/react'

const handleApplyFilters = ( filters ) => {
	console.log( 'get', filters )
	router.get( '/transactions', {
		f_date: filters.date,
		f_acc: filters.account,
		f_pay: filters.status,
	} )
}

const handleMonthClick = ( filtersValues, type ) => {
	if ( filtersValues.date ) {
		let date = new Date( filtersValues.date + '-01T00:00:00' )
		switch ( type ) {
			case 'NEXT':
				date.setMonth( date.getMonth() + 1 )
				date = date.toISOString().split( 'T' )[ 0 ].substring( 0, 7 )
				handleApplyFilters( { ...filtersValues, date: date } )
				break
			case 'PREV':
				date.setMonth( date.getMonth() - 1 )
				date = date.toISOString().split( 'T' )[ 0 ].substring( 0, 7 )
				handleApplyFilters( { ...filtersValues, date: date } )
				break
			default:
				break
		}
	}
}


const handleMonthChange = ( filtersValues, value ) => {
	console.log( value )
	if ( value ) {
		let date = new Date( value + '-01T00:00:00' )
		date = date.toISOString().split( 'T' )[ 0 ].substring( 0, 7 )
		handleApplyFilters( { ...filtersValues, date: date } )
	}
}

const handleStatusChange = ( filtersValues, status ) => {
	handleApplyFilters( { ...filtersValues, status: status } )
}

export default function Filters( { className = '', ...props } ) {
	const filters = {
		date: props.f_date,
		account: props.f_acc,
		status: props.f_pay,
	}

	const [ filtersValues, setFiltersValues ] = useState( filters )
	const [ selectedAccounts, setSelectedAccounts ] = useState( null )

	return (
		<>
			<div className="mb-2 mt-4">
				<InputLabel htmlFor="f_date" value="Period"/>
				<div className="flex flex-row items-center">
					<Button type="button"
							className="w-10 h-8 rounded-l-md bg-gray-300 box-border flex items-center justify-center"
							onClick={ () => handleMonthClick( filtersValues, 'PREV' ) }>
						<ChevronLeftIcon className="text-gray-500 hover:text-gray-700 w-8"/>
					</Button>
					<TextInput
						id="f_date"
						type="month"
						name="f_date"
						value={ filtersValues.date || '' }
						onChange={ e => handleMonthChange( filtersValues, e.target.value ) }
						className="flex-grow text-xs rounded-none h-8"
						isFocused={ true }
					/>
					<Button type="button"
							className="w-10 h-8 rounded-r-md bg-gray-300 box-border flex items-center justify-center"
							onClick={ () => handleMonthClick( filtersValues, 'NEXT' ) }>
						<ChevronRightIcon className="text-gray-500 hover:text-gray-700 w-8"/>
					</Button>
				</div>
			</div>

			<div className="mb-2">
				<InputLabel htmlFor="f_acc" value="Select Account"/>
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
					onChange={ e => handleStatusChange( filtersValues, e.target.value ) }
					className="mt-1 w-full text-xs"
					data={ props.comboPaid }
					selected={ filtersValues.status }
				/>
			</div>
		</>
	)
}
