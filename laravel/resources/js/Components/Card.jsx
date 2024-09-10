export default function Card( { className = '', ...props } ) {
	return (
		<div className="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none md:w-full lg:w-1/2 xl:w-1/3 2xl:mb-0 2xl:w-1/4">
			<div
				className="relative flex flex-col min-w-64 break-words bg-white shadow-xl dark:bg-slate-850 dark:shadow-dark-xl rounded-2xl bg-clip-border">
				<div className="flex-auto p-4">
					<div className="flex flex-row -mx-3 overflow-clip">
						<div className="flex-none grow px-2">
							<div>
								<p className="mb-0 font-sans font-semibold leading-normal uppercase text-sm">{ props.title ?? 'Month Incoming' }</p>
								{ props.children }
							</div>
						</div>
						<div className="px-2">
							<div
								className={ "flex items-center justify-center w-12 h-12 rounded-full " + props?.iconclasses }>
								{ props.icon }
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	)
}
