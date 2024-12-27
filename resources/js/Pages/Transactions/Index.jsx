import { Head } from '@inertiajs/react'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.jsx'
import Cards from '@/Pages/Transactions/partials/Cards.jsx'
import { Fragment, useState } from 'react'
import TabItem from '@/Components/TabItem.jsx'
import TableListTransactions from '@/Components/TableListTransactions.jsx'
import {
	ArrowsRightLeftIcon,
	ArrowTrendingUpIcon,
	CreditCardIcon,
	DocumentTextIcon,
	ReceiptPercentIcon,
	UsersIcon,
} from '@heroicons/react/16/solid/index.js'
import { Tab, TabGroup, TabList, TabPanel, TabPanels } from '@headlessui/react'

const classNamesTabDefault = 'inline-flex items-center justify-center p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-800 hover:border-gray-800 hover:bg-gray-200 dark:hover:text-gray-800 group';
const classNamesTabActive = 'data-[selected]:text-blue-600 data-[selected]:border-b-2 data-[selected]:border-blue-600 data-[selected]:rounded-t-lg data-[selected]:bg-white data-[selected]:hover:bg-gray-200 data-[selected]:dark:text-blue-500 data-[selected]:dark:border-blue-500';
const classNamesSvgDefault = 'w-6 h-6 me-2';

export default function Index( { auth, items, monthTotals, monthBalance, finalBalance, estimatedBalance, viewAttributes,  comboAccounts, comboPaid, f_date, f_acc, f_pay, ...props } ) {
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

			<TabGroup className="border-b border-gray-200 dark:border-gray-700">
				<TabList className="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500 dark:text-gray-400">
					<Tab className={ classNamesTabDefault + classNamesTabActive }>
						<ArrowTrendingUpIcon className={ classNamesSvgDefault }/>
						Incoming
					</Tab>
					<Tab className={ classNamesTabDefault + classNamesTabActive }>
						<DocumentTextIcon className={ classNamesSvgDefault }/>
						Fixed Expenses
					</Tab>
					<Tab className={ classNamesTabDefault + classNamesTabActive }>
						<CreditCardIcon className={ classNamesSvgDefault }/>
						Variable Expenses
					</Tab>
					<Tab className={ classNamesTabDefault + classNamesTabActive }>
						<UsersIcon className={ classNamesSvgDefault }/>
						People
					</Tab>
					<Tab className={ classNamesTabDefault + classNamesTabActive}>
						<ReceiptPercentIcon className={ classNamesSvgDefault }/>
						Taxes
					</Tab>
					<Tab className={classNamesTabDefault + classNamesTabActive}>
						<ArrowsRightLeftIcon className={ classNamesSvgDefault }/>
						Transfers
					</Tab>
				</TabList>
				<TabPanels className="border-2 border-gray-100 dark:border-gray-200">
					<TabPanel as={Fragment}>
						<TableListTransactions data={items['RECEI'].data}/>
					</TabPanel>
					<TabPanel as={Fragment}>
						<TableListTransactions data={items['FIXEX'].data}/>
					</TabPanel>
					<TabPanel as={Fragment}>
						<TableListTransactions data={items['VAREX'].data}/>
					</TabPanel>
					<TabPanel as={Fragment}>
						<TableListTransactions data={items['PEOPL'].data}/>
					</TabPanel>
					<TabPanel as={Fragment}>
						<TableListTransactions data={items['TAXES'].data}/>
					</TabPanel>
					<TabPanel as={Fragment}>
						<TableListTransactions data={items['TRANS'].data}/>
					</TabPanel>
				</TabPanels>
			</TabGroup>
		</AuthenticatedLayout>
	)
}
