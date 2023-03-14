<?php

namespace App\Models;

use CodeIgniter\Model;

class AcademyTutorModel extends Model
{
    protected $table         = 'academies_users';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'users_id',
        'academies_id',
        'profiles_id',
        'metadata',
    ];

    protected $returnType    = 'array';

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $useSoftDeletes = true;

    private $users_fields = [
        'id',
        'email',
        'password',
        'name',
        'dni',
        'birthdate',
        'surname',
        'phone',
        'address',
        'post_code',
        'languages_id',
        'countries_id',
        'metadata',
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



    /**
     * Get total hous and repor_type_id for student_id and course_id
     */
    public function getTeachers() {

        $builder = $this->table('academies_users');
        $builder->select(''
            .'academies_users.*'
            .','.$this->fieldsToString('users_fields','users','user')."\n" //student user data
            .',languages.name as languages_name, countries.name as countries_name'."\n"
        );
        $builder->join('users', 'academies_users.users_id = users.id');
        $builder->join('languages', 'users.languages_id = languages.id');
        $builder->join('countries', 'users.countries_id = countries.id');
        $builder->where('profiles_id', 15);


        return $builder->findAll();

    }
}
