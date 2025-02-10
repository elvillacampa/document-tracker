<?php
namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    public $timestamps = false;
    use HasFactory;

    protected $fillable = ['document_id', 'location', 'receiver', 'timestamp', 'created_by', 'updated_by','dispatcher','created_at','updated_at'];

    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater() {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}

