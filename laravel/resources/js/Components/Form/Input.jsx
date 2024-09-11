import InputLabel from '@/Components/InputLabel.jsx'
import TextInput from '@/Components/TextInput.jsx'
import InputError from '@/Components/InputError.jsx'

export default function FormInput( { ...props } ) {
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
			<TextInput
				id={ props.fieldName }
				type={ props?.type || 'text' }
				name={ props.fieldName }
				value={ props?.data?.[props.fieldName] || '' }
				onChange={ e => props.setData( props.fieldName, e.target.value ) }
				className="mt-1 w-full"
				isFocused={ !!props?.focus }
				placeholder={ props?.placeholder || props?.label }
				{...props?.others}
			/>
			<InputError
				message={ props?.errors && Object.keys( props?.errors ).length ? props?.errors[ props.fieldName ] : null }
				className="mt-2"/>
		</div>
	)
}
