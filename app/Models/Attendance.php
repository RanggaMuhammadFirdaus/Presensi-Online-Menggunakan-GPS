<?php

namespace App\Models;

use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\URL;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'start_time',
        'batas_start_time',        
        'code',
        'limit'
    ];

    protected $appends = ['data'];

    protected function data(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                $now = now();
                $startTime = Carbon::parse($this->start_time);
                $batasStartTime = Carbon::parse($this->batas_start_time);

                

                

                return (object) [
                    "start_time" => $this->start_time,
                    "batas_start_time" => $this->batas_start_time,
                    "now" => $now->format("H:i:s"),
                    "is_start" => $startTime <= $now && $batasStartTime >= $now,                
                    'is_using_qrcode' => $this->code ? true : false,                    
                ];
            },
        );
    }

    public function scopeForCurrentUser($query, $userPositionId)
    {
        $query->whereHas('positions', function ($query) use ($userPositionId) {
            $query->where('position_id', $userPositionId);
        });
    }

    public function positions()
    {
        return $this->belongsToMany(Position::class);
    }

        public function presences()
        {
            return $this->hasMany(Presence::class);
        }
}
