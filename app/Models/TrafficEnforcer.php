<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrafficEnforcer extends Model
{
        protected $table = 'traffic_enforcers';
        protected $primaryKey = 'enforcer_id';
        public $timestamps = false;
        public $incrementing = false;
        protected $keyType = 'string';

        protected $fillable = [
                'enforcer_id',
                'enforcer_name',
                'enforcer_email',
                'enforcer_password',
                'assigned_area',
                'profile_image',
                'contact_no',
                'gender',
                'registered_at',
                'code',
                'enforcer_signature',
                'is_locked',
                'role_id'
        ];
}
