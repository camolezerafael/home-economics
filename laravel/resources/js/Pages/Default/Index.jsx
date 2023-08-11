import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.jsx'
import { Head } from '@inertiajs/react'
import PrimaryButton from '@/Components/PrimaryButton.jsx'
import DangerButton from '@/Components/DangerButton.jsx'
import Modal from '@/Components/Modal.jsx'
import { useState } from 'react'
import DynamicEdit from '@/Components/DynamicEdit.jsx'

export default function Index( { auth, items, viewAttributes } ) {
	const [ formEditModalOpen, setFormEditModalOpen ] = useState( false )
	const formEdit = viewAttributes.viewPath
	console.clear()
	console.log( formEdit, auth, viewAttributes )

	return (
		<AuthenticatedLayout
			user={ auth.user }
			header={ <h2
				className="font-semibold text-xl text-gray-800 leading-tight">{ viewAttributes.pluralItem }</h2> }
		>
			<Head title={ viewAttributes.pluralItem }/>

			<Modal closeable={ true } show={ formEditModalOpen } title={ `Edit ${ viewAttributes.singularItem }` }
				   onClose={ () => setFormEditModalOpen( false ) }>
				<DynamicEdit form={ formEdit }/>
			</Modal>
			<div className="p-4 bg-white rounded-md block">
				<div className="border-2 border-solid border-gray-100 rounded-md">
					<table className="divide-y divide-gray-400 w-full">
						<thead className="bg-gray-200">
							<tr>
								{ viewAttributes.columns.map( ( column, i ) => (
										<th key={ i } scope="col"
											className="px-4 py-3 text-md font-bold text-left rtl:text-right text-gray-500">
											{ column.name }
										</th>
									),
								) }
								<th scope="col"
									className="px-4 py-3 text-md font-bold text-left rtl:text-right text-gray-500 ">Actions
								</th>
							</tr>
						</thead>
						<tbody className="bg-white divide-y divide-gray-300">
							{ items.data.map( ( line, i ) => {
								return (
									<tr key={ i } className="hover:bg-gray-50 ">
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
												<td key={ j } scope="col"
													className={ 'py-1 px-4 text-sm font-normal text-gray-500 ' + classLine }>
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
												</td>
											)
										} ) }
										<td className="text-sm whitespace-nowrap">
											<div className="flex justify-evenly">
												<PrimaryButton className="px-2 py-1.5 mx-1"
															   onClick={ () => setFormEditModalOpen( true ) }>
													<svg xmlns="http://www.w3.org/2000/svg" fill="none"
														 viewBox="0 0 24 24" strokeWidth={ 1.5 } stroke="currentColor"
														 className="w-4 h-4">
														<path strokeLinecap="round" strokeLinejoin="round"
															  d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"/>
													</svg>
												</PrimaryButton>
												<DangerButton className="px-2 py-1.5 mx-1">
													<svg xmlns="http://www.w3.org/2000/svg" fill="none"
														 viewBox="0 0 24 24" strokeWidth={ 1.5 } stroke="currentColor"
														 className="w-4 h-4">
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
			</div>
		</AuthenticatedLayout>
	)
}
