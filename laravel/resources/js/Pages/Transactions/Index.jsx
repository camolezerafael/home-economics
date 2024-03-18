import { Head } from '@inertiajs/react'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.jsx'

export default function Index( { auth, items, viewAttributes } ) {
	return (
		<AuthenticatedLayout
			user={ auth.user }
			header={ <h2
				className="font-semibold text-xl text-gray-800 leading-tight">{ viewAttributes.pluralItem }</h2> }
		>
			<Head title={ viewAttributes.pluralItem }/>
		</AuthenticatedLayout>
	)
}
