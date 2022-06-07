<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Blacklist;

class Advertiser extends Model
{
    use HasFactory;


    public function blacklist()
    {
        return $this->belongsTo(Blacklist::class, 'adv_id');
    }
}
