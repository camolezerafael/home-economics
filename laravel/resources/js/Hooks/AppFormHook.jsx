import ModalContext from '@/Contexts/ModalContext.jsx'
import { useForm } from '@inertiajs/react'
import { handleGetData } from '@/Helpers/handleGetData.js'
import { useContext, useEffect, useState } from 'react'

export default function appFormHook( props ) {

	const { setModalProcessing, setFormModalOpen, routePath, reloadData } = useContext( ModalContext )

	const [ aggregates, setAggregates ] = useState( {} )

	const { data, setData, errors, patch, post, processing } = useForm( props.initialValues )

	const submitParams = {
		routePath,
		setFormModalOpen,
		patch,
		post,
		data,
		reloadData,
	}

	useEffect( () => {
		setModalProcessing( processing )
	}, [ processing ] )

	useEffect( () => {
		if ( props.id ) {
			handleGetData( routePath, props.id ).then( data => setDatas( data ) )
		} else {
			handleGetData( routePath ).then( data => setDatas( data ) )
		}
	}, [] )

	const setDatas = ( data ) => {
		setData( data.item )

		if ( props.hasAggregates ) {
			setAggregates( data.aggregates )
		}
	}

	let loading = true

	if ( props.hasAggregates ) {
		if ( ( !props.id && Object.keys( aggregates ).length ) || ( data.id && Object.keys( aggregates ).length ) ) {
			loading = false
		} else {
			loading = true
		}
	} else {
		if ( !props.id || !data.id ) {
			loading = false
		} else {
			loading = true
		}
	}

	return {
		submitParams,
		loading,
		errors,
		data,
		setData,
		aggregates,
	}
}
