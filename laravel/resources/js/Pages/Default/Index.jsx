import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.jsx'
import { Head } from '@inertiajs/react'

export default function Index( { auth, items, viewAttributes } ) {

	return (
		<AuthenticatedLayout
			user={ auth.user }
			header={ <h2
				className="font-semibold text-xl text-gray-800 leading-tight">{ viewAttributes.pluralItem }</h2> }
		>
			<Head title={ viewAttributes.pluralItem }/>

			<div className="p-4 bg-white rounded-md block">
				<div className="border-2 border-solid border-gray-100 rounded-md">
					<table className="divide-y divide-gray-200 w-full">
						<thead className="bg-gray-50">
							<tr>
								{ viewAttributes.columns.map( ( column, i ) => (
										<th key={ i } scope="col"
											className="py-3.5 px-4 text-sm font-normal text-left rtl:text-right text-gray-500">
											{ column.name }
										</th>
									),
								) }
								<th scope="col"
									className="px-4 py-3.5 text-sm font-normal text-left rtl:text-right text-gray-500 ">Actions
								</th>
							</tr>
						</thead>
						<tbody className="bg-white divide-y divide-gray-200">
							{ items.data.map( ( line, i ) => {
								return (
									<tr key={ i }>
										{ viewAttributes.columns.map( ( col, j ) => {
											let classLine = 'text-left rtl:text-right'
											let value = line[ col.column ]

											if ( col?.cast && col.cast.includes( 'decimal' ) ) {
												value = new Intl.NumberFormat( undefined, {
													style: 'currency',
													currency: 'BRL',
												} ).format( value )
												classLine = 'text-right rtl:text-left'
											}

											return (
												<th key={ j } scope="col"
													className={ 'py-3.5 px-4 text-sm font-normal text-gray-500 ' + classLine }>
													{ ( () => {
														if ( typeof line[ col.column ] === 'object' ) {
															return (
																<>
																	<h4 className="text-gray-700">{ value.name }</h4>
																	<p className="text-gray-400">{ value.description }</p>
																</> )
														} else {
															return ( <>{ value }</> )
														}
													} )()

													}
												</th>
											)
										} ) }
										<td className="px-4 py-4 text-sm whitespace-nowrap">
											<button
												className="px-1 py-1 text-gray-500 transition-colors duration-200 rounded-lg hover:bg-gray-100">
												<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
													 strokeWidth="1.5" stroke="currentColor" className="w-6 h-6">
													<path strokeLinecap="round" strokeLinejoin="round"
														  d="M12 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 18.75a.75.75 0 110-1.5.75.75 0 010 1.5z"/>
												</svg>
											</button>
										</td>
									</tr>
								)
							} ) }
						</tbody>
					</table>
				</div>
			</div>
		</AuthenticatedLayout>
	)
}
