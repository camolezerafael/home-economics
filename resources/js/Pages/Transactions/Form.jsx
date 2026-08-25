import { handleSubmit } from '@/Helpers/handleSubmit.js'
import appFormHook from '@/Hooks/AppFormHook.jsx'
import FormInput from '@/Components/Form/Input.jsx'
import FormSelect from '@/Components/Form/Select.jsx'
import { useEffect } from 'react'

export default function TransactionForm( { id = null } ) {
	const { submitParams, loading, errors, data, setData, aggregates } = appFormHook( {
		initialValues: {
			id: 0,
			account_id: '',
			account_to_id: '',
			transaction_type: '',
			description: '',
			from_to_id: '',
			category_id: '',
			payment_type_id: '',
			amount: 0,
			status: 0,
			date_due: '',
			date_payment: '',
		},
		hasAggregates: true,
		id,
	} )

	const isTransfer = data?.transaction_type === 'TRANS'

	// Ensure amount is always positive for transfers
	useEffect( () => {
		if ( isTransfer && data?.amount && parseFloat( data.amount ) < 0 ) {
			setData( 'amount', Math.abs( parseFloat( data.amount ) ) )
		}
	}, [ isTransfer, data?.amount, setData ] )

	// For transfers, use date_payment if available, otherwise use date_due
	useEffect( () => {
		if ( isTransfer && data?.date_payment && !data?.date_due ) {
			setData( 'date_due', data.date_payment )
		}
	}, [ isTransfer, data?.date_payment, data?.date_due, setData ] )

	return (
		<form onSubmit={ ( ev ) => handleSubmit( { ev, ...submitParams } ) } id="form-save" className="w-full">
			{ ( () => {
				return (
					<>
						<FormSelect { ...{
							isLoading: loading,
							fieldName: 'transaction_type',
							label: 'Type',
							setData,
							errors,
							selected: data?.transaction_type,
							data: aggregates?.types,
							emptyOption: 'Select Type...',
							required: true,
						} } />

						<FormInput { ...{
							isLoading: loading,
							fieldName: 'description',
							label: 'Description',
							setData,
							errors,
							focus: true,
							data: data,
							required: true,
						} } />

						<div className="grid grid-cols-1 md:grid-cols-2 gap-4">
							<FormInput { ...{
								isLoading: loading,
								fieldName: 'amount',
								label: 'Amount',
								setData,
								errors,
								data: data,
								type: 'number',
								others: { 
									step: '0.01',
									min: isTransfer ? '0' : undefined,
								},
								required: true,
								onChange: isTransfer ? (e) => {
									const value = Math.abs(parseFloat(e.target.value) || 0);
									setData('amount', value);
								} : undefined,
							} } />

							<FormInput { ...{
								isLoading: loading,
								fieldName: 'date_due',
								label: isTransfer ? 'Transfer Date' : 'Due Date',
								setData,
								errors,
								data: data,
								type: 'date',
								required: true,
							} } />
						</div>

						<div className="grid grid-cols-1 md:grid-cols-2 gap-4">
							<FormSelect { ...{
								isLoading: loading,
								fieldName: 'account_id',
								label: 'Account',
								setData,
								errors,
								selected: data?.account_id,
								data: aggregates?.accounts,
								emptyOption: 'Select Account...',
								required: true,
							} } />

							{ isTransfer && (
								<FormSelect { ...{
									isLoading: loading,
									fieldName: 'account_to_id',
									label: 'To Account',
									setData,
									errors,
									selected: data?.account_to_id,
									data: aggregates?.accounts,
									emptyOption: 'Select Account...',
									required: true,
								} } />
							) }
						</div>

						{ !isTransfer && (
							<div className="grid grid-cols-1 md:grid-cols-2 gap-4">
								<FormSelect { ...{
									isLoading: loading,
									fieldName: 'from_to_id',
									label: 'From/To',
									setData,
									errors,
									selected: data?.from_to_id,
									data: aggregates?.from_tos,
									emptyOption: 'Select From/To...',
								} } />

								<FormSelect { ...{
									isLoading: loading,
									fieldName: 'category_id',
									label: 'Category',
									setData,
									errors,
									selected: data?.category_id,
									data: aggregates?.categories,
									emptyOption: 'Select Category...',
								} } />
							</div>
						) }

						{ !isTransfer && (
							<div className="grid grid-cols-1 md:grid-cols-2 gap-4">
								<FormSelect { ...{
									isLoading: loading,
									fieldName: 'payment_type_id',
									label: 'Payment Type',
									setData,
									errors,
									selected: data?.payment_type_id,
									data: aggregates?.payment_types,
									emptyOption: 'Select Payment Type...',
								} } />

								<FormSelect { ...{
									isLoading: loading,
									fieldName: 'status',
									label: 'Status',
									setData,
									errors,
									selected: data?.status,
									data: aggregates?.paid,
									emptyOption: 'Select Status...',
									required: true,
								} } />
							</div>
						) }
					</>
				)
			} )() }
		</form>
	)
}
