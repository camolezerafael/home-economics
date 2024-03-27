export default function Card( { className = '', ...props } ) {
	return (
		<div className="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
			<div
				className="relative flex flex-col min-w-0 break-words bg-white shadow-xl dark:bg-slate-850 dark:shadow-dark-xl rounded-2xl bg-clip-border">
				<div className="flex-auto p-4">
					<div className="flex flex-row -mx-3">
						<div className="flex-none grow px-2">
							<div>
								<p className="mb-0 font-sans font-semibold leading-normal uppercase text-sm">{ props.title ?? 'Month Incoming' }</p>
								{ props.children }
							</div>
						</div>
						<div className="px-2 text-center">
							<div
								className={ "content-center justify-items-center w-12 h-12 rounded-circle " + props?.iconclasses }>
								{ props.icon }
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	)
}
