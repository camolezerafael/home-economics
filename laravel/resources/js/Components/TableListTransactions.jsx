import PrimaryButton from '@/Components/PrimaryButton.jsx'
import DangerButton from '@/Components/DangerButton.jsx'
import { useEffect, useState } from 'react'
import { router } from '@inertiajs/react'


const handlePostChangeStatus = async ( id ) => {
	const change = await axios.post( `/transaction/${ id }` )
	if ( change.data === 1 ) {
		await router.reload({only: ['comboAccounts','items','monthTotals','monthBalance','finalBalance','estimatedBalance']})
	} else {
		console.error( change.status, change.statusText )
	}
}

const calculateTotals = ( data ) => {
	let total = 0
	let totalPaid = 0
	let totalToPay = 0

	data.forEach( line => {
		total += line.amount
		if(line.status){
			totalPaid += line.amount
		}else{
			totalToPay += line.amount
		}
	})

	return [total, totalPaid, totalToPay]
}

export default function TableListTransactions( { tabName, tabActive, data } ) {
	const [ dataState, setDataState ] = useState( data )
	const [ totalState, setTotalState ] = useState( 0 )
	const [ totalPaidState, setTotalPaidState ] = useState( 0 )
	const [ totalToPayState, setTotalToPayState ] = useState( 0 )
	const [ totalSelectedState, setTotalSelectedState ] = useState( 0 )

	const handleStatusChange = ( ev ) => {
		handlePostChangeStatus( ev.target.value )
	}

	const handleLineSelect = ( ev ) => {
		const line = dataState.find( l => l.id.toString() === ev.target.value )

		if(ev.target.checked){
			setTotalSelectedState(totalSelectedState.toPrecision(2) + line.amount.parseFloat().toPrecision(2))
		}else{
			setTotalSelectedState(totalSelectedState.toPrecision(2) - line.amount.parseFloat().toPrecision(2))
		}
	}

	useEffect( () => {
		setDataState(data)

		const [total, totalPaid, totalToPay] = calculateTotals(data)

		setTotalState(total)
		setTotalPaidState(totalPaid)
		setTotalToPayState(totalToPay)
	}, [data]);

	let isInactiveClass = ( tabActive !== tabName ) && 'hidden'

	return (
		<div className={ 'border-2 border-solid border-gray-100 rounded-md ' + isInactiveClass }>
			<table className="divide-y divide-gray-400 w-full">
				<thead className="bg-gray-200">
					<tr>
						<th scope="col" className="px-4 py-3 text-xs font-bold text-left rtl:text-right text-gray-500">
							&nbsp;
						</th>
						<th scope="col" className="px-4 py-3 text-xs font-bold text-left rtl:text-right text-gray-500">
							DUE DATE
						</th>
						<th scope="col" className="px-4 py-3 text-xs font-bold text-left rtl:text-right text-gray-500">
							DESCRIPTION
						</th>
						<th scope="col" className="px-4 py-3 text-xs font-bold text-left rtl:text-right text-gray-500">
							FROM
						</th>
						<th scope="col" className="px-4 py-3 text-xs font-bold text-left rtl:text-right text-gray-500">
							AMOUNT
						</th>
						<th scope="col" className="px-4 py-3 text-xs font-bold text-left rtl:text-right text-gray-500">
							CATEGORY
						</th>
						<th scope="col" className="px-4 py-3 text-xs font-bold text-left rtl:text-right text-gray-500">
							PAYMENT
						</th>
						<th scope="col" className="px-4 py-3 text-xs font-bold text-left rtl:text-right text-gray-500">
							PAID
						</th>
						<th scope="col"
							className="px-4 py-3 text-xs font-bold text-left rtl:text-right text-gray-500 w-20">
							ACTIONS
						</th>
					</tr>
				</thead>
				<tbody className="bg-white divide-y divide-gray-300">
					{ dataState?.map( ( line, i ) => {
						let classNames = '';

						if(!line.status && line.is_late) {
							classNames += ' bg-red-100'
						}

						if(!line.status && line.is_today) {
							classNames += ' bg-yellow-100'
						}

						if(line.status && line.is_paid_late) {
							classNames += ' bg-green-100'
						}

						if(line.status && !line.is_paid_late) {
							classNames += ' bg-green-300'
						}

						return (
							<tr key={ i } className={ 'hover:bg-gray-50' + classNames }>
								<td scope="col" className={ 'font-normal text-gray-500 ' }>
									<div className="flex items-center">
										<input type="checkbox" value={ line.id } onChange={ handleLineSelect }
											   className="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"/>
									</div>
								</td>
								<td scope="col" className={ 'py-1 px-4 font-normal text-gray-500 ' }>
									<span className="text-sm">{ line.date_due }</span>
								</td>
								<td scope="col" className={ 'py-1 px-4 font-normal text-gray-600 ' }>
									<span className="text-sm">{ line.description }</span>
									<p className="text-gray-400 text-xs">{ line.account }</p>
								</td>
								<td scope="col" className={ 'py-1 px-4 font-normal text-gray-500 ' }>
									<span className="text-sm">{ line.from_to }</span>
								</td>
								<td scope="col" className={ 'py-1 px-4 font-normal text-gray-600 ' }>
									<span className="text-md">{ line.amount }</span>
								</td>
								<td scope="col" className={ 'py-1 px-4 font-normal text-gray-500 ' }>
									<span className="text-sm">{ line.category }</span>
								</td>
								<td scope="col" className={ 'py-1 px-4 font-normal text-gray-500 ' }>
									<span className="text-sm">{ line.payment_type }</span>
								</td>
								<td scope="col" className={ 'py-1 px-4 font-normal ' }>
									<label className="inline-flex items-center cursor-pointer pt-2.5">
										<input type="checkbox" value={ line.id } className="sr-only peer"
											   checked={ line.status } onChange={ handleStatusChange }/>
										<div
											className="relative w-8 h-4 bg-red-400 peer-focus:outline-none rounded-full peer dark:bg-red-400 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[1.5px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-3 after:w-3.5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600"></div>
									</label>
								</td>
								<td className="text-xs whitespace-nowrap">
									<div className="flex justify-evenly">
										<PrimaryButton className="px-2 py-1.5 mx-0">
											<svg xmlns="http://www.w3.org/2000/svg" fill="none"
												 viewBox="0 0 24 24" strokeWidth={ 1.5 } stroke="currentColor"
												 className="w-3 h-3">
												<path strokeLinecap="round" strokeLinejoin="round"
													  d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"/>
											</svg>
										</PrimaryButton>
										<DangerButton className="px-2 py-1.5 mx-0">
											<svg xmlns="http://www.w3.org/2000/svg" fill="none"
												 viewBox="0 0 24 24" strokeWidth={ 1.5 } stroke="currentColor"
												 className="w-3 h-3">
												<path strokeLinecap="round" strokeLinejoin="round"
													  d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
											</svg>
										</DangerButton>
									</div>
								</td>
							</tr>
						)
					} ) }
				</tbody>
			</table>
			<hr/>
			<br/>
			<div className="flex flex-row justify-center">
				<div className="basis-1/4 border-solid border-2 border-gray-300 rounded-md my-2">
					<div className="text-sm text-gray-900 flex bg-gray-200">
						<span className="w-24 text-md text-end me-3">Total:</span>
						<span className="grow text-md ">{totalState.toFixed(2)}</span>
					</div>
					<div className="text-sm text-gray-500 flex">
						<span className="w-24 text-md text-end me-3">Paid:</span>
						<span className="grow text-md">{totalPaidState.toFixed(2)}</span>
					</div>
					<div className="text-sm text-gray-500 flex bg-gray-200">
						<span className="w-24 text-md text-end me-3">To Pay:</span>
						<span className="grow text-md">{ totalToPayState.toFixed(2) }</span>
					</div>
					<div className="text-sm text-gray-500 flex bg-gray-200">
						<span className="w-24 text-md text-end me-3">Selected:</span>
						<span className="grow text-md">{ totalSelectedState.toFixed(2) }</span>
					</div>
				</div>
			</div>
		</div>
	)
}

