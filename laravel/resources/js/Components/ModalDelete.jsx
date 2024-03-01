import Modal from '@/Components/Modal.jsx'
import { useContext } from 'react'
import ModalDeleteContext from '@/Contexts/ModalDeleteContext.jsx'
import { handleDelete } from '@/Helpers/handleDelete.js'

export default function ModalDelete( props ) {
	const {
		routePath,
		formDeleteModalOpen,
		setFormDeleteModalOpen,
		deleteTextModal,
		type,
		idDataModal,
		reloadData
	} = useContext( ModalDeleteContext )

	return (
		<Modal
			closeable={ true }
			deletable={ true }
			onClose={ () => {
				setFormDeleteModalOpen( false )
			} }
			title={ `Delete this ${ type }` }
			show={ formDeleteModalOpen }
			maxWidth="sm"
			onDelete={ () => {
				handleDelete( routePath, idDataModal ).then(()=> {
					setFormDeleteModalOpen( false )
					reloadData()
				})
			} }
		>
			{ deleteTextModal }
		</Modal>
	)
}
