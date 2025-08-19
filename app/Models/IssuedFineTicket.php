<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IssuedFineTicket extends Model
{
    protected $table = 'issued_fine_tickets';

    // Allow mass assignment for specific columns
    protected $fillable = [
        'license_id',
        'violation_type',
        'status',
        'total_amount',
        'vehicle_no',
        'place',
        'created_at',
        // add more columns as needed
    ];
}
