import InputLabel from '@/Components/InputLabel.jsx'
import InputError from '@/Components/InputError.jsx'
import Select from '@/Components/Select.jsx'

export default function FormSelect( { ...props } ) {
	if ( props?.isLoading ) {
		return (
			<div className="mb-5 rounded-md w-full">
				<div className="animate-pulse flex space-x-4">
					<div className="w-full">
						<div className="h-4 bg-gray-200 rounded w-1/4 my-2"></div>
						<div className="h-10 bg-gray-200 rounded"></div>
					</div>
				</div>
			</div>
		)
	}
	return (
		<div className="mb-5">
			<InputLabel htmlFor={ props.fieldName } value={ props.label }/>
			<Select
				id={ props.fieldName }
				name={ props.fieldName }
				onChange={ e => props.setData( props.fieldName, e.target.value ) }
				className="mt-1 w-full"
				data={ props?.data }
				selected={ props?.selected }
				isFocused={ !!props?.focus }
				emptyOption={ props?.emptyOption }
				required={ props?.required }
				{...props?.others}
			/>
			<InputError
				message={ props?.errors && Object.keys( props?.errors ).length ? props?.errors[ props.fieldName ] : null }
				className="mt-2"/>
		</div>
	)
}
