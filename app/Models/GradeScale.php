<?php
// app/Models/GradeScale.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradeScale extends Model
{
    use HasFactory;

    protected $fillable = [
        'grade',
        'min_score',
        'max_score',
        'remark',
        'color',
        'is_default',
        'order'
    ];

    protected $casts = [
        'min_score' => 'decimal:2',
        'max_score' => 'decimal:2',
        'is_default' => 'boolean'
    ];

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public static function getGrade($percentage)
    {
        $scale = self::where('min_score', '<=', $percentage)
            ->where('max_score', '>=', $percentage)
            ->first();

        return $scale ? $scale->grade : 'F';
    }

    public static function getRemark($percentage)
    {
        $scale = self::where('min_score', '<=', $percentage)
            ->where('max_score', '>=', $percentage)
            ->first();

        return $scale ? $scale->remark : 'Fail';
    }
}