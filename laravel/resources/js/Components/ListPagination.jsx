export default function ListPagination( props ) {
	const classNormal = 'relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-500 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0'
	const classHidden = 'relative hidden items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 md:inline-flex'
	const classCurrent = 'relative z-10 inline-flex items-center bg-gray-500 px-4 py-2 text-sm font-semibold text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-600'

	const indexLastLink = props.links.length - 1

	return (
		<div className="flex items-center justify-between border-t border-gray-200 bg-white px-2 pt-8 pb-3 sm:px-2">
			<div className="flex flex-1 justify-between sm:hidden">
				<a
					href={props.prev_page_url}
					className="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
				>
					{props.links[0].label}
				</a>
				<a
					href={props.last_page_url}
					className="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
				>
					{props.links[props.last_page + 1].label}
				</a>
			</div>
			<div className="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
				<div>
					<p className="text-sm text-gray-700">
						Showing <span className="font-medium">{props.from}</span> to <span
						className="font-medium">{props.to}</span> of{ ' ' }
						<span className="font-medium">{props.total}</span> results
					</p>
				</div>
				<div>
					<nav className="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
						{
							props.links.map((link, i) => {
								if(i === 0){
									return (
										<a
											key={i}
											href={link.url}
											className={ 'relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0' }
										>
											<span className="sr-only">{link.label}</span>
											<svg className="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
												<path fillRule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clipRule="evenodd" />
											</svg>
										</a>
									)
								}else if(i === indexLastLink){
									return (
										<a
											key={i}
											href={link.url}
											className={ 'relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0' }
										>
											<span className="sr-only">{link.label}</span>
											<svg className="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
												<path fillRule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clipRule="evenodd" />
											</svg>
										</a>
									)
								}else{
									return (
										<a
											key={i}
											href={link.url}
											aria-current="page"
											className={ link.active ? classCurrent : classNormal }
										>
											{link.label}
										</a>
									)
								}
							})
						}
					</nav>
				</div>
			</div>
		</div>
	)
}
