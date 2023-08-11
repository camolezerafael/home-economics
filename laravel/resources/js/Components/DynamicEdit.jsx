import React from "react";
import AccountEdit from '@/Pages/Accounts/Edit.jsx'

const components = {
	account: AccountEdit,
};

export default function DynamicEdit( props ) {
	const FormEdit = components[props.form];
	return <FormEdit />;
}
