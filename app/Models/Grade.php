<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'level',
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