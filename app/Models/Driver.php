<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    // ✅ Use the correct table name
    protected $table = 'driver_list';

    // ✅ Define fillable columns matching your database
    protected $fillable = [
        'license_id',
        'driver_name',
        'contact_no',
        'home_address',
        'license_issue_date',
        'license_expire_date',
        'date_of_birth',
        'license_type',
        'registered_at',
        'status',
        'signature_path',
        'is_archived'
    ];
}
