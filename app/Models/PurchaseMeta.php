<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseMeta extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(PurchaseCategory::class, 'category_id', 'id');
    }

    public function subCategory()
    {
        return $this->belongsTo(PurchaseSubCategory::class, 'sub_cat_id', 'id');
    }
}
