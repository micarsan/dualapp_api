<?php

namespace App\Models;

use CodeIgniter\Model;

class ReportsModel extends Model
{
    protected $table         = 'reports_daily';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'report_types_id',
        'students_id',
        'companies_users_id',
        'academies_users_id',
        'courses_id',
        'date',
        'hours',
        'hours_total',
        'doing',
        'sing_tutor_acad',
        'sign_tutor_comp',
        'metadata',
    ];

    protected $returnType    = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';

    private $reports_fields = [
        'id',
        'report_types_id',
        'students_id',
        'companies_users_id',
        'academies_users_id',
        'courses_id',
        'date',
        'hours',
        'hours_total',
        'doing',
        'sing_tutor_acad',
        'sign_tutor_comp',
    ];

    /**
     * Return a fields string in sql format for select header
     * 
     *   $table => Table name and name of array with _fields sufix (ej: users_fields)
     *   $new_table_name => Name for table alias (ej: companies_users => companies_users_email for users.email of companies_users)
     */
    private function fieldsToString($array_fields, $table_name, $alias_prefix=null)
    {

        $return_string = '';
        
        if ($alias_prefix) {
            foreach ($this->$array_fields as $field) {
                $return_string .= $table_name . '.' . $field . ' as ' . $alias_prefix . '_' . $field . ',';
            }
        } else {
            foreach ($this->$array_fields as $field) {
                $return_string .= $table_name . '.' . $field . ' as ' . $table_name . '_' . $field . ',';
            }
        }

        return substr($return_string, 0, -1);
    }

    public function getAll()
    {
        $builder = $this->table('reports_daily');
        $builder->select('*');
        return $builder->findAll();
    }

    public function get($id)
    {
        $builder = $this->table('reports_daily');
        $builder->select('*');
        return $builder->find($id);
    }


    public function getTotalHoursByType($student_id)
    {

        $builder = $this->table('reports_daily');
        $builder->select('report_types_id, report_types.name as report_types_name, SUM(hours_total) as hours_total');
        $builder->join('report_types', 'reports_daily.report_types_id = report_types.id');
        $builder->groupBy('report_types_id');
        $builder->where('students_id', $student_id);
        return $builder->findAll();
    }
}
