import { handleSubmit } from '@/Helpers/handleSubmit.js'
import appFormHook from '@/Hooks/AppFormHook.jsx'
import FormInput from '@/Components/Form/Input.jsx'
import FormSelect from '@/Components/Form/Select.jsx'

const fromTosTypes = {
	'FRM': 'From & To (Pessoa + Estabelecimento)',
	'TO': 'To (Estabelecimento)',
	'FT': 'From (Pessoa)',
}

export default function FromTosForm( { id = null } ) {
	const { submitParams, loading, errors, data, setData } = appFormHook( {
		initialValues: {
			id: 0,
			name: '',
			description: '',
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

							<FormInput { ...{
								isLoading: loading,
								fieldName: 'description',
								label: 'Description',
								setData,
								errors,
								data: data,
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