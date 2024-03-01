<?php

	namespace App\Http\Controllers;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Foundation\Http\FormRequest;
	use Illuminate\Http\JsonResponse;
	use Illuminate\Http\RedirectResponse;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\Validator;
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

		protected function getColumns(): array
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

		protected function getAggregates(): array
		{
			return [];
		}

		protected function getDefaultOptionsModel( $model ): array
		{
			$options = [];
			$model::all()
				  ->where( 'user_id', Auth::id() )
				  ->each( static function ( $row ) use ( &$options ) {
					  $options[ $row->id ] = $row->name;
				  } );
			return $options;
		}

		/**
		 * Display a listing of the resource.
		 *
		 * @return Response
		 */
		public function index( Request $request ): Response
		{
			$props = [
				'viewAttributes' => fn () => $this->viewAttributes(),
				'items' => fn () => $this->modelClass::query()->paginate( 5 ),
			];

			return Inertia::render(
				$this->defaultList ? 'Default/Index' : $this->listPath,
				$props
			);
		}

		/**
		 * Show the form for creating a new resource.
		 *
		 * @return JsonResponse
		 */
		public function create(): JsonResponse
		{
			$viewAttributes = $this->viewAttributes();
			$aggregates     = $this->getAggregates();
			$item           = new $this->modelClass();
			$item->_token   = csrf_token();
			$item->_uri     = "/$this->defaultPath";
			return response()->json( compact( 'item', 'viewAttributes', 'aggregates' ) );
		}

		/**
		 * Store a newly created resource in storage.
		 *
		 * @param Request $request
		 * @return RedirectResponse
		 */
		public function store( Request $request ): RedirectResponse
		{
			$validator = Validator::make( $request->all(), ( new $this->formRequest )->rules() );
			if ( $validator->fails() ) {
				return back()->withErrors( $validator )->withInput();
			}
			$columns = $validator->validated();
			$this->beforeSave( $columns );
			$item = new $this->modelClass;
			$item->fill( $columns );
			$item->save();
			return back()->with( compact( 'item' ) );
		}

		/**
		 * Show the form for editing the specified resource.
		 *
		 * @param int $id
		 * @return JsonResponse
		 */
		public function edit( int $id ): JsonResponse
		{
			$viewAttributes = $this->viewAttributes();
			$aggregates     = $this->getAggregates();
			$item           = $this->modelClass::query()->findOrFail( $id );
			$item->_token   = csrf_token();
			$item->_method  = 'PATCH';
			$item->_uri     = "/$this->defaultPath/$item->id";
			return response()->json( compact( 'item', 'aggregates' ) );
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
			$item      = $this->modelClass::query()->findOrFail( $id );
			$validator = Validator::make( $request->all(), ( new $this->formRequest )->rules() );
			if ( $validator->fails() ) {
				return back()->withErrors( $validator )->withInput();
			}
			$columns = $validator->validated();
			$this->beforeSave( $columns );
			$item->update( $columns );
			return back()->with( compact( 'item' ) );
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
