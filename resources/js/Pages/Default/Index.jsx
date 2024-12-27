import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.jsx'
import { Head, router } from '@inertiajs/react'
import PrimaryButton from '@/Components/PrimaryButton.jsx'
import DangerButton from '@/Components/DangerButton.jsx'
import { useState } from 'react'
import ModalDelete from '@/Components/ModalDelete.jsx'
import ModalForm from '@/Components/ModalForm.jsx'
import ModalFormContext from '@/Contexts/ModalFormContext.jsx'
import ModalDeleteContext from '@/Contexts/ModalDeleteContext.jsx'
import ListPagination from '@/Components/ListPagination.jsx'
import { PencilSquareIcon, TrashIcon } from '@heroicons/react/16/solid/index.js'

export default function Index( { auth, items, viewAttributes } ) {
	const [ formModalOpen, setFormModalOpen ] = useState( false )
	const [ formDeleteModalOpen, setFormDeleteModalOpen ] = useState( false )
	const [ deleteTextModal, setDeleteTextModal ] = useState( '' )
	const [ idDataModal, setIdDataModal ] = useState( null )

	const routePath = viewAttributes.routePath
	const formName = viewAttributes.viewPath
	const type = viewAttributes.singularItem
	const pagination = { ...items }
	const data = items.data

	delete pagination.data
	items = null

	const reloadData = () => {
		router.reload({ only: ['items'] })
	}

	const setTextDeleteModal = ( name ) => {
		const text = `Confirm delete this ${ viewAttributes.singularItem }: "${ name }"?`
		setDeleteTextModal( text )
	}

	return (
		<AuthenticatedLayout
			user={ auth.user }
			header={ <h2
				className="font-semibold text-xl text-gray-800 leading-tight">{ viewAttributes.pluralItem }</h2> }
		>
			<Head title={ viewAttributes.pluralItem }/>

			<ModalFormContext.Provider value={ {
				auth,
				routePath,
				formModalOpen,
				setFormModalOpen,
				formName,
				type,
				idDataModal,
				reloadData
			} }>
				<ModalForm/>
			</ModalFormContext.Provider>

			<ModalDeleteContext.Provider value={ {
				routePath,
				formDeleteModalOpen,
				setFormDeleteModalOpen,
				deleteTextModal,
				type,
				idDataModal,
				reloadData
			} }>
				<ModalDelete/>
			</ModalDeleteContext.Provider>

			<div className="p-4 bg-white rounded-md block">
				<div className="my-2 w-full text-right">
					<PrimaryButton className="py-2 px-3" onClick={() => {
						setIdDataModal( null )
						setFormModalOpen( true )
					} }>New {type}</PrimaryButton>
				</div>

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
									className="px-4 py-3 text-md font-bold text-left rtl:text-right text-gray-500 w-28">Actions
								</th>
							</tr>
						</thead>
						<tbody className="bg-white divide-y divide-gray-300">
							{ data.map( ( line, i ) => {
								return (
									<tr key={ i } className="hover:bg-gray-50">
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
													{
														( () => {
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
											<div className="flex justify-evenly py-1.5">
												<PrimaryButton className="px-1.5 py-1 mx-1"
															   onClick={ () => {
																   setIdDataModal( line.id )
																   setFormModalOpen( true )
															   } }>
													<PencilSquareIcon className="w-4 h-4"/>
												</PrimaryButton>
												<DangerButton className="px-1.5 py-1 mx-1"
															  onClick={ () => {
																  setIdDataModal( line.id )
																  setTextDeleteModal( line.name )
																  setFormDeleteModalOpen( true )
															  } }>
													<TrashIcon className="w-4 h-4"/>
												</DangerButton>
											</div>
										</td>
									</tr>
								)
							} ) }
						</tbody>
					</table>
					<ListPagination {...pagination}/>
				</div>
			</div>
		</AuthenticatedLayout>
	)
}
