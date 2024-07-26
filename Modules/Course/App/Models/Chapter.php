<?php

namespace Modules\Course\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Course\Database\factories\ChapterFactory;
use Modules\Course\App\Models\Course;
class Chapter extends Model
{
    protected $table = 'co_chapters';
    protected $fillable = ['creator_id','course_id','type','title','attachments','level','date','tags','description','UniqueId'];
    protected $hidden = [
        'creator_id', 
    ];

    public function course()
        {
            return $this->belongsTo(Course::class,'course_id')->select('id','title');
        }

}
