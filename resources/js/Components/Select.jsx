import { forwardRef, useEffect, useRef } from 'react'

export default forwardRef(function Select({
	className = '',
	isFocused = false,
	data = {},
	selected = 0,
	emptyOption,
	...props
}, ref) {
	const select = ref ? ref : useRef()

	useEffect(() => {
		if (isFocused) {
			select.current.focus()
		}
	}, [])

	return (
		<select
			{...props}
			className={
				'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm ' +
				className
			}
			ref={select}
			defaultValue={selected}
		>
			{emptyOption && <option value="" disabled>{emptyOption}</option>}
			{(() => {
				if (data) {
					return Object.keys(data).map((opt) => {
						const val = data[opt]
						let label
						if (val && typeof val === 'object') {
							label = val.label ?? val.name ?? String(opt)
						} else {
							label = String(val)
						}
						return (
							<option key={opt} value={opt}>{label}</option>
						)
					})
				} else {
					return <option key={0} value={0}>No Options...</option>
				}
			})()}
		</select>
	)
})
