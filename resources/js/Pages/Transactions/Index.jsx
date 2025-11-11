import { Head, router } from '@inertiajs/react'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.jsx'
import Cards from '@/Pages/Transactions/Partials/Cards.jsx'
import { useState } from 'react'
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
import PrimaryButton from '@/Components/PrimaryButton.jsx'
import ModalForm from '@/Components/ModalForm.jsx'
import ModalFormContext from '@/Contexts/ModalFormContext.jsx'
import ListPaginationTransactions from '@/Components/ListPaginationTransactions.jsx'

const classNamesTabDefault = 'inline-flex items-center justify-center p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-800 hover:border-gray-800 hover:bg-gray-200 dark:hover:text-gray-800 group';
const classNamesTabActive = 'data-[selected]:text-blue-600 data-[selected]:border-b-2 data-[selected]:border-blue-600 data-[selected]:rounded-t-lg data-[selected]:bg-white data-[selected]:hover:bg-gray-200 data-[selected]:dark:text-blue-500 data-[selected]:dark:border-blue-500';
const classNamesSvgDefault = 'w-6 h-6 me-2';

export default function Index( { auth, items, monthTotals, monthBalance, finalBalance, estimatedBalance, viewAttributes,  comboAccounts, comboPaid, f_date, f_acc, f_pay, ...props } ) {
	const [ formModalOpen, setFormModalOpen ] = useState( false )
	const [ idDataModal, setIdDataModal ] = useState( null )

	const routePath = viewAttributes.routePath
	const formName = viewAttributes.viewPath
	const type = viewAttributes.singularItem

	const reloadData = () => {
		router.reload( { only: [ 'comboAccounts', 'items', 'monthTotals', 'monthBalance', 'finalBalance', 'estimatedBalance' ] } )
	}

	return (
		<AuthenticatedLayout
			user={ auth.user }
			header={ <h2
				className="font-semibold text-xl text-gray-800 leading-tight">{ viewAttributes.pluralItem }</h2> }
		>
			<Head title={ viewAttributes.pluralItem }/>

			<ModalFormContext.Provider value={ {
				auth,
				routePath,
				formModalOpen,
				setFormModalOpen,
				formName,
				type,
				idDataModal,
				reloadData
			} }>
				<ModalForm />
			</ModalFormContext.Provider>

			<div className="p-4 bg-white rounded-md block">
				<div className="my-2 w-full text-right">
					<PrimaryButton className="py-2 px-3" onClick={() => {
						setIdDataModal( null )
						setFormModalOpen( true )
					} }>New {type}</PrimaryButton>
				</div>
			</div>

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
					<TabPanel>
						<TableListTransactions data={items['RECEI'].data} setIdDataModal={setIdDataModal} setFormModalOpen={setFormModalOpen} />
						<ListPaginationTransactions page={items['RECEI']} />
					</TabPanel>
					<TabPanel>
						<TableListTransactions data={items['FIXEX'].data} setIdDataModal={setIdDataModal} setFormModalOpen={setFormModalOpen} />
						<ListPaginationTransactions page={items['FIXEX']} />
					</TabPanel>
					<TabPanel>
						<TableListTransactions data={items['VAREX'].data} setIdDataModal={setIdDataModal} setFormModalOpen={setFormModalOpen} />
						<ListPaginationTransactions page={items['VAREX']} />
					</TabPanel>
					<TabPanel>
						<TableListTransactions data={items['PEOPL'].data} setIdDataModal={setIdDataModal} setFormModalOpen={setFormModalOpen} />
						<ListPaginationTransactions page={items['PEOPL']} />
					</TabPanel>
					<TabPanel>
						<TableListTransactions data={items['TAXES'].data} setIdDataModal={setIdDataModal} setFormModalOpen={setFormModalOpen} />
						<ListPaginationTransactions page={items['TAXES']} />
					</TabPanel>
					<TabPanel>
						<TableListTransactions data={items['TRANS'].data} setIdDataModal={setIdDataModal} setFormModalOpen={setFormModalOpen} type='transferences' />
						<ListPaginationTransactions page={items['TRANS']} />
					</TabPanel>
				</TabPanels>
			</TabGroup>
		</AuthenticatedLayout>
	)
}
