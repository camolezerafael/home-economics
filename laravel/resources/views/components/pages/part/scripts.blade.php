@props(['route'])

<script>
	const actionRoute = '/{{$route}}/'
	let idDelete = 0

	function callDelete(id){
		if(id > 0){
			idDelete = id
		}
	}

	function resetDelete(){
		idDelete = 0;
	}

	function requestDelete(){
		if(idDelete > 0){
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				type: 'DELETE',
				url: actionRoute + idDelete,
				dataType: 'json',
				success: function () {
					window.location.href = window.location.pathname + window.location.search + $('.nav-pills a.nav-link.active').attr('href');
					window.location.reload()
				},
				error: function () {
					alert('Não foi possével excluir')
				}
			});
		}
	}
</script>
