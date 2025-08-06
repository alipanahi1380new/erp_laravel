<?php

namespace App\Models;

use App\Contracts\Loggable;
use App\Traits\LogsChanges;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductUnit extends Model implements Loggable
{
    /** @use HasFactory<\Database\Factories\ProductUnitFactory> */
    use HasFactory, SoftDeletes, LogsChanges;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'title',
        'coding' ,
        'unit_type',
        'user_id_maker',
        'description',
        'can_have_float_value',
    ];

    public function getLoggableFields(): array
    {
        return ['title', 'unit_type', 'description', 'can_have_float_value'];
    }

    public function getLoggableFieldNames(): array
    {
        return [
            'title' => 'Unit Name',
            'unit_type' => 'Unit Type',
            'description' => 'Description',
            'can_have_float_value' => 'Allows Decimal Values',
        ];
    }

}
