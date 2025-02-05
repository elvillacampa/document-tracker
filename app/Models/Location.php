<?php
namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

 protected $fillable = ['document_id', 'location', 'receiver', 'timestamp'];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}

