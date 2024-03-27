export default function Card( { className = '', ...props } ) {
	return (
		<div className="flex flex-wrap -mx-3">

			<div className="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
				<div
					className="relative flex flex-col min-w-0 break-words bg-white shadow-xl dark:bg-slate-850 dark:shadow-dark-xl rounded-2xl bg-clip-border">
					<div className="flex-auto p-4">
						<div className="flex flex-row -mx-3">
							<div className="flex-none w-2/3 max-w-full px-3">
								<div>
									<p className="mb-0 font-sans font-semibold leading-normal uppercase text-sm">{ props.title ?? 'Month Incoming' }</p>
									<div className="w-full bg-gray-200 rounded-full dark:bg-gray-700 mt-2 mb-2">
										<div
											className="bg-blue-600 text-xs font-medium text-blue-100 text-center p-0.5 leading-none rounded-full"
											style={{width: '45%'}}> 45%
										</div>
									</div>
									<div className="flex fw-bold flex-row">
										<div className="font-bold leading-normal text-sm flex-1">Received</div>
										<div className="mb-2 font-bold flex-grow">$53,000</div>
									</div>
								</div>
							</div>
							<div className="px-3 text-right basis-1/3">
								<div
									className="inline-block w-12 h-12 text-center rounded-circle bg-gradient-to-tl from-blue-500 to-violet-500">
									A
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	)
}
