import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.jsx'
import { Head } from '@inertiajs/react'

export default function Index({ auth, list }) {
	return (
		<AuthenticatedLayout
			user={auth.user}
			header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Accounts</h2>}
		>
			<Head title="Accounts" />

			<div className="py-12">
				<div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
					<div className="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
						aaa
					</div>
				</div>
			</div>
		</AuthenticatedLayout>
	);
}
