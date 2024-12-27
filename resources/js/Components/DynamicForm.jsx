import { formComponents } from '@/Helpers/formComponents.js'

export default function DynamicForm( props ) {
	const Form = formComponents[props.form];
	return <Form {...props} />;
}
