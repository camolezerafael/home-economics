import AccountsForm from '@/Pages/Accounts/Form.jsx'
import AccountTypesForm from '@/Pages/AccountTypes/Form.jsx'
import CategoriesForm from '@/Pages/Categories/Form.jsx'
import FromTosForm from '@/Pages/FromTos/Form.jsx'
import PaymentTypesForm from '@/Pages/PaymentTypes/Form.jsx'
import TransactionForm from '@/Pages/Transactions/Form.jsx'

export const formComponents = {
	account: AccountsForm,
	account_type: AccountTypesForm,
	category: CategoriesForm,
	from_to: FromTosForm,
	payment_type: PaymentTypesForm,
	transaction: TransactionForm,
}
