import PrimaryButton from '@/Components/PrimaryButton.jsx'
import { PencilSquareIcon, TrashIcon } from '@heroicons/react/16/solid/index.js'
import DangerButton from '@/Components/DangerButton.jsx'

export default function TableListDefaultAuto( { className = '', data, columns, setIdDataModal, setFormModalOpen, setTextDeleteModal, setFormDeleteModalOpen, ...props } ) {
	return (
		<table className="divide-y divide-gray-400 w-full">
			<thead className="bg-gray-200">
				<tr>
					{ columns.map( ( column, i ) => (
							<th key={ i } scope="col"
								className="px-4 py-3 text-md font-bold text-left rtl:text-right text-gray-500">
								{ column.name }
							</th>
						),
					) }
					<th scope="col"
						className="px-4 py-3 text-md font-bold text-left rtl:text-right text-gray-500 w-28">Actions
					</th>
				</tr>
			</thead>
			<tbody className="bg-white divide-y divide-gray-300">
				{ data.map( ( line, i ) => {
					return (
						<tr key={ i } className="hover:bg-gray-50">
							{ columns.map( ( col, j ) => {
								let classLine = 'text-left rtl:text-right'
								let value = line[ col.column ]

								if ( col?.cast && col.cast.includes( 'decimal' ) ) {
									value = new Intl.NumberFormat( undefined, {
										style: 'currency',
										currency: 'BRL',
									} ).format( value )
									classLine = 'text-right rtl:text-left'
								}

								return (
									<td key={ j } scope="col"
										className={ 'py-1 px-4 text-sm font-normal text-gray-500 ' + classLine }>
										{
											( () => {
												if ( typeof line[ col.column ] === 'object' ) {
													return (
														<>
															<h4 className="text-gray-700">{ value.name }</h4>
															<p className="text-gray-400">{ value.description }</p>
														</> )
												} else {
													return ( <>{ value }</> )
												}
											} )()
										}
									</td>
								)
							} ) }
							<td className="text-sm whitespace-nowrap">
								<div className="flex justify-evenly py-1.5">
									<PrimaryButton className="px-1.5 py-1 mx-1"
												   onClick={ () => {
													   setIdDataModal( line.id )
													   setFormModalOpen( true )
												   } }>
										<PencilSquareIcon className="w-4 h-4"/>
									</PrimaryButton>
									<DangerButton className="px-1.5 py-1 mx-1"
												  onClick={ () => {
													  setIdDataModal( line.id )
													  setTextDeleteModal( line.name )
													  setFormDeleteModalOpen( true )
												  } }>
										<TrashIcon className="w-4 h-4"/>
									</DangerButton>
								</div>
							</td>
						</tr>
					)
				} ) }
			</tbody>
		</table>
	)
}
