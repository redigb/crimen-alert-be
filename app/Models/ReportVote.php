<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportVote extends Model {

    protected $table = 'report_votes';

    protected $fillable = [
        'user_id',
        'report_id',
        'vote'
    ];
}
