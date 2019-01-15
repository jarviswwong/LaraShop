<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'parent_id', 'is_directory', 'level', 'path'];
    protected $casts = [
        'is_directory' => 'boolean',
    ];

    // 在创建分类前填充某些数据
    protected static function boot()
    {
        parent::boot();
        static::creating(function (Category $category) {
            if (is_null($category->parent_id)) {
                $category->level = 0;
                $category->path = '-';
            } else {
                $category->level = ($category->parent->level) + 1;
                $category->path = $category->parent->path . $category->parent->id . '-';
            }
        });
    }

    public function parent()
    {
        return $this->belongsTo(Category::class);
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // 访问器，访问所有祖先类目ID
    public function getPathIdsAttribute()
    {
        // array_filter 去除数组中的空值
        return array_filter(explode('-', trim($this->attributes['path'], '-')));
    }

    // 访问器 获取祖先分类并按照层级排序
    public function getAncestorsAttribute()
    {
        return Category::query()
            ->whereIn('id', $this->attributes['path_ids'])
            ->orderBy('level')
            ->get();
    }

    // 访问器 获取祖先分类名称并以 '-' 分隔
    public function getFullNameAttribute()
    {
        return $this->attributes['ancestors']
            ->pluck('name')
            ->push($this->name)
            ->implode(' - ');
    }
}
