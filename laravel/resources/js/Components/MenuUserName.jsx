function showInitials(name){
	const splitedName = name.split(' ')
	let result = ''

	if(splitedName.length >= 2){
		result = splitedName[0][0] + splitedName[1][0]
	}else{
		result = splitedName[0][0]
	}

	return result
}

export default function MenuUserName({user}) {
    return (
		<div className="flex flex-col items-center my-2 border-b border-slate-700">
			<div
				className="flex w-full flex-row items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white hover:text-gray-300 focus:outline-none transition ease-in-out duration-150"
			>
				<div className="rounded-full border-2 border-gray-600 w-12 h-12 flex justify-center items-center">
					{ user.profile ?
						<img className="w-12 h-12 contents" src={ user?.profile } alt={ user.name }/> :
						<span className="w-12 h-12 contents">{showInitials(user.name)}</span>
					}
				</div>
				<div className="ms-3 text-base">{ user.name }</div>
			</div>
		</div>
    );
}
