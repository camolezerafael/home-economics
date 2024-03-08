import { router } from '@inertiajs/react'

export const navigation = [
	{ name: 'Dashboard', href: 'dashboard', current: true },
	{ name: 'Transactions', href: '', current: false },
	{ name: 'Accounts', href: 'accounts', current: false },
	{ name: 'Account Types', href: 'account_types', current: false },
	{ name: 'Categories', href: 'categories', current: false },
	{ name: 'From\'s & To\'s', href: 'from_tos', current: false },
	{ name: 'Payment Methods', href: 'payment_types', current: false },
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
