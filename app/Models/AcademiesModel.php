<?php

namespace App\Models;

use CodeIgniter\Model;

class AcademiesModel extends Model
{
    protected $table         = 'students';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'users_id',
        'academies_users_id',
        'companies_users_id',
        'courses_id',
    ];

    protected $returnType    = 'array';

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $useSoftDeletes = true;

    private $users_fields = [
        'email',
        'password',
        'name',
        'surname',
        'phone',
        'address',
        'post_code',
        'languages_id',
        'countries_id',
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


    public function getFull($id)
    {

        $builder = $this->table('students');
        $builder->select(
            'studentss.*,'.
            $this->fieldsToString('users_fields','users') .
            ',languages.name as languages_name, countries.name as countries_name,'.
            $this->fieldsToString('users_fields', 'acad', 'academies')
        );
        $builder->join('users', 'students.users_id = users.id');
        $builder->join('languages', 'users.languages_id = languages.id');
        $builder->join('countries', 'users.countries_id = countries.id');
        $builder->join('academies_users', 'students.academies_users_id = academies_users.id');
        $builder->join('users as acad', 'academies_users.id.users_id = acad.id');
        return $builder->find($id);
    }
}
