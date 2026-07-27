<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'status'
    ];

    public function students()
    {
        return $this->hasMany(Students::class);
    }

    public function feeStructures()
    {
        return $this->hasMany(FeeStructure::class);
    }
}