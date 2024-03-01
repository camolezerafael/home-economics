import { useForm } from '@inertiajs/react'
import { useContext, useEffect, useState } from 'react'
import ModalContext from '@/Contexts/ModalContext.jsx'
import { handleGetData } from '@/Helpers/handleGetData.js'
import { handleSubmit } from '@/Helpers/handleSubmit.js'
import FormInput from '@/Components/Form/Input.jsx'
import FormSelect from '@/Components/Form/Select.jsx'

export default function Form( { id = null } ) {
	const { setModalProcessing, setFormModalOpen, routePath, reloadData } = useContext( ModalContext )

	const [ aggregates, setAggregates ] = useState( {} )

	const { data, setData, errors, patch, post, processing } = useForm( {
		id: 0,
		name: '',
		description: '',
		initial_balance: 0,
		decimal_precision: 2,
		type_id: 0,
	} )

	const submitParams = {
		routePath,
		setFormModalOpen,
		patch,
		post,
		data,
		reloadData
	}

	useEffect( () => {
		setModalProcessing( processing )
	}, [ processing ] )

	useEffect( () => {
		if ( id ) {
			handleGetData( routePath, id ).then( data => setDatas( data ) )
		} else {
			handleGetData( routePath ).then( data => setDatas( data ) )
		}
	}, [] )

	const setDatas = ( data ) => {
		setData( data.item )
		setAggregates( data.aggregates )
	}

	let loading = true

	if ( ( !id && Object.keys( aggregates ).length ) || ( data.id && Object.keys( aggregates ).length ) ) {
		loading = false
	} else {
		loading = true
	}

	return (
		<form onSubmit={ ( ev ) => handleSubmit( { ev, ...submitParams } ) } id="form-account"
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
									step: '0.01'
								}
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
									step: '0.01'
								}
							} } />

							<FormSelect { ...{
								isLoading: loading,
								fieldName: 'type_id',
								label: 'Account Type',
								setData,
								errors,
								selected: data?.type_id,
								data: aggregates?.account_type,
							} } />

						</>
					)
			} )() }
		</form>
	)
}
