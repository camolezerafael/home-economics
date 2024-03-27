import { Head } from '@inertiajs/react'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.jsx'
import Cards from '@/Pages/Transactions/partials/Cards.jsx'

export default function Index( { auth, items, monthTotals, monthBalance, finalBalance, estimatedBalance, viewAttributes } ) {
	return (
		<AuthenticatedLayout
			user={ auth.user }
			header={ <h2
				className="font-semibold text-xl text-gray-800 leading-tight">{ viewAttributes.pluralItem }</h2> }
		>
			<Head title={ viewAttributes.pluralItem }/>
			<Cards
				monthTotals={monthTotals}
				monthBalance={monthBalance}
				finalBalance={finalBalance}
				estimatedBalance={estimatedBalance}
			/>
		</AuthenticatedLayout>
	)
}
