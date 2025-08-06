<?php

namespace App\Http\Controllers\API;

use App\Services\Logger;
use App\Models\ProductUnit;
use Illuminate\Http\Request;
use App\Models\simpleFormLog;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

class ProductUnitController extends Controller
{
    protected $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function index()
    {
        return ProductUnit::where('user_id_maker', auth()->id())->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
                'unique:product_units,title,NULL,id',
            ],
            'unit_type' => ['required' , 'in:barcode,not_barcode'],
            'description' => ['nullable' , 'string'],
            'can_have_float_value' => ['required' , 'boolean'],
        ], [
            'title.unique' => 'repeated_unit_title',
            'title.required' => 'unit_title_required',
            'title.string' => 'repeated_unit_must_be_string',
            'title.max' => 'repeated_unit_max_char',
            'unit_type.required' => 'repeated_type_required',
            'unit_type.in' => 'repeated_type_not_in_range',
            'description.string' => 'description_must_be_string',
            'can_have_float_value.required' => 'can_have_float_value_required',
            'can_have_float_value.boolean' => 'can_have_float_value_boolean',
        ]);

        $lastUnit = ProductUnit::orderBy('id', 'desc')->first();
        $nextCode = $lastUnit ? str_pad((int)$lastUnit->coding + 1, 4, '0', STR_PAD_LEFT) : '0001';

        $validated['user_id_maker'] = auth()->id();
        $validated['coding'] = $nextCode;

        if($validated['unit_type'] == 'barcode')
        {
            $validated['can_have_float_value'] = false;
        }

        $productUnit = ProductUnit::create($validated);

        // Log creation
        $this->logger->logCreation($productUnit, $productUnit->only($productUnit->getLoggableFields()));


        return $this->generateResponse('success' , '201' , $productUnit->toArray());
    }

    public function show($productUnitId)
    {
        // Try to find the ProductUnit by ID
        $productUnit = ProductUnit::find($productUnitId);

        // Check if the ProductUnit exists
        if (!$productUnit) {
            return response()->json(['error' => 'Product Unit not found'], 404);
        }

        // Check if the authenticated user is authorized
        if ($productUnit->user_id_maker !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return $this->generateResponse('success' , '201' , $productUnit->toArray());
    }

    public function update(Request $request, $productUnitId)
    {
        $productUnit = ProductUnit::find($productUnitId);
        if (!$productUnit) {
            return response()->json(['error' => 'Product Unit not found'], 404);
        }

        if ($productUnit->user_id_maker !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('product_units')->ignore($productUnitId)
            ],
            'unit_type' => ['required', 'in:barcode,not_barcode'],
            'description' => ['nullable', 'string'],
            'can_have_float_value' => ['required', 'boolean'],
        ], [
            'title.required' => 'unit_title_required',
            'title.string' => 'repeated_unit_must_be_string',
            'title.max' => 'repeated_unit_max_char',
            'title.unique' => 'repeated_unit_title', // Custom error code for duplicates
            'unit_type.required' => 'repeated_type_required',
            'unit_type.in' => 'repeated_type_not_in_range',
            'description.string' => 'description_must_be_string',
            'can_have_float_value.required' => 'can_have_float_value_required',
            'can_have_float_value.boolean' => 'can_have_float_value_boolean',
        ]);

        $oldData = $productUnit->only($productUnit->getLoggableFields());
        $productUnit->update($validated);
        $this->logger->logUpdate($productUnit, $oldData, $productUnit->fresh()->only($productUnit->getLoggableFields()));

        return $this->generateResponse('success', '201', $productUnit->toArray());
    }

    public function logs($productUnitId)
    {
        $productUnit = ProductUnit::find($productUnitId);
        if (!$productUnit) {
            return response()->json(['error' => 'Product Unit not found'], 404);
        }
        if ($productUnit->user_id_maker !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $logs = simpleFormLog::where('loggable_type', ProductUnit::class)
            ->where('loggable_id', $productUnitId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->map(function ($log) use ($productUnit) {
                $fieldNames = $productUnit->getLoggableFieldNames();
                return [
                    'action' => $log->action,
                    'user_id' => $log->user_id,
                    'user_name' => $log->user ? $log->user->name : 'Unknown',
                    'timestamp' => $log->created_at->toDateTimeString(),
                    'changes' => $log->action === 'created'
                        ? array_map(
                            fn($key) => ['field' => $fieldNames[$key] ?? $key, 'value' => $log->new_data[$key]],
                            array_keys($log->new_data)
                        )
                        : array_map(
                            fn($key) => [
                                'field' => $fieldNames[$key] ?? $key,
                                'old' => $log->old_data[$key] ?? null,
                                'new' => $log->new_data[$key],
                            ],
                            array_keys($log->new_data)
                        ),
                ];
            });

        return $this->generateResponse('success', '200', $logs);
    }

    public function destroy($productUnitId)
    {
        $productUnit = ProductUnit::find($productUnitId);
        
        // 1. Check if exists
        if (!$productUnit) {
            return response()->json(['error' => 'Product unit not found'], 404);
        }
        
        // 2. Check ownership
        if ($productUnit->user_id_maker !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // 3. Delete the record
        $productUnit->delete();
        
        return $this->generateResponse('success', '201');
    }
}