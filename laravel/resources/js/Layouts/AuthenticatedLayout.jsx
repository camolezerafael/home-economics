import { useState } from 'react';
import MenuTopLogo from '@/Components/MenuTopLogo.jsx'
import MenuUserName from '@/Components/MenuUserName.jsx'
import MenuItems from '@/Components/MenuItems.jsx'
import { Link } from '@inertiajs/react'


export default function Authenticated({ user, header, children }) {
    const [showingNavigationMenu, setShowingNavigationMenu] = useState(false);

    return (
        <div className="min-h-screen bg-gray-100">
			<nav
				className={'flex flex-col w-72 z-50 fixed top-0 bottom-0 transition-all duration-700 -translate-x-full md:translate-x-0 ' + (showingNavigationMenu && 'translate-x-0')}
				id="sidebar">

				<div className="-mr-2 items-center md:hidden absolute -right-8 top-2">
					<button
						onClick={() => setShowingNavigationMenu((previousState) => !previousState)}
						className="inline-flex items-center justify-center p-2 rounded-e-md text-gray-400 bg-gray-800 hover:text-gray-300 hover:bg-gray-700 focus:outline-none focus:bg-gray-700 focus:text-gray-300 transition duration-150 ease-in-out"
					>
						<svg className="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
							<path
								className=""
								strokeLinecap="round"
								strokeLinejoin="round"
								strokeWidth="2"
								d={showingNavigationMenu ? 'M6 18L18 6M6 6l12 12' : 'M4 6h16M4 12h16M4 18h16'}
							/>
						</svg>
					</button>
				</div>

				<aside className="bg-gray-800 text-white h-full">
					<MenuTopLogo />
					<MenuUserName user={user} />
					<MenuItems />
				</aside>
			</nav>

            {header && (
                <header className="flex bg-white shadow md:pl-72">
                    <div className="py-4 pl-14 pr-4 md:px-4 lg:px-8 flex w-full justify-between content-center">
						{header}
						<div className="flex flex-wrap text-sm content-center">
							<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.2} stroke="currentColor" className="w-5 h-5">
								<path strokeLinecap="round" strokeLinejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
							</svg>
							<Link href={route('logout')} method="post" as="button">Logout</Link>
						</div>
					</div>
                </header>
            )}

            <main className="flex flex-col grow-1 md:pl-72 m-2 md:m-4 lg:mx-6 lg:my-4">{children}</main>
        </div>
    );
}
