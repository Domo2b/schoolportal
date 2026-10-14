<?php

namespace App\Models;

use Database\Factories\EnrolmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrolment extends Model
{
    /** @use HasFactory<EnrolmentFactory> */
    use HasFactory;

    protected $table = 'course_student';

    protected $fillable = ['student_id', 'course_id', 'session', 'semester', 'status'];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
