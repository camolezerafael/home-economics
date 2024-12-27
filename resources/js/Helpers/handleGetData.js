export async function handleGetData(routePath, id = null){
	let response

	if(id){
		response = await axios.get(`/${routePath}/${id}/edit`)
	}else{
		response = await axios.get(`/${routePath}/create`)
	}

	return response?.data
}
