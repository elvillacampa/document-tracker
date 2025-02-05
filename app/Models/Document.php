<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'drafter', 'category', 'file_path','purpose'];

    public function locations()
    {
        return $this->hasMany(Location::class);
    }
}

