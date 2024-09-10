export async function handleDelete( routePath, id ) {
	return await axios.delete( `/${ routePath }/${ id }` )
		.catch(function (error){
			if(error.response){
				return error.response.data
			}
			else if(error.request){
				return error.request.data
			}
			else {
				console.error(error)
			}
		})
}
