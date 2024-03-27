export default function OverallEstimated( { className = '', ...props } ) {
	return (
		<>
			<div className="w-full mt-2 mb-2">
				<div className={ 'text-md text-center leading-none font-bold pt-1 ' + (props.mainvalue > 0 ? 'text-green-600' : 'text-red-600' ) }>
					{ props.mainvalue }
				</div>
			</div>
			<div className="flex flex-row justify-between font-bold pt-1">
				<div className="leading-normal text-xs">{ props.labelone }</div>
				<div className="text-sm">{ props.valueone }</div>
			</div>
			<div className="flex flex-row justify-between">
				<div className="leading-normal text-xs">{ props.labeltwo }</div>
				<div className="text-sm">{ props.valuetwo }</div>
			</div>
		</>
	)
}
