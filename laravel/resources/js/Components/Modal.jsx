import { Fragment } from 'react'
import { Dialog, Transition } from '@headlessui/react'
import PrimaryButton from '@/Components/PrimaryButton.jsx'
import SecondaryButton from '@/Components/SecondaryButton.jsx'
import DangerButton from '@/Components/DangerButton.jsx'

export default function Modal( {
								   children,
								   show = false,
								   maxWidth = '2xl',
								   closeable = true,
								   onClose = () => {},
								   savable = false,
								   onSave = () => {},
								   deletable = false,
								   onDelete = () => {},
								   title = 'Modal',
								   processing = false,
								   type = ''
							   } ) {
	const handleClose = () => {
		if ( closeable ) {
			onClose()
		}
	}

	const handleSave = () => {
		if ( savable ) {
			onSave()
		}
	}

	const handleDelete = () => {
		if ( deletable ) {
			onDelete()
		}
	}

	const maxWidthClass = {
		sm: 'sm:max-w-sm',
		md: 'sm:max-w-md',
		lg: 'sm:max-w-lg',
		xl: 'sm:max-w-xl',
		'2xl': 'sm:max-w-2xl',
	}[ maxWidth ]

	return (
		<Transition show={ show } as={ Fragment } leave="duration-200">
			<Dialog
				as="div"
				id="modal"
				className="fixed inset-0 flex overflow-y-auto px-2 py-3 sm:px-0 items-center z-50 transform transition-all"
				onClose={ handleClose }
			>
				<Transition.Child
					as={ Fragment }
					enter="ease-out duration-300"
					enterFrom="opacity-0"
					enterTo="opacity-100"
					leave="ease-in duration-200"
					leaveFrom="opacity-100"
					leaveTo="opacity-0"
				>
					<div className="absolute inset-0 bg-gray-700/75"/>
				</Transition.Child>

				<Transition.Child
					as={ Fragment }
					enter="ease-out duration-300"
					enterFrom="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
					enterTo="opacity-100 translate-y-0 sm:scale-100"
					leave="ease-in duration-200"
					leaveFrom="opacity-100 translate-y-0 sm:scale-100"
					leaveTo="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
				>
					<Dialog.Panel
						className={ `mb-4 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:mx-auto ${ maxWidthClass }` }
					>
						<div className="flex flex-col w-full divide-y divide-gray-300">
							<header className="flex w-full content-center justify-between p-2">
								<h1 className="h-6 text-lg">{ title }</h1>
								<button className="hover:text-gray-700 transform transition-all duration-150"
										onClick={ handleClose }>
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
										 strokeWidth={ 1.5 } stroke="currentColor" className="w-6 h-6">
										<path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12"/>
									</svg>
								</button>
							</header>
							<div className="flex w-full p-6">
								{ children }
							</div>
							<footer className="flex w-full justify-end p-2">
								{ closeable &&
									<PrimaryButton
										onClick={ handleClose }
										className="px-2 py-1.5 mx-1">
										Cancel
									</PrimaryButton>
								}

								{ savable &&
									<SecondaryButton
										type="submit"
										form={ `form-save` }
										className="px-2 py-1.5 mx-1"
										disabled={processing}
									>
										{(()=>{
											if(processing){
											return (
												<>
													<svg className="animate-spin -ml-1 mr-3 h-5 w-5 text-black" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
														<circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
														<path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
													</svg>
													Saving...
												</>
											)
											}else{
												return(
													<>
														Save
													</>
												)

											}
										})()}
									</SecondaryButton>
								}

								{ deletable &&
									<DangerButton
										onClick={ handleDelete }
										className="px-2 py-1.5 mx-1"
										disabled={processing}
									>
										Delete
									</DangerButton>
								}
							</footer>
						</div>
					</Dialog.Panel>
				</Transition.Child>
			</Dialog>
		</Transition>
	)
}
