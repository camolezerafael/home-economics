import { forwardRef, useEffect, useRef, useState } from 'react'

export default forwardRef( function Select( {
												className = '',
												isFocused = false,
												data = {},
												selected = 0,
												...props
											}, ref ) {
	const select = ref ? ref : useRef()

	useEffect( () => {
		if ( isFocused ) {
			select.current.focus()
		}
	}, [] )

	return (
		<select
			{ ...props }
			className={
				'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm ' +
				className
			}
			ref={ select }
			defaultValue={selected}
		>
			{ props.emptyOption && <option value="" disabled>{ props.emptyOption }</option> }
			{ ( () => {
				if ( data ) {
					return Object.keys( data ).map( ( opt ) => (
						<option key={ opt } value={ opt }>{ data[ opt ] }</option>
					) )
				} else {
					return <option key={ 0 } value={ 0 }>No Options...</option>
				}
			} )() }
		</select>
	)
} )
