import { useEffect, useRef } from 'react'

const classNamesTabDefault = 'inline-flex items-center justify-center p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-800 hover:border-gray-800 hover:bg-gray-200 dark:hover:text-gray-800 group';
const classNamesTabActive = 'inline-flex items-center justify-center p-4 text-blue-600 border-b-2 border-blue-600 rounded-t-lg bg-white hover:bg-gray-200 active dark:text-blue-500 dark:border-blue-500 group';
const classNamesSvgDefault = 'w-4 h-4 me-2 text-gray-400 group-hover:text-gray-800 dark:text-gray-500 dark:group-hover:text-gray-800';
const classNamesSvgActive = 'w-4 h-4 me-2 text-blue-600 dark:text-blue-500';

export default function TabItem({ tabName, tabActive, setTabActive, icon, ...props }) {
	const isActive = tabActive === tabName;

    return (
		<li className="me-2 cursor-pointer">
			<div className={ isActive ? classNamesTabActive : classNamesTabDefault } onClick={ () => setTabActive(tabName) }>
				<svg
					className={ isActive ? classNamesSvgActive : classNamesSvgDefault }
					aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
					viewBox="0 0 20 20">
					{icon}
				</svg>
				{ tabName }
			</div>
		</li>
	);
}
