<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockDeductionDetail extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function stockDeduction()
    {
        return $this->belongsTo(StockDeduction::class);
    }

    public function category()
    {
        return $this->belongsTo(PurchaseCategory::class, 'category_id', 'id');
    }

    public function sub_category()
    {
        return $this->belongsTo(PurchaseSubCategory::class, 'sub_cat_id', 'id');
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
