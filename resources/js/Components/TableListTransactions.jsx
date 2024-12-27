import PrimaryButton from '@/Components/PrimaryButton.jsx'
import DangerButton from '@/Components/DangerButton.jsx'
import { useEffect, useState } from 'react'
import { router } from '@inertiajs/react'
import { PencilSquareIcon, TrashIcon } from '@heroicons/react/16/solid/index.js'


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

export default function TableListTransactions( { data } ) {
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
			setTotalSelectedState(totalSelectedState + line.amount)
		}else{
			setTotalSelectedState(totalSelectedState - line.amount)
		}
	}

	useEffect( () => {
		setDataState(data)

		const [total, totalPaid, totalToPay] = calculateTotals(data)

		setTotalState(total)
		setTotalPaidState(totalPaid)
		setTotalToPayState(totalToPay)
	}, [data]);

	return (
		<div className={ 'border-2 border-solid border-gray-100 rounded-md '}>
			<table className="divide-y divide-gray-400 w-full">
				<thead className="bg-gray-200">
					<tr>
						<th scope="col" className="px-4 py-3 text-xs font-bold text-left rtl:text-right text-gray-500 text-center">
							#
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
						<th scope="col" className="px-4 py-3 text-xs font-bold text-left rtl:text-right text-gray-500 text-center">
							PAID
						</th>
						<th scope="col"
							className="px-4 py-3 text-xs font-bold text-left rtl:text-right text-gray-500 w-20 text-center">
							ACTIONS
						</th>
					</tr>
				</thead>
				<tbody className="bg-white divide-y divide-gray-300">
					{ dataState?.map( ( line, i ) => {
						let classNames = '';

						if(!line.status && line.is_late) {
							classNames += ' bg-red-200'
						}

						if(!line.status && line.is_today) {
							classNames += ' bg-amber-200'
						}

						if(line.status && line.is_paid_late) {
							classNames += ' bg-lime-200'
						}

						if(line.status && !line.is_paid_late) {
							classNames += ' bg-green-200'
						}

						return (
							<tr key={ i } className={ 'hover:bg-gray-50' + classNames }>
								<td scope="col" className={ 'font-normal text-gray-500' }>
									<div className="flex items-center justify-center">
										<input type="checkbox" value={ line.id } onChange={ handleLineSelect }
											   className="w-4 h-4 cursor-pointer text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"/>
									</div>
								</td>
								<td scope="col" className={ 'py-1 px-4 font-normal text-gray-500 ' }>
									<span className={ 'text-sm' + (line.is_paid_late ? ' text-red-800 font-bold' : '')  }>{ line.date_due }</span>
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
								<td scope="col" className={ 'py-1 px-4 font-normal text-center ' }>
									<label className="inline-flex items-center cursor-pointer pt-2.5">
										<input type="checkbox" value={ line.id } className="sr-only peer"
											   checked={ line.status } onChange={ handleStatusChange }/>
										<div
											className="relative w-8 h-4 bg-red-400 peer-focus:outline-none rounded-full peer dark:bg-red-400 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[1.5px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-3 after:w-3.5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600"></div>
									</label>
								</td>
								<td className="text-xs whitespace-nowrap">
									<div className="flex justify-evenly">
										<PrimaryButton className="px-1.5 py-1 mx-0">
											<PencilSquareIcon className="w-4 h-4"/>
										</PrimaryButton>
										<DangerButton className="px-1.5 py-1 mx-0">
											<TrashIcon className="w-4 h-4"/>
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
					{
						( () => {
							if ( totalSelectedState ) {
								return (
									<>
										<div className="text-sm text-gray-900 flex bg-gray-200">
											<span className="w-24 text-md text-end me-3">Selected:</span>
											<span className="grow text-md">{ totalSelectedState.toFixed( 2 ) }</span>
										</div>
									</> )
							} else {
								return (
									<>
										<div className="text-sm text-gray-900 flex bg-gray-200">
											<span className="w-24 text-md text-end me-3">Total:</span>
											<span className="grow text-md ">{ totalState.toFixed( 2 ) }</span>
										</div>
										<div className="text-sm text-gray-500 flex">
											<span className="w-24 text-md text-end me-3">Paid:</span>
											<span className="grow text-md">{ totalPaidState.toFixed( 2 ) }</span>
										</div>
										<div className="text-sm text-gray-500 flex bg-gray-200">
											<span className="w-24 text-md text-end me-3">To Pay:</span>
											<span className="grow text-md">{ totalToPayState.toFixed( 2 ) }</span>
										</div>
									</>
								)
							}
						} )()
					}
				</div>
			</div>
		</div>
	)
}

