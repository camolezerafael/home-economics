import { Head } from '@inertiajs/react'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.jsx'
import { Chart } from 'primereact/chart'
import { useState, useEffect } from 'react'

export default function Dashboard( { auth, totals, graphBalance, graphIncoming, graphOutgoing, graphTransfers } ) {
	const [ chartData, setChartData ] = useState( null )

	useEffect( () => {
		if ( graphBalance && graphIncoming && graphOutgoing ) {
			setChartData( {
				labels: graphBalance.labels,
				datasets: [
					{
						label: 'Saldo',
						data: graphBalance.values,
						borderColor: 'rgb(59, 130, 246)',
						backgroundColor: 'rgba(59, 130, 246, 0.1)',
						fill: true,
						tension: 0.3,
					},
					{
						label: 'Receitas',
						data: graphIncoming.values,
						borderColor: 'rgb(16, 185, 129)',
						backgroundColor: 'rgba(16, 185, 129, 0.1)',
						fill: true,
						tension: 0.3,
					},
					{
						label: 'Despesas',
						data: graphOutgoing.values,
						borderColor: 'rgb(239, 68, 68)',
						backgroundColor: 'rgba(239, 68, 68, 0.1)',
						fill: true,
						tension: 0.3,
					},
					{
						label: 'Transferências',
						data: graphTransfers.values,
						borderColor: 'rgb(139, 92, 246)',
						backgroundColor: 'rgba(139, 92, 246, 0.1)',
						fill: true,
						tension: 0.3,
					},
				],
			} )
		}
	}, [ graphBalance, graphIncoming, graphOutgoing, graphTransfers ] )

	const formatCurrency = ( value ) => {
		return new Intl.NumberFormat( 'pt-BR', { style: 'currency', currency: 'BRL' } ).format( value )
	}

	return (
		<AuthenticatedLayout
			user={ auth.user }
			header={ <h2 className="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2> }
		>
			<Head title="Dashboard"/>

			<div className="p-4 space-y-6">
				<div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
					<div className="bg-white rounded-lg shadow-sm p-5 border border-gray-100">
						<div className="flex items-center justify-between">
							<div>
								<p className="text-sm text-gray-500">Saldo Final</p>
								<p className="text-2xl font-bold text-gray-800">{ formatCurrency( totals.balance ) }</p>
								<p className="text-sm text-green-600 mt-1">{ totals.balance_percentage } vs mês anterior</p>
							</div>
						</div>
					</div>

					<div className="bg-white rounded-lg shadow-sm p-5 border border-gray-100">
						<div className="flex items-center justify-between">
							<div>
								<p className="text-sm text-gray-500">Receitas do Mês</p>
								<p className="text-2xl font-bold text-green-600">{ formatCurrency( totals.incoming ) }</p>
								<p className="text-sm text-green-600 mt-1">{ totals.incoming_percentage } vs mês anterior</p>
							</div>
						</div>
					</div>

					<div className="bg-white rounded-lg shadow-sm p-5 border border-gray-100">
						<div className="flex items-center justify-between">
							<div>
								<p className="text-sm text-gray-500">Despesas Fixas</p>
								<p className="text-2xl font-bold text-red-600">{ formatCurrency( totals.fixed ) }</p>
								<p className="text-sm text-red-600 mt-1">{ totals.fixed_percentage } vs mês anterior</p>
							</div>
						</div>
					</div>

					<div className="bg-white rounded-lg shadow-sm p-5 border border-gray-100">
						<div className="flex items-center justify-between">
							<div>
								<p className="text-sm text-gray-500">Despesas Variáveis</p>
								<p className="text-2xl font-bold text-orange-600">{ formatCurrency( totals.variable ) }</p>
								<p className="text-sm text-orange-600 mt-1">{ totals.variable_percentage } vs mês anterior</p>
							</div>
						</div>
					</div>
				</div>

				<div className="bg-white rounded-lg shadow-sm p-5 border border-gray-100">
					<h3 className="text-lg font-medium text-gray-800 mb-4">Evolução dos Últimos 6 Meses</h3>
					<div className="h-80">
						{ chartData ? (
							<Chart type="line" data={ chartData } options={ {
								responsive: true,
								maintainAspectRatio: false,
								plugins: { legend: { position: 'top' } },
								scales: { y: { beginAtZero: false } },
							} } />
						) : (
							<div className="flex items-center justify-center h-full text-gray-400">Carregando gráfico...</div>
						) }
					</div>
				</div>
			</div>
		</AuthenticatedLayout>
	)
}