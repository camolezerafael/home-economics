import InputLabel from '@/Components/InputLabel.jsx'
import TextInput from '@/Components/TextInput.jsx'
import InputError from '@/Components/InputError.jsx'
import { useForm } from '@inertiajs/react'
import { useContext, useEffect, useState } from 'react'
import ModalContext from '@/Contexts/ModalContext.jsx'
import Select from '@/Components/Select.jsx'

async function handleGetData(id = null){
	let response = null

	if(id){
		response = await axios.get(`/account/${id}/edit`)
	}else{
		response = await axios.get(`/account/create`)
	}

	return response?.data
}

export default function Form( { id = null } ) {
	const {setModalProcessing, setFormModalOpen} = useContext(ModalContext)

	const [aggregates, setAggregates] = useState([])

	const { data, setData, errors, patch, post, processing } = useForm({
		id: 0,
		name: '',
		description: '',
		initial_balance: 0,
		decimal_precision: 2,
		type_id: 0
	});

	async function handleSubmit(e){
		e.preventDefault()
		const options = {
			data: data,
			onSuccess: () => {
				setFormModalOpen( false )
			},
			onError: (e) => console.error(e, data)
		}

		if(id){
			await patch(`/account/${id}`, options)
		}else{
			await post(`/account`, options)
		}
	}

	function setDatas(data){
		setData( data.item )
		setAggregates(data.aggregates)
	}

	useEffect(()=>{
		setModalProcessing(processing)
	}, [processing])

	useEffect(()=>{
		if(id){
			handleGetData(id).then(data => setDatas(data))
		}else{
			handleGetData().then(data => setDatas(data))
		}
	}, [])

	return (
		<form onSubmit={handleSubmit} id='form-account' className="w-full">
			{ (()=>{
				if(!id || data.id){
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
									placeholder="Decimal Precision"
								/>
								<InputError message={errors.decimal_precision} className="mt-2" />
							</div>

							<div>
								<InputLabel htmlFor="type_id" value="Account Type" />
								<Select
									id="type_id"
									name="type_id"
									onChange={e => setData('type_id', e.target.value)}
									className="mt-1 w-full"
									data={aggregates?.account_type}
									selected={data?.type_id}
								/>
								<InputError message={errors.type_id} className="mt-2" />
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
