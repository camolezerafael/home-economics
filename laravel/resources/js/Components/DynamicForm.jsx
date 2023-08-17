import AccountForm from '@/Pages/Accounts/Form.jsx'

const components = {
	account: AccountForm,
};

export default function DynamicForm( props ) {
	const Form = components[props.form];
	return <Form {...props} />;
}
