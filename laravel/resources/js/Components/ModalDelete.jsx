import Modal from '@/Components/Modal.jsx'
import { useContext } from 'react'
import ModalDeleteContext from '@/Contexts/ModalDeleteContext.jsx'

async function handleDelete( routePath, id ) {
	return await axios.delete( `/${ routePath }/${ id }` )
}

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
				// reloadData()
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
