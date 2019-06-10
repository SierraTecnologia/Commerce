<?php

namespace Sitec\Commerce\Models;

use Sitec\Cms\Models\CmsModel;
use Sitec\Commerce\Services\ProductService;

class Variant extends CmsModel
{
    public $table = 'product_variants';

    public $primaryKey = 'id';

    public $timestamps = true;

    public $fillable = [
        'product_id',
        'key',
        'value',
    ];

    public static $rules = [];

    public function getOptionsAttribute()
    {
        return app(ProductService::class)->variantOptions($this);
    }

    public function rawValue($value)
    {
        $valueWithoutParenthesis = preg_replace("/\([^)]+\)/","", $value);
        $valueWithoutSquareParenthesis = preg_replace("/\[[^)]+\]/","", $valueWithoutParenthesis);

        return ucfirst($valueWithoutSquareParenthesis);
    }
}
