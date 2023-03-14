<?php

namespace App\Models;

use CodeIgniter\Model;

class CoursesHoursModel extends Model
{
    protected $table         = ['courses_hours'];
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'courses_id',
        'students_id',
        'report_types_id',
        'hours',
    ];

    protected $returnType    = 'array';

/*
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $useSoftDeletes = true;
*/


    /**
     * Get total hous and repor_type_id for student_id and course_id
     */
    public function getHoursForStudenAndCourses($students_id, $courses_id) {

        $builder = $this->table('courses_hours');
        $builder->select('report_types_id, hours as course_hours');
        $builder->where('students_id', $students_id);
        $builder->where('courses_id', $courses_id);

        return $builder->findAll();

    }
}
