export async function handleSubmit({ev, routePath, setFormModalOpen, patch, post, data, reloadData}){
	ev.preventDefault()
	const options = {
		data,
		onSuccess: () => {
			setFormModalOpen( false )
			reloadData()
		},
		onError: (e) => console.error(e, data)
	}

	if(data?.id){
		await patch(`/${routePath}/${data.id}`, options)
	}else{
		await post(`/${routePath}`, options)
	}
}
