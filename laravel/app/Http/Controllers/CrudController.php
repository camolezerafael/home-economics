<?php

	namespace App\Http\Controllers;

	use Illuminate\Contracts\View\View;
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Foundation\Http\FormRequest;
	use Illuminate\Http\RedirectResponse;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Str;
	use Inertia\Inertia;
	use Inertia\Response;

	class CrudController extends Controller
	{
		protected string $defaultPath = '';
		protected string $basePage    = '';
		protected        $modelClass  = Model::class;
		protected        $formRequest = FormRequest::class;
		protected        $defaultList = true;
		protected        $listPath    = null;

		protected function viewAttributes(): array
		{
			return [
				'routePath'    => $this->defaultPath,
				'basePage'     => __( $this->basePage ),
				'viewPath'     => $this->defaultPath,
				'homePage'     => Str::of( $this->defaultPath )->headline()->pluralStudly()->snake()->toString(),
				'singularItem' => Str::of( __( $this->defaultPath ) )->headline()->singular()->toString(),
				'pluralItem'   => Str::of( __( $this->defaultPath ) )->headline()->pluralStudly()->toString(),
				'columns'      => $this->getColumns()
			];
		}

		protected function getColumns()
		{
			$viewColumns = $this->modelClass::keys();
			$columns     = [];

			foreach ($viewColumns as $column => $cast) {
				if ( is_string( $column ) ) {
					$name      = __( Str::of( $column )->remove( '_id' )->headline()->toString() );
					$columns[] = compact( 'column', 'name', 'cast' );
				} else {
					$name      = __( Str::of( $cast )->remove( '_id' )->headline()->toString() );
					$columns[] = [ 'column' => $cast, 'name' => $name ];
				}
			}

			return $columns;
		}

		/**
		 * Display a listing of the resource.
		 *
		 * @return Response
		 */
		public function index( Request $request ): Response
		{
			$viewAttributes = $this->viewAttributes();
			$items          = $this->modelClass::query()->paginate( 15 );

			return Inertia::render(
				$this->defaultList ? 'Default/Index' : $this->listPath,
				compact( 'items', 'viewAttributes' )
			);
		}

		/**
		 * Show the form for creating a new resource.
		 *
		 * @return View
		 */
		public function create(): View
		{
			$viewAttributes = $this->viewAttributes();
			$item           = new $this->modelClass();
			$item->_token   = csrf_token();
			$item->_uri     = "/$this->defaultPath";
			return view( "$this->defaultPath.edit", compact( 'item', 'viewAttributes' ) );
		}

		/**
		 * Store a newly created resource in storage.
		 *
		 * @param Request $request
		 * @return RedirectResponse
		 */
		public function store( Request $request ): RedirectResponse
		{
			$item    = new $this->modelClass;
			$columns = $request->validate( ( new $this->formRequest )->rules() );
			$this->beforeSave( $columns );
			$item->fill( $columns );
			$item->save();
			return redirect( "/$this->defaultPath/$item->id/edit" );
		}

		/**
		 * Show the form for editing the specified resource.
		 *
		 * @param int $id
		 * @return View
		 */
		public function edit( int $id ): View
		{
			$viewAttributes = $this->viewAttributes();
			$item           = $this->modelClass::query()->findOrFail( $id );
			$item->_token   = csrf_token();
			$item->_method  = 'PATCH';
			$item->_uri     = "/$this->defaultPath/$item->id";
			return view( "$this->defaultPath.edit", compact( 'item', 'viewAttributes' ) );
		}

		/**
		 * Update the specified resource in storage.
		 *
		 * @param int $id
		 * @param Request $request
		 * @return RedirectResponse
		 */
		public function update( int $id, Request $request ): RedirectResponse
		{
			$item    = $this->modelClass::query()->findOrFail( $id );
			$columns = $request->validate( ( new $this->formRequest )->rules() );
			$this->beforeSave( $columns );
			$item->update( $columns );
			return redirect( "/$this->defaultPath/$item->id/edit" );
		}

		/**
		 * Remove the specified resource from storage.
		 *
		 * @param int $id
		 * @return bool
		 */
		public function destroy( int $id ): bool
		{
			return $this->modelClass::destroy( $id );
		}

		public function beforeSave( array &$columns ): void
		{
			$columns[ 'user_id' ] = Auth::id();
		}

	}
