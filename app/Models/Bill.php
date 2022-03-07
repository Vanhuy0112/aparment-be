<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $table = 'bills';
    protected $fillable = [
        'name',
        'amount',
        'status',
        'type_payment',
        'payment_method',
        'image',
        'fax',
        'apartment_id',
        'notes',
        'receiver_id'
    ];
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function apartment(){
        return $this->belongsTo(Apartment::class, 'apartment_id');
    }

    public function services(){
        return $this->belongsToMany(Service::class, 'bill_details', 'bill_id', 'service_id');
    }
}
