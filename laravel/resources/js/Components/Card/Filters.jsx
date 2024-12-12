import InputLabel from '@/Components/InputLabel.jsx'
import TextInput from '@/Components/TextInput.jsx'
import Select from '@/Components/Select.jsx'
import { useEffect, useState } from 'react'
import { Button, Combobox, ComboboxButton, ComboboxInput, ComboboxOption, ComboboxOptions } from '@headlessui/react'
import { CheckIcon, ChevronDownIcon, ChevronLeftIcon, ChevronRightIcon } from '@heroicons/react/16/solid/index.js'
import { router } from '@inertiajs/react'
import clsx from 'clsx'

const handleApplyFilters = ( filters ) => {
	const data = {
		f_date: filters.date,
		f_acc: filters.account,
		f_pay: filters.status,
	}
	router.get( '/transactions', data )
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
	if ( value ) {
		let date = new Date( value + '-01T00:00:00' )
		date = date.toISOString().split( 'T' )[ 0 ].substring( 0, 7 )
		handleApplyFilters( { ...filtersValues, date: date } )
	}
}

const handleAccountChange = ( filtersValues, setFiltersValues, account ) => {
	if(account === filtersValues.account){
		return
	}

	if ( account.includes( 'all' ) || account === 'all' || account.length === 0 ) {
		handleApplyFilters( { ...filtersValues, account: 'all' } )
	} else {
		let accounts = account
		if ( Array.isArray( accounts ) ) {
			accounts = account.map( acc => acc).join( ',' )
		}
		handleApplyFilters( { ...filtersValues, account: accounts } )
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
	const [ selectedAccounts, setSelectedAccounts ] = useState( filters.account )
	const [ selectedAccountsField, setSelectedAccountsField ] = useState( filters.account )

	useEffect( () => {
		let ret = selectedAccounts

		if ( typeof ret === 'string' ) {
			ret = props.comboAccounts[ret].name
		}else{
			ret = ret.map( id => {
				return props.comboAccounts[id].name
			})
		}
		setSelectedAccountsField(ret.join(', '))
	}, [selectedAccounts])

	return (
		<div className="flex flex-col flex-auto">
			<div className="mb-2 mt-4">
				<InputLabel htmlFor="f_date" value="Period"/>
				<div className="flex flex-row items-center flex-auto">
					<Button type="button"
							className="w-8 h-8 rounded-l-md bg-gray-300 box-border flex items-center justify-center grow-0"
							onClick={ () => handleMonthClick( filtersValues, 'PREV' ) }>
						<ChevronLeftIcon className="text-gray-500 hover:text-gray-700 w-6"/>
					</Button>
					<TextInput
						id="f_date"
						type="month"
						name="f_date"
						value={ filtersValues.date || '' }
						onChange={ e => handleMonthChange( filtersValues, e.target.value ) }
						className="text-xs rounded-none h-8 flex-auto"
						isFocused={ true }
					/>
					<Button type="button"
							className="w-8 h-8 rounded-r-md bg-gray-300 box-border flex items-center justify-center grow-0"
							onClick={ () => handleMonthClick( filtersValues, 'NEXT' ) }>
						<ChevronRightIcon className="text-gray-500 hover:text-gray-700 w-6"/>
					</Button>
				</div>
			</div>

			<InputLabel htmlFor="f_acc" value="Select Account"/>
			<div className="mb-2 flex flex-col flex-auto">
				<Combobox name="f_acc" multiple value={ selectedAccounts } onChange={ e => setSelectedAccounts(e) }
						  onClose={ () => handleAccountChange( filtersValues, setFiltersValues, selectedAccounts ) }>
					<div className="relative">
						<ComboboxInput aria-label="Accounts"
									   className={ clsx(
										   'w-full rounded-lg border-1 border-gray-300 bg-white/5 py-1.5 pr-8 pl-3 text-xs text-black cursor-pointer',
										   'focus:outline-none data-[focus]:outline-2 data-[focus]:-outline-offset-2 data-[focus]:outline-blue/25',
									   ) } value={ selectedAccountsField }>
						</ComboboxInput>
						<ComboboxButton className="group absolute inset-y-0 right-0 px-2.5">
							<ChevronDownIcon className="size-4 fill-black/60 group-data-[hover]:fill-black"/>
						</ComboboxButton>
					</div>
					<ComboboxOptions anchor="bottom" transition className={ clsx(
						'w-[var(--input-width)] rounded-xl border border-black/5 bg-white p-1 [--anchor-gap:var(--spacing-1)] empty:invisible',
						'transition duration-200 ease-in data-[leave]:data-[closed]:opacity-0',
					) }>
						{ Object.entries( props.comboAccounts ).map( ( [ id, account ] ) => (
							<ComboboxOption key={ id } value={ id }
											className="group flex cursor-default items-center gap-2 rounded-lg py-1.5 px-3 select-none data-[focus]:bg-white/10">
								<CheckIcon className="invisible size-4 fill-green-400 group-data-[selected]:visible"/>
								<div className="text-sm/6 text-black">{ account.name } <span className="font-bold">( { account.balance } )</span></div>
							</ComboboxOption>
						) )
						}
					</ComboboxOptions>
				</Combobox>
			</div>

			<div className="mb-0 flex flex-col">
				<InputLabel htmlFor="f_pay" value="Status"/>
				<Select
					id="f_pay"
					name="f_pay"
					onChange={ e => handleStatusChange( filtersValues, e.target.value ) }
					className="mt-1 flex-auto text-xs"
					data={ props.comboPaid }
					selected={ filtersValues.status }
				/>
			</div>
		</div>
	)
}
