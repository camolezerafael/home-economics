import Card from '@/Components/Card.jsx'
import ComparisonPercentage from '@/Components/Card/ComparisonPercentage.jsx'
import OverallEstimated from '@/Components/Card/OverallEstimated.jsx'
import Filters from '@/Components/Card/Filters.jsx'

const iconIncome = <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={ 2 }
						stroke="currentColor" className="w-6 h-6">
	<path strokeLinecap="round" strokeLinejoin="round"
		  d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941"/>
</svg>

const iconExpenses = <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={ 2 }
						  stroke="currentColor" className="w-6 h-6">
	<path strokeLinecap="round" strokeLinejoin="round"
		  d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m6.75 12H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
</svg>


const iconBalance = <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={ 2 }
						 stroke="currentColor" className="w-6 h-6">
	<path strokeLinecap="round" strokeLinejoin="round"
		  d="M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0 0 12 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-3-.52 2.62 10.726c.122.499-.106 1.028-.589 1.202a5.988 5.988 0 0 1-2.031.352 5.988 5.988 0 0 1-2.031-.352c-.483-.174-.711-.703-.59-1.202L18.75 4.971Zm-16.5.52c.99-.203 1.99-.377 3-.52m0 0 2.62 10.726c.122.499-.106 1.028-.589 1.202a5.989 5.989 0 0 1-2.031.352 5.989 5.989 0 0 1-2.031-.352c-.483-.174-.711-.703-.59-1.202L5.25 4.971Z"/>
</svg>

const iconFilter = <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 30 30" strokeWidth={ 2 }
						stroke="currentColor" className="w-6 h-6">
		<path strokeLinecap="round" strokeLinejoin="round"
			d="M19.199 28.262h-6.403v-10.398l-10.661-14.126h27.731l-10.667 14.126v10.398zM13.862 27.196h4.271v-9.688l9.592-12.703h-23.449l9.586 12.703v9.688z"></path>
</svg>

export default function Cards( { monthTotals, monthBalance, finalBalance, estimatedBalance } ) {
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
				/>
			</Card>
		</div>
	)
}
