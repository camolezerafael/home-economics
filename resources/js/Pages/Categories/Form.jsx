import { handleSubmit } from '@/Helpers/handleSubmit.js'
import appFormHook from '@/Hooks/AppFormHook.jsx'
import FormInput from '@/Components/Form/Input.jsx'

export default function CategoriesForm( { id = null } ) {
	const { submitParams, loading, errors, data, setData } = appFormHook( {
		initialValues: {
			id: 0,
			name: '',
			description: '',
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
						</>
					)
			} )() }
		</form>
	)
}
