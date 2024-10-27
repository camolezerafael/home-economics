import InputLabel from '@/Components/InputLabel.jsx'
import TextInput from '@/Components/TextInput.jsx'
import Select from '@/Components/Select.jsx'
import { MultiSelect } from 'primereact/multiselect'
import { useState } from 'react'
import { PrimeReactProvider } from 'primereact/api'


export default function Filters( { className = '', ...props } ) {
	const initialValues = {
		date: new Date().toISOString().split( 'T' )[ 0 ].substring( 0, 7 ),
		account: 'all',
		status: 'all',
	}

	console.log( props )

	const [selectedAccounts, setSelectedAccounts] = useState(null);

	return (
		<>
			<div className="mb-2 mt-4">
				<InputLabel htmlFor="f_date" value="Period"/>
				<TextInput
					id="f_date"
					type="month"
					name="f_date"
					value={ props.f_date || initialValues.date || '' }
					// onChange={ e => props.setData( props.fieldName, e.target.value ) }
					className="mt-1 w-auto text-xs"
					isFocused={ true }
				/>
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
