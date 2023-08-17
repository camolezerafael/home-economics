import Modal from '@/Components/Modal.jsx'
import { useContext } from 'react'
import ModalDeleteContext from '@/Contexts/ModalDeleteContext.jsx'

export default function ModalDelete(props) {
	const {formDeleteModalOpen, setFormDeleteModalOpen, deleteTextModal, type} = useContext(ModalDeleteContext)

    return (
        <Modal closeable={true} deletable={true} onClose={()=>setFormDeleteModalOpen(false)} title={ `Delete this ${ type }` } show={formDeleteModalOpen} maxWidth="sm">
			{deleteTextModal}
		</Modal>
    );
}
