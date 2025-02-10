<?php
namespace App\Models;
use Carbon\Carbon;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;

    public function getLastActivityAttribute($value)
    {
        if (!$value) {
            return null;
        }
        
        $date = Carbon::createFromTimestamp($value, 'Asia/Manila');
        $ip   = $this->attributes['ip_address'] ?? 'Unknown';
        
        // Return as an object (you could also return an array if you prefer)
        return (object)[
            'date'       => $date, // This is a Carbon instance; you can format it as needed
            'ip_address' => $ip,
        ];
    }

    // If needed, include your user relationship here as well
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }
}

