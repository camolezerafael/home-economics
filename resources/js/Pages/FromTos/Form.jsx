import { handleSubmit } from '@/Helpers/handleSubmit.js'
import appFormHook from '@/Hooks/AppFormHook.jsx'
import FormInput from '@/Components/Form/Input.jsx'
import FormSelect from '@/Components/Form/Select.jsx'

const fromTosTypes = {
	'FRM' : 'From',
	'TO' : 'To',
	'FT' : 'From & To',
}

export default function FromTosForm( { id = null } ) {
	const { submitParams, loading, errors, data, setData } = appFormHook( {
		initialValues: {
			id: 0,
			name: '',
			type: '',
		},
		hasAggregates: false,
		id
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
								required: true,
							} } />

							<FormSelect { ...{
								isLoading: loading,
								fieldName: 'type',
								label: 'Type',
								setData,
								errors,
								selected: data?.type,
								data: fromTosTypes,
								emptyOption: 'Select Type...',
								required: true,
							} } />
						</>
					)
			} )() }
		</form>
	)
}
