import Card from '@/Components/Card.jsx'
import ComparisonPercentage from '@/Components/Card/ComparisonPercentage.jsx'
import OverallEstimated from '@/Components/Card/OverallEstimated.jsx'
import Filters from '@/Components/Card/Filters.jsx'
import { iconBalance, iconExpenses, iconFilter, iconIncome } from '@/Components/Icons/Cards.jsx'

export default function Cards( { monthTotals, monthBalance, finalBalance, estimatedBalance, comboAccounts, comboPaid } ) {
	const formatter = new Intl.NumberFormat()

	return (
		<div className="flex flex-wrap -mx-3">
			<Card
				icon={ iconIncome }
				iconclasses="bg-green-600 text-green-100"
				title="Month Incoming"
			>
				<ComparisonPercentage
					percentage={ parseFloat( monthTotals.RECEI.PERC ).toFixed( 0 ) }
					labelone="Received"
					valueone={ formatter.format( monthTotals.RECEI.PAID ) }
					labeltwo="Estimated"
					valuetwo={ formatter.format( monthTotals.RECEI.TO_PAY ) }
					barclasses="bg-green-600 text-green-100"
				/>
			</Card>

			<Card
				icon={ iconExpenses }
				iconclasses="bg-red-600 text-red-100"
				title="Month Expenses"
			>
				<ComparisonPercentage
					percentage={ parseFloat( monthTotals.EXPEN.PERC ).toFixed( 0 ) }
					labelone="Received"
					valueone={ formatter.format( monthTotals.EXPEN.PAID ) }
					labeltwo="Estimated"
					valuetwo={ formatter.format( monthTotals.EXPEN.TO_PAY ) }
					barclasses="bg-red-600 text-red-100"
				/>
			</Card>

			<Card
				icon={ iconBalance }
				iconclasses="bg-orange-400 text-orange-100"
				title="Estimated Month Balance"
			>
				<OverallEstimated
					mainvalue={ formatter.format( monthBalance.month_balance ) }
					maincolor={ monthBalance.month_balance >= 0 ? 'text-green-600' : 'text-red-600' }
					labelone="Overall Balance"
					valueone={ formatter.format( finalBalance.final_balance ) }
					labeltwo="Estimated Balance"
					valuetwo={ formatter.format( estimatedBalance.final_balance ) }
					barclasses="bg-orange-400 text-orange-100"
				/>
			</Card>

			<Card
				icon={ iconFilter }
				iconclasses="bg-gray-400 text-gray-100"
				title="Filters"
			>
				<Filters
					mainvalue={ formatter.format( monthBalance.month_balance ) }
					maincolor={ monthBalance.month_balance >= 0 ? 'text-green-600' : 'text-red-600' }
					labelone="Overall Balance"
					valueone={ formatter.format( finalBalance.final_balance ) }
					labeltwo="Estimated Balance"
					valuetwo={ formatter.format( estimatedBalance.final_balance ) }
					barclasses="bg-orange-400 text-orange-100"
					comboAccounts={comboAccounts}
					comboPaid={comboPaid}
				/>
			</Card>
		</div>
	)
}
