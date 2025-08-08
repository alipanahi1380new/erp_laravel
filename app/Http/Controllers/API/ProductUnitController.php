<?php

namespace App\Http\Controllers\API;

use App\Services\Logger;
use App\Models\ProductUnit;
use App\Services\ModelFilter;
use App\Traits\FiltersModels;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductUnitRequest;

class ProductUnitController extends Controller
{
    use FiltersModels;
    protected $logger;
    protected $modelFilter;
    protected $modelClass = ProductUnit::class;

    public function __construct(Logger $logger , ModelFilter $modelFilter)
    {
        $this->logger = $logger;
        $this->modelFilter = $modelFilter;
    }

    public function saveProductUnit(array $params)
    {
        $returnArray = ['message' => 'success'];

        $request = $params['request']; // ProductUnitRequest instance
        $productUnit = $params['productUnit'] ?? null;

        $isUpdate = ($productUnit !== null);

        $validated = $request->validated();

        // Force can_have_float_value to false if unit_type is barcode
        if ($validated['unit_type'] === 'barcode') {
            $validated['can_have_float_value'] = false;
        }

        if ($isUpdate) {
            $oldData = $productUnit->only($productUnit->getLoggableFields());
            try{
                $productUnit->update($validated);
            }
            catch (\Exception $e) {
                return $this->generateResponse(
                    [
                        'returnArray' => $returnArray ,
                        'message' => 'update_database_error' ,
                        'errors' => ['ProductUnit' , $e->getMessage()] ,
                        'statusCode' => 500
                    ]
                );
            }

            $logUpdate = $this->logger->logUpdate($productUnit, $oldData, $productUnit->fresh()->only($productUnit->getLoggableFields()));

            if ($logUpdate['message'] !== 'success') {
                return $this->transferResponse($returnArray , $logUpdate);
            }

        } else {
            $lastUnit = ProductUnit::orderBy('id', 'desc')->first();
            $nextCode = $lastUnit ? str_pad((int)$lastUnit->coding + 1, 4, '0', STR_PAD_LEFT) : '0001';

            $validated['user_id_maker'] = auth()->id();
            $validated['coding'] = $nextCode;

            try {
                $productUnit = ProductUnit::create($validated);
            } catch (\Exception $e) {
                return $this->generateResponse(
                    [
                        'returnArray' => $returnArray ,
                        'message' => 'insert_database_error' ,
                        'errors' => ['ProductUnit' , $e->getMessage()] ,
                        'statusCode' => 500
                    ]
                );
            }

            $logCreation = $this->logger->logCreation($productUnit, $productUnit->only($productUnit->getLoggableFields()));
            if ($logCreation['message'] !== 'success') {
                return $this->transferResponse($returnArray , $logCreation);
            }

        }

        return $this->transferResponse($returnArray , ['data' => $productUnit->toArray()]);
    }

    public function store(ProductUnitRequest $request)
    {
        return $this->saveProductUnit(
            [
                'request' => $request
            ]
        );
    }

    public function update(ProductUnitRequest $request, ProductUnit $productUnit)
    {
        return $this->saveProductUnit(
            [
                'request' => $request ,
                'productUnit' => $productUnit
            ]
        );
    }

    public function show(ProductUnit $productUnit)
    {
        return $this->generateResponse(
            [
                'message' => 'success' ,
                'data' => $productUnit->toArray() ,
                'statusCode' => 201
            ]
        );
    }

    public function destroy(ProductUnit $productUnit)
    {
        try{
            $productUnit->delete();
        }
        catch (\Exception $e) {
            return $this->generateResponse(
                [
                    'message' => 'delete_database_error' ,
                    'errors' => ['ProductUnit' , $e->getMessage()] ,
                    'statusCode' => 500
                ]
            );
        }

        return $this->generateResponse(
            [
                'message' => 'success' ,
                'statusCode' => 201
            ]
        );
    }

}