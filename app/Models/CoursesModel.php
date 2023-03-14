<?php

namespace App\Models;

use CodeIgniter\Model;

class CoursesModel extends Model
{
    protected $table         = 'courses';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'name',
        'acronym',
        'metadata',
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
     * Get all courses
    */
    public function getAll() {
        $builder = $this->table('courses');
        $builder->orderBy('acronym', 'ASC');

        return $builder->findAll();
    }


}
