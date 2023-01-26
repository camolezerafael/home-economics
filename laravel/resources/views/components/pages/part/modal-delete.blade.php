@props(['route'])

<div class="modal fade" id="modal-delete" tabindex="-1" role="dialog" aria-labelledby="modal-delete"
	 aria-hidden="true">
	<div class="modal-dialog modal-sm modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h6 class="modal-title font-weight-normal" id="modal-title-delete">{{__('Confirm Delete?')}}</h6>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="text-center">
					<i class="fas fa-trash-alt" style="font-size: 4rem;"></i>
					<h5 class="text-gradient text-danger mt-4">{{__('You really want to delete this item?')}}</h5>
					<p>{{__('This action cannot be reversed!')}}</p>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" onclick="requestDelete()">{{__('Yes, delete!')}}</button>
				<button type="button" class="btn btn-secondary text-white" data-bs-dismiss="modal"  onclick="resetDelete()">
					{{__('Cancel')}}
				</button>
			</div>
		</div>
	</div>
</div>

<x-pages.part.scripts :$route />
