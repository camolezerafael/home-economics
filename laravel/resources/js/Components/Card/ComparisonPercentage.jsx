export default function ComparisonPercentage( { className = '', ...props } ) {
	return (
		<>
			<div className="w-full bg-gray-200 rounded-full dark:bg-gray-700 mt-2 mb-2">
				<div
					className={ 'text-xs font-medium text-center p-0.5 leading-none rounded-full transition-all duration-500 ' + props?.barclasses }
					style={ { width: (Math.max(props.percentage, 8 )) + '%' } }>
					<span className='text-nowrap'>
						{ props.percentage }%
					</span>
				</div>
			</div>
			<div className="flex flex-row justify-between font-bold pt-2">
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
