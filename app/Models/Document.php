<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    public $timestamps = false;
    use HasFactory;

    protected $fillable = ['name', 'category','file_path', 'purpose', 'drafter', 'created_by', 'updated_by','date_rcvd_sent','created_at','updated_at'];

    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater() {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function locations()
    {
        return $this->hasMany(Location::class);
    }
}

