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
import TableListDefaultAuto from '@/Components/TableListDefaultAuto.jsx'

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
					<TableListDefaultAuto data={data} columns={viewAttributes.columns} setIdDataModal={setIdDataModal} setFormModalOpen={setFormModalOpen} setTextDeleteModal={setTextDeleteModal} setFormDeleteModalOpen={setFormDeleteModalOpen}/>
					<ListPagination {...pagination}/>
				</div>
			</div>
		</AuthenticatedLayout>
	)
}
