import ResponsiveNavLink from '@/Components/ResponsiveNavLink.jsx'
import { navigation } from '@/Helpers/handleMenuItem.js'
import { useEffect } from 'react'

function classNames( ...classes ) {
	return classes.filter( Boolean ).join( ' ' )
}

export default function MenuItems() {
	useEffect( () => {
	}, [navigation] )

    return (
		<div className="divide-y divide-slate-700">
			{
				navigation.map( ( item, i ) => (
					<ResponsiveNavLink
						key={ i }
						href={ item.href && route(item.href) }
						active={item.current}
					>
						{ item.name }
					</ResponsiveNavLink>
				) )
			}
		</div>
    );
}
