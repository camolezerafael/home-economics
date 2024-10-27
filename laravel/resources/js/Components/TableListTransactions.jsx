import PrimaryButton from '@/Components/PrimaryButton.jsx'
import DangerButton from '@/Components/DangerButton.jsx'
import { useState } from 'react'

export default function TableListTransactions( { tabName, tabActive, data } ) {
	let isInactiveClass = (tabActive !== tabName) && 'hidden';

	const [dataState, setDataState] = useState(data);

	const handleStatusChange = (ev) => {
		console.log(ev.target.value)
		const newData = dataState.map((item)=>{
			if(item.id === ev.target.value){
				item.paid = !item.paid;
			}
			return item;
		})
		setDataState(newData);
	};

	return (
		<div className={ 'border-2 border-solid border-gray-100 rounded-md ' + isInactiveClass}>
			<table className="divide-y divide-gray-400 w-full">
				<thead className="bg-gray-200">
					<tr>
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
						<th scope="col" className="px-4 py-3 text-xs font-bold text-left rtl:text-right text-gray-500 w-20">
							ACTIONS
						</th>
					</tr>
				</thead>
				<tbody className="bg-white divide-y divide-gray-300">
					{ dataState?.map( ( line, i ) => {
						return (
							<tr key={ i } className="hover:bg-gray-50">
								<td scope="col" className={ 'py-1 px-4 font-normal text-gray-500 ' }>
									<span className="text-sm">{line.date_due}</span>
								</td>
								<td scope="col" className={ 'py-1 px-4 font-normal text-gray-600 ' }>
									<span className="text-sm">{line.description}</span>
									<p className="text-gray-400 text-xs">{line.account}</p>
								</td>
								<td scope="col" className={ 'py-1 px-4 font-normal text-gray-500 ' }>
									<span className="text-sm">{line.from_to}</span>
								</td>
								<td scope="col" className={ 'py-1 px-4 font-normal text-gray-600 ' }>
									<span className="text-md">{line.amount}</span>
								</td>
								<td scope="col" className={ 'py-1 px-4 font-normal text-gray-500 ' }>
									<span className="text-sm">{line.category}</span>
								</td>
								<td scope="col" className={ 'py-1 px-4 font-normal text-gray-500 ' }>
									<span className="text-sm">{line.payment_type}</span>
								</td>
								<td scope="col" className={ 'py-1 px-4 font-normal ' }>
									<label className="inline-flex items-center cursor-pointer">
										<input type="checkbox" value={line.id} className="sr-only peer" checked={line.paid} onChange={handleStatusChange}/>
										<div
											className="relative w-9 h-5 bg-red-400 peer-focus:outline-none rounded-full peer dark:bg-red-400 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-600 peer-checked:bg-green-600"></div>
									</label>
								</td>
								<td className="text-xs whitespace-nowrap">
									<div className="flex justify-evenly py-1">
										<PrimaryButton className="px-2 py-1.5 mx-1">
											<svg xmlns="http://www.w3.org/2000/svg" fill="none"
												 viewBox="0 0 24 24" strokeWidth={ 1.5 } stroke="currentColor"
												 className="w-3 h-3">
												<path strokeLinecap="round" strokeLinejoin="round"
													  d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"/>
											</svg>
										</PrimaryButton>
										<DangerButton className="px-2 py-1.5 mx-1">
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
		</div>
	)
}

