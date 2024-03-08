import AccountsForm from '@/Pages/Accounts/Form.jsx'
import AccountTypesForm from '@/Pages/AccountTypes/Form.jsx'

const components = {
	account: AccountsForm,
	account_type: AccountTypesForm,
};

export default function DynamicForm( props ) {
	const Form = components[props.form];
	return <Form {...props} />;
}
