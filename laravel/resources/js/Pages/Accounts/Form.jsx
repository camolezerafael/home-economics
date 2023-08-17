import InputLabel from '@/Components/InputLabel.jsx'
import TextInput from '@/Components/TextInput.jsx'
import InputError from '@/Components/InputError.jsx'
import { useForm } from '@inertiajs/react'
import { useContext, useEffect } from 'react'
import ModalContext from '@/Contexts/ModalContext.jsx'
import { post } from 'axios'

async function handleGetData(id){
	const response = await axios.get(`/account/${id}/edit`)
	return response.data.item
}

export default function Form( { id = null } ) {
	const {setModalProcessing, setFormEditModalOpen} = useContext(ModalContext)

	const { data, setData, errors, patch, processing } = useForm({
		id: 0,
		name: '',
		description: '',
		initial_balance: 0,
		decimal_precision: 2,
	});

	async function handleSubmit(e){
		e.preventDefault()
		if(id){
			await patch(`/account/${id}`, {
				data: data,
				onSuccess: () => setFormEditModalOpen(false),
				onError: (e) => console.error(e, data)
			})
		}else{
			await post(`/account`, {
				data: data,
				onSuccess: () => setFormEditModalOpen(false),
				onError: (e) => console.error(e, data)
			})
		}
	}

	useEffect(()=>{
		setModalProcessing(processing)
	}, [processing])

	useEffect(()=>{
		if(id){
			handleGetData(id).then(data => setData(data))
		}
	}, [])

	return (
		<form onSubmit={handleSubmit} id='form-account' className="w-full">
			{ (()=>{
				if(data.id){
					return (
						<>
							<div className="mb-5">
								<InputLabel htmlFor="name" value="Name" />
								<TextInput
									id="name"
									type="text"
									name="name"
									value={data.name}
									onChange={e => setData('name', e.target.value)}
									className="mt-1 w-full"
									isFocused
									placeholder="Name"
								/>
								<InputError message={errors.name} className="mt-2" />
							</div>
							<div className="mb-5">
								<InputLabel htmlFor="description" value="Description" />
								<TextInput
									id="description"
									type="text"
									name="description"
									value={data.description}
									onChange={e => setData('description', e.target.value)}
									className="mt-1 w-full"
									isFocused
									placeholder="Description"
								/>
								<InputError message={errors.description} className="mt-2" />
							</div>
							<div className="mb-5">
								<InputLabel htmlFor="initial_balance" value="Initial Balance" />
								<TextInput
									id="initial_balance"
									type="number"
									step="0.01"
									name="initial_balance"
									value={data.initial_balance}
									onChange={e => setData('initial_balance', e.target.value)}
									className="mt-1 w-full"
									isFocused
									placeholder="Initial Balance"
								/>
								<InputError message={errors.initial_balance} className="mt-2" />
							</div>
							<div>
								<InputLabel htmlFor="decimal_precision" value="Decimal Precision" />
								<TextInput
									id="decimal_precision"
									type="number"
									step="0.01"
									name="decimal_precision"
									value={data.decimal_precision}
									onChange={e => setData('decimal_precision', e.target.value)}
									className="mt-1 w-full"
									isFocused
									placeholder="Decimal Precision"
								/>
								<InputError message={errors.decimal_precision} className="mt-2" />
							</div>
						</>
					)
				}else{
					return (
						<>
							<div className="mb-5 rounded-md w-full">
								<div className="animate-pulse flex space-x-4">
									<div className="w-full">
										<div className="h-4 bg-gray-200 rounded w-1/4 my-2"></div>
										<div className="h-10 bg-gray-200 rounded"></div>
									</div>
								</div>
							</div>
							<div className="mb-5 rounded-md w-full">
								<div className="animate-pulse flex space-x-4">
									<div className="w-full">
										<div className="h-4 bg-gray-200 rounded w-1/4 my-2"></div>
										<div className="h-10 bg-gray-200 rounded"></div>
									</div>
								</div>
							</div>
							<div className="mb-5 rounded-md w-full">
								<div className="animate-pulse flex space-x-4">
									<div className="w-full">
										<div className="h-4 bg-gray-200 rounded w-1/4 my-2"></div>
										<div className="h-10 bg-gray-200 rounded"></div>
									</div>
								</div>
							</div>
							<div className="rounded-md w-full">
								<div className="animate-pulse flex space-x-4">
									<div className="w-full">
										<div className="h-4 bg-gray-200 rounded w-1/4 my-2"></div>
										<div className="h-10 bg-gray-200 rounded"></div>
									</div>
								</div>
							</div>
						</>
					)
				}
			})() }
		</form>
	)
}
