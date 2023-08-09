import { Link } from '@inertiajs/react'

const navigation = [
	{ name: 'Dashboard', href: 'dashboard', current: true },
	{ name: 'Transactions', href: 'dashboard', current: false },
	{ name: 'Accounts', href: 'accounts', current: false },
	{ name: 'Account Types', href: 'dashboard', current: false },
	{ name: 'Categories', href: 'dashboard', current: false },
	{ name: 'From\'s & To\'s', href: 'dashboard', current: false },
	{ name: 'Payment Methods', href: 'dashboard', current: false },
]

function classNames( ...classes ) {
	return classes.filter( Boolean ).join( ' ' )
}

export default function MenuItems(props) {
    return (
		<div className="divide-y divide-slate-700">
			{
				navigation.map( ( item, i ) => (
					<Link
						key={ i }
						href={ route(item.href) }
						className={ classNames(
							item.current
								? 'bg-gray-900 text-white'
								: 'text-gray-300 hover:bg-gray-700 hover:text-white',
							'rounded-md px-4 py-2 text-sm font-medium w-100 block',
						) }
						aria-current={ item.current ? 'page' : undefined }
					>
						{ item.name }
					</Link>
				) )
			}
		</div>
    );
}
