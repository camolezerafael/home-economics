import InputLabel from '@/Components/InputLabel.jsx'
import TextInput from '@/Components/TextInput.jsx'
import Select from '@/Components/Select.jsx'
import { MultiSelect } from 'primereact/multiselect'
import { useState } from 'react'

export default function Filters( { className = '', ...props } ) {
	const initialValues = {
		date: new Date().toISOString().split( 'T' )[ 0 ],
		account: 0,
		status: 0,
	}

	const [selectedAccounts, setSelectedAccounts] = useState(null);

	const dataAccounts = [
		{ code: 'all', name: 'All' },
		{ code: 1, name: 'Account 1' },
		{ code: 2, name: 'Account 2' },
		{ code: 3, name: 'Account 3' },
	]

	const dataStatus = {
		'all': 'All',
		0: 'To pay',
		1: 'Paid',
	}

	return (
		<>

			<div className="mb-2 mt-4">
				<InputLabel htmlFor="f_date" value="Period"/>
				<TextInput
					id="f_date"
					type="date"
					name="f_date"
					value={ initialValues.date || '' }
					// onChange={ e => props.setData( props.fieldName, e.target.value ) }
					className="mt-1 w-full text-xs"
					isFocused={ true }
				/>
			</div>

			<div className="mb-2">
				<InputLabel htmlFor="f_acc" value="Select Account"/>
				<MultiSelect
					value={selectedAccounts}
					optionLabel="name"
					onChange={(e) => setSelectedAccounts(e.value)}
					options={dataAccounts}
					className="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 w-full text-xs" />

			</div>

			<div className="mb-0">
				<InputLabel htmlFor="f_pay" value="Status"/>
				<Select
					id="f_pay"
					name="f_pay"
					// onChange={ e => props.setData( props?.f_acc, e.target.value ) }
					className="mt-1 w-full text-xs"
					data={ dataStatus }
					selected={ '' }
				/>
			</div>
		</>
	)
}