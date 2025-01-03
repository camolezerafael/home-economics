import { handleSubmit } from '@/Helpers/handleSubmit.js'
import FormInput from '@/Components/Form/Input.jsx'
import FormSelect from '@/Components/Form/Select.jsx'
import appFormHook from '@/Hooks/AppFormHook.jsx'

export default function AccountsForm( { id = null } ) {
	const { submitParams, loading, errors, data, setData, aggregates } = appFormHook( {
		initialValues: {
			id: 0,
			name: '',
			description: '',
			initial_balance: 0,
			decimal_precision: 2,
			type_id: 0,
		},
		hasAggregates: true,
		id
	} )

	return (
		<form onSubmit={ ( ev ) => handleSubmit( { ev, ...submitParams } ) } id="form-save"
			  className="w-full">
			{ ( () => {
				return (
					<>
						<FormInput { ...{
							isLoading: loading,
							fieldName: 'name',
							label: 'Name',
							setData,
							errors,
							focus: true,
							data: data,
							required: true,
						} } />

						<FormInput { ...{
							isLoading: loading,
							fieldName: 'description',
							label: 'Description',
							setData,
							errors,
							data: data,
						} } />

						<FormInput { ...{
							isLoading: loading,
							fieldName: 'initial_balance',
							label: 'Initial Balance',
							setData,
							errors,
							data: data,
							type: 'number',
							others: {
								step: '0.01',
							},
							required: true,
						} } />

						<FormInput { ...{
							isLoading: loading,
							fieldName: 'decimal_precision',
							label: 'Decimal Precision',
							setData,
							errors,
							data: data,
							type: 'number',
							others: {
								step: '0.01',
							},
							required: true,
						} } />

						<FormSelect { ...{
							isLoading: loading,
							fieldName: 'type_id',
							label: 'Account Type',
							setData,
							errors,
							selected: data?.type_id,
							data: aggregates?.account_type,
							required: true,
						} } />

					</>
				)
			} )() }
		</form>
	)
}
