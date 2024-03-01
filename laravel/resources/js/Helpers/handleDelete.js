export async function handleDelete( routePath, id ) {
	return await axios.delete( `/${ routePath }/${ id }` )
}
