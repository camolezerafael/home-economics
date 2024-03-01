export async function handleSubmit({ev, routePath, setFormModalOpen, patch, post, data}){
	ev.preventDefault()
	const options = {
		data: data,
		onSuccess: () => {
			setFormModalOpen( false )
		},
		onError: (e) => console.error(e, data)
	}

	if(data?.id){
		await patch(`/${routePath}/${data.id}`, options)
	}else{
		await post(`/${routePath}`, options)
	}
}
