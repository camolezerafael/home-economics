<div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
	<div class="card">
		<div class="card-header p-3 pt-2">
			<div
				class="icon icon-lg icon-shape {{$class}} text-center border-radius-xl mt-n4 position-absolute">
				<i class="{{$icon}} opacity-10"></i>
			</div>
			<div class="text-end pt-1">
				<p class="text-sm mb-0 text-capitalize">{{$title}}</p>
				<h4 class="mb-0">@formatMoney($amount)</h4>
			</div>
		</div>
		<hr class="dark horizontal my-0">
		<div class="card-footer p-3">
			<p class="mb-0">
				@php
					$color = 'text-success';
                    $signal = '+';

                    if($percentage < 0.0)
                    {
						$signal = '';
                    }
                    elseif($percentage === 0.0)
                    {
						$color = '';
                        $signal = '~';
                    }

                    if( ( ($percentage < 0.0) && ($type === 'in') ) || ( ($percentage > 0.0) && ($type === 'out') ) )
                    {
                        $color = 'text-danger';
                    }
				@endphp
				<span class="{{$color}} text-sm font-weight-bolder">{{$signal}}@formatNumber($percentage)% </span>
				{{__('than last month')}}
			</p>
		</div>
	</div>
</div>
