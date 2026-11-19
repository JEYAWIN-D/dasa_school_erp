<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'name', 'code', 'class_id', 'type', 'max_marks', 'pass_marks',
        'is_theory', 'is_practical', 'is_active', 'sort_order',
        'is_elective', 'language_type', 'board_curriculum', 'credit_hours',
        'stream', 'medium', 'is_coscholastic',
    ];

    protected $casts = [
        'is_theory'       => 'boolean',
        'is_practical'    => 'boolean',
        'is_active'       => 'boolean',
        'is_elective'     => 'boolean',
        'is_coscholastic' => 'boolean',
    ];

    public function class(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function examSchedules(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ExamSchedule::class);
    }

    public function allocations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TeacherSubjectAllocation::class, 'subject_id');
    }

    public function teachers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'teacher_subject_allocations', 'subject_id', 'employee_id')
            ->distinct();
    }

    public function getCategoryGroupAttribute(): string
    {
        $name = strtolower($this->name);
        $type = strtolower($this->type ?? '');
        $code = strtoupper($this->code ?? '');

        if (str_starts_with($code, 'KG-') || str_contains($name, 'kg') || str_contains($name, 'rhyme') || str_contains($name, 'montessori') || str_contains($name, 'phonics') || str_contains($name, 'motor skills') || str_contains($name, 'action song') || str_contains($name, 'circle time') || str_contains($name, 'wrap-up')) {
            return 'Kindergarten & Early Years';
        }
        if ($type === 'language' || in_array($name, ['tamil', 'english', 'hindi', 'sanskrit', 'french', 'story']) || str_contains($name, 'language') || str_contains($name, 'reading & grammar') || str_contains($name, 'literature')) {
            return 'Languages & Literature';
        }
        if (str_contains($name, 'computer') || str_contains($name, 'coding') || str_contains($name, 'robotics') || str_contains($name, 'stem') || str_contains($name, 'ai') || $type === 'practical') {
            return 'Computer Science & STEM Labs';
        }
        if ($type === 'activity' || str_contains($name, 'physical') || str_contains($name, 'games') || str_contains($name, 'sports') || str_contains($name, 'yoga') || str_contains($name, 'swimming') || str_contains($name, 'chess') || str_contains($name, 'art') || str_contains($name, 'craft') || str_contains($name, 'music') || str_contains($name, 'dance') || str_contains($name, 'library')) {
            return 'Co-Curricular, Sports & Wellness';
        }
        if (str_contains($name, 'special') || str_contains($name, 'abl') || str_contains($name, 'assembly') || str_contains($name, 'eksaq') || str_contains($name, 'scout') || str_contains($name, 'debate')) {
            return 'Special & Extracurricular Activities';
        }
        return 'Core Academic Subjects';
    }

    public function getHandlingTeacherNamesAttribute(): string
    {
        $names = $this->allocations->map(fn($a) => $a->employee?->full_name ?: $a->employee?->first_name)->filter()->unique();
        if ($names->isEmpty()) {
            return 'Not Assigned';
        }
        return $names->implode(', ');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
