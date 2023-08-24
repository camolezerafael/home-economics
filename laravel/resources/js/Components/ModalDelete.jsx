import Modal from '@/Components/Modal.jsx'
import { useContext } from 'react'
import ModalDeleteContext from '@/Contexts/ModalDeleteContext.jsx'
import ModalContext from '@/Contexts/ModalContext.jsx'

async function handleDelete( type, id ) {
	return await axios.delete( `/${ type.toString().toLowerCase() }/${ id }` )
}

export default function ModalDelete( props ) {
	const {
		formDeleteModalOpen,
		setFormDeleteModalOpen,
		deleteTextModal,
		type,
		idDataModal
	} = useContext( ModalDeleteContext )

	return (
		<Modal
			closeable={ true }
			deletable={ true }
			onClose={ () => setFormDeleteModalOpen( false ) }
			title={ `Delete this ${ type }` }
			show={ formDeleteModalOpen }
			maxWidth="sm"
			onDelete={ () => {
				handleDelete( type, idDataModal ).then(()=>setFormDeleteModalOpen())
			} }
		>
			{ deleteTextModal }
		</Modal>
	)
}
