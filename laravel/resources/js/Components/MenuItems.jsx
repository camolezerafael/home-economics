import { Link } from '@inertiajs/react'
import NavLink from '@/Components/NavLink.jsx'
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.jsx'

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
					<ResponsiveNavLink
						key={ i }
						href={ route(item.href) }
						active={item.current}
					>
						{ item.name }
					</ResponsiveNavLink>
				) )
			}
		</div>
    );
}
