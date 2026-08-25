import { router } from '@inertiajs/react'

export const navigation = [
	{ name: 'Dashboard', href: 'dashboard', current: true },
	{ name: 'Transactions', href: 'transaction.index', current: false },
	{ name: 'Accounts', href: 'account.index', current: false },
	{ name: 'Account Types', href: 'account_type.index', current: false },
	{ name: 'Categories', href: 'category.index', current: false },
	{ name: 'From\'s & To\'s', href: 'from_to.index', current: false },
	{ name: 'Payment Methods', href: 'payment_type.index', current: false },
]

export function setCurrentNavigation(route){
	return navigation.map((el) => {
		el.current = el.href === route
		return el
	})
}

router.on('navigate', (event) => {
	setCurrentNavigation(event.detail.page.props?.viewAttributes?.homePage || event.detail.page.url.replace('\/', ''))
})
