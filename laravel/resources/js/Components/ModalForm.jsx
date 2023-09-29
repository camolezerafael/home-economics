import Modal from '@/Components/Modal.jsx'
import DynamicForm from '@/Components/DynamicForm.jsx'
import { useContext, useState } from 'react'
import ModalFormContext from '@/Contexts/ModalFormContext.jsx'
import ModalContext from '@/Contexts/ModalContext.jsx'

export default function ModalForm(props) {
	const {
		auth,
		formModalOpen,
		setFormModalOpen,
		formName,
		type,
		idDataModal,
		reloadData
	} = useContext(ModalFormContext)

	const [modalProcessing, setModalProcessing] = useState(false)

    return (
		<ModalContext.Provider value={{setModalProcessing, setFormModalOpen}}>
			<Modal
				closeable={true}
				savable={true}
				onClose={() => {
					setFormModalOpen(false)
					reloadData()
				}}
				title={ `Edit ${ type }` }
				show={formModalOpen}
				processing={modalProcessing}
				type={type}
			>
				<DynamicForm form={ formName } auth={ auth } id={idDataModal} />
			</Modal>
		</ModalContext.Provider>
    );
}
