import { Head, router } from '@inertiajs/react'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.jsx'
import Cards from '@/Pages/Transactions/partials/Cards.jsx'
import { useState } from 'react'
import TabItem from '@/Components/TabItem.jsx'
import {
	tabIconFixedExpenses,
	tabIconIncome,
	tabIconPeople,
	tabIconTaxes,
	tabIconTransfers,
	tabIconVariableExpenses,
} from '@/Components/Icons/Tabs.jsx'
import TableListTransactions from '@/Components/TableListTransactions.jsx'

export default function Index( { auth, items, monthTotals, monthBalance, finalBalance, estimatedBalance, viewAttributes,  comboAccounts, comboPaid, f_date, f_acc, f_pay, ...props } ) {
	const [tabActive, setTabActive] = useState('Incoming');

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
				comboAccounts={comboAccounts}
				comboPaid={comboPaid}
				f_date={f_date}
				f_acc={f_acc}
				f_pay={f_pay}
			/>

			<>
				<div className="border-b border-gray-200 dark:border-gray-700">
					<ul className="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500 dark:text-gray-400">
						<TabItem tabName="Incoming" tabActive={tabActive} setTabActive={setTabActive} icon={tabIconIncome}/>
						<TabItem tabName="Fixed Expenses" tabActive={tabActive} setTabActive={setTabActive} icon={tabIconFixedExpenses}/>
						<TabItem tabName="Variable Expenses" tabActive={tabActive} setTabActive={setTabActive} icon={tabIconVariableExpenses}/>
						<TabItem tabName="People" tabActive={tabActive} setTabActive={setTabActive} icon={tabIconPeople}/>
						<TabItem tabName="Taxes" tabActive={tabActive} setTabActive={setTabActive} icon={tabIconTaxes}/>
						<TabItem tabName="Transfers" tabActive={tabActive} setTabActive={setTabActive} icon={tabIconTransfers}/>
					</ul>
				</div>

				<div className="border-2 border-gray-100 dark:border-gray-200">
					<TableListTransactions viewAttributes={viewAttributes} tabActive={tabActive} tabName="Incoming" data={items['RECEI'].data}/>
					<TableListTransactions viewAttributes={viewAttributes} tabActive={tabActive} tabName="Fixed Expenses" data={items['FIXEX'].data}/>
					<TableListTransactions viewAttributes={viewAttributes} tabActive={tabActive} tabName="Variable Expenses" data={items['VAREX'].data}/>
					<TableListTransactions viewAttributes={viewAttributes} tabActive={tabActive} tabName="People" data={items['PEOPL'].data}/>
					<TableListTransactions viewAttributes={viewAttributes} tabActive={tabActive} tabName="Taxes" data={items['TAXES'].data}/>
					<TableListTransactions viewAttributes={viewAttributes} tabActive={tabActive} tabName="Transfers" data={items['TRANS'].data}/>
				</div>
			</>
		</AuthenticatedLayout>
	)
}
