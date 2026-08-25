<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

abstract class CrudController extends Controller
{
    protected string $defaultPath = '';

    protected string $basePage = '';

    protected string $modelClass = '';

    protected string $formRequest = '';

    public function index(Request $request): Response
    {
        $query = $this->modelClass::query()->where('user_id', Auth::id());

        if (method_exists($this->modelClass, 'scopeOrdered')) {
            $query->ordered();
        }

        $items = $query->paginate(10);

        return Inertia::render('Default/Index', [
            'items' => $items,
            'viewAttributes' => $this->viewAttributes(),
            'aggregates' => $this->getAggregates(),
        ]);
    }

    public function create(): JsonResponse
    {
        $item = new $this->modelClass;
        $item->user_id = Auth::id();

        return response()->json([
            'item' => $item,
            'viewAttributes' => $this->viewAttributes(),
            'aggregates' => $this->getAggregates(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->getValidatedData($request);

        $this->beforeSave($validated);

        $item = new $this->modelClass;
        $item->fill($validated);
        $item->save();

        return redirect()->route($this->defaultPath.'.show', $item)
            ->with('status', $this->defaultPath.'-created');
    }

    public function edit(int $id): JsonResponse
    {
        $item = $this->modelClass::where('user_id', Auth::id())->findOrFail($id);

        return response()->json([
            'item' => $item,
            'viewAttributes' => $this->viewAttributes(),
            'aggregates' => $this->getAggregates(),
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $item = $this->modelClass::where('user_id', Auth::id())->findOrFail($id);

        $validated = $this->getValidatedData($request);

        $this->beforeSave($validated);

        $item->update($validated);

        return back()->with('status', $this->defaultPath.'-updated');
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $item = $this->modelClass::where('user_id', Auth::id())->findOrFail($id);
            $item->delete();

            return response()->json(['success' => true]);
        } catch (QueryException $e) {
            if (
                ($e->getCode() === '23000' && str_contains($e->getMessage(), '1451'))
                || str_contains($e->getMessage(), 'FOREIGN KEY constraint failed')
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'Existem itens registrados usando esta conta. Esta informação não pode ser deletada.',
                ], 409);
            }

            return response()->json([
                'success' => false,
                'message' => 'Erro ao deletar registro.',
            ], 500);
        }
    }

    protected function getValidatedData(Request $request): array
    {
        return $request->validate(app($this->formRequest)->rules());
    }

    protected function beforeSave(array &$columns): void
    {
        $columns['user_id'] = Auth::id();
    }

    protected function viewAttributes(): array
    {
        return [
            'routePath' => $this->defaultPath,
            'basePage' => $this->basePage,
            'viewPath' => $this->defaultPath,
            'homePage' => $this->defaultPath.'s',
            'singularItem' => class_basename($this->modelClass),
            'pluralItem' => class_basename($this->modelClass).'s',
            'columns' => $this->getColumns(),
        ];
    }

    protected function getColumns(): array
    {
        if (method_exists($this->modelClass, 'keys')) {
            return call_user_func([$this->modelClass, 'keys']);
        }

        return [];
    }

    protected function getAggregates(): array
    {
        return [];
    }

    protected function getDefaultOptionsModel(string $modelClass): array
    {
        return $modelClass::query()
            ->where('user_id', Auth::id())
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($item) => [
                'id' => $item->id,
                'name' => $item->name,
            ])
            ->values()
            ->toArray();
    }
}
