<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ModelFilter
{
    public function filter(Model $model, array $filters, int $perPage = 20): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = $model::where('user_id_maker', auth()->id())->with('user');

        // Filter by user IDs
        if (!empty($filters['user_ids']) && is_array($filters['user_ids'])) {
            $query->whereIn('user_id_maker', $filters['user_ids']);
        }

        // Filter by date range
        if (!empty($filters['start_date'])) {
            $query->where('created_at', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $query->where('created_at', '<=', $filters['end_date']);
        }

        // Filter by search word
        if (!empty($filters['search']) && $model instanceof \App\Contracts\Filterable) {
            $searchableFields = $model->getSearchableFields();
            $query->where(function (Builder $q) use ($searchableFields, $filters) {
                foreach ($searchableFields as $field) {
                    $q->orWhere($field, 'like', '%' . $filters['search'] . '%');
                }
            });
        }

        return $query->orderBy('updated_at', 'desc')->paginate($perPage);
    }
}