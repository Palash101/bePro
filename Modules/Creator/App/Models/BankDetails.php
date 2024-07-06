<?php

namespace Modules\Creator\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Creator\Database\factories\BankDetailsFactory;

class BankDetails extends Model
{
    use HasFactory;

    protected $table = 'bank_details';
    protected $fillable = ['user_id','bank_name','account_name','ifsc_code','account_number'];
}
