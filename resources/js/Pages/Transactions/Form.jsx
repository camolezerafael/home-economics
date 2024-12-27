import { handleSubmit } from '@/Helpers/handleSubmit.js'
import appFormHook from '@/Hooks/AppFormHook.jsx'
import FormInput from '@/Components/Form/Input.jsx'

export default function TransactionForm( { id = null } ) {
	const { submitParams, loading, errors, data, setData } = appFormHook( {
		initialValues: {
			id: 0,
			name: '',
			type: '',
		},
		hasAggregates: false,
		id,
	} )

	return (
		<form onSubmit={ ( ev ) => handleSubmit( { ev, ...submitParams } ) } id="form-save" className="w-full">
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
						} } />
					</>
				)
			} )() }
		</form>
	)
}
