import ApplicationLogo from '@/Components/ApplicationLogo.jsx'

export default function MenuTopLogo(props) {
    return (
		<div className="flex flex-col items-center py-3 md:py-1 border-b border-slate-500">
			<ApplicationLogo className="w-10 h-10 fill-white"/>
			{props?.appDescription ?? 'Home Economics'}
		</div>
    );
}
