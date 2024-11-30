import Card from '@/Components/Card.jsx'
import ComparisonPercentage from '@/Components/Card/ComparisonPercentage.jsx'
import OverallEstimated from '@/Components/Card/OverallEstimated.jsx'
import Filters from '@/Components/Card/Filters.jsx'
import {
	ArrowTrendingDownIcon, BanknotesIcon,
	DocumentCurrencyDollarIcon,
	FunnelIcon,
	ScaleIcon,
} from '@heroicons/react/16/solid/index.js'

export default function Cards( { monthTotals, monthBalance, finalBalance, estimatedBalance, comboAccounts, comboPaid, f_acc, f_date, f_pay } ) {
	const formatter = new Intl.NumberFormat()

	return (
		<div className="flex flex-wrap -mx-3">
			<Card
				icon={ BanknotesIcon }
				iconclasses="bg-green-600 text-white"
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
				icon={ DocumentCurrencyDollarIcon }
				iconclasses="bg-red-600 text-white"
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
				icon={ ScaleIcon }
				iconclasses="bg-orange-400 text-white"
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
				icon={ FunnelIcon }
				iconclasses="bg-gray-400 text-white"
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
					f_date={f_date}
					f_acc={f_acc}
					f_pay={f_pay}
				/>
			</Card>
		</div>
	)
}
