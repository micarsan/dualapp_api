<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentsModel extends Model
{
    protected $table         = 'students';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'users_id',
        'academies_users_id',
        'companies_users_id',
        'courses_id',
    ];
    protected $validationRules = [
        'users_id'        => 'required',
        'courses_id'      => 'required',
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

    public function getAll()
    {

        $builder = $this->table('students');
        $builder->select(
            'students.*,'.
            $this->fieldsToString('users_fields', 'users') .
            ',languages.name as languages_name, countries.name as countries_name'
        );
        $builder->join('users', 'students.users_id = users.id');
        $builder->join('languages', 'users.languages_id = languages.id');
        $builder->join('countries', 'users.countries_id = countries.id');
        return $builder->findAll();
    }

    public function get($id)
    {

        $builder = $this->table('students');
        $builder->select(
            'students.*,'.
            $this->fieldsToString('users_fields','users') .
            ',languages.name as languages_name, countries.name as countries_name'
        );
        $builder->join('users', 'students.users_id = users.id');
        $builder->join('languages', 'users.languages_id = languages.id');
        $builder->join('countries', 'users.countries_id = countries.id');
        return $builder->find($id);
    }


    public function getStudentForAcademiesUserId($academies_user_id=null)
    {

        $builder = $this->table('students');
        $builder->select(''
            .',students.id as student_id, students.users_id as student_users_id, students.academies_users_id as student_academies_users_id'
            .',students.companies_users_id as student_companies_users_id, students.courses_id as student_courses_id'
            .','.$this->fieldsToString('users_fields','student','user')."\n" //student user data
            .',languages.name as languages_name, countries.name as countries_name'."\n"
            .','.$this->fieldsToString('users_fields','academy_tutor','academy_tutor')."\n" //student user data
            .','.$this->fieldsToString('users_fields','company_tutor','company_tutor')."\n" //student user data
            .',company.id as company_id, company.name as company_name, company.address as company_address'
            .',courses.id as course_id, courses.name as course_name, courses.acronym as course_acronym'            
        );
        $builder->join('users student', 'students.users_id = student.id');
        $builder->join('languages', 'student.languages_id = languages.id');
        $builder->join('countries', 'student.countries_id = countries.id');
        $builder->join('academies_users', 'students.academies_users_id = academies_users.id');
        $builder->join('users academy_tutor', 'academies_users.users_id = academy_tutor.id');
        $builder->join('companies_users', 'students.companies_users_id = companies_users.id');
        $builder->join('users company_tutor', 'companies_users.users_id = company_tutor.id');
        $builder->join('companies company', 'companies_users.companies_id = company.id');
        $builder->join('courses', 'students.courses_id = courses.id');
        if( $academies_user_id ) {
            $builder->where('students.academies_users_id', $academies_user_id);
        }
        
/*        
$builder->findAll();
print_r($builder->getLastQuery());
*/      
        
        return $builder->findAll();

    }



    public function setStudent($data) {

        //Prepare array for users table
        

    }
}
