<?php

// app/Models/TrafficAdmin.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrafficAdmin extends Model
{
    protected $table = 'traffic_admins';
    protected $fillable = ['admin_email', 'code'];
    public $timestamps = false; // if your table doesn’t use created_at/updated_at
}
