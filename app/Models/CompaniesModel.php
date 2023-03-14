<?php

namespace App\Models;

use CodeIgniter\Model;

class CompaniesModel extends Model
{
    protected $table         = 'companies';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'name',
        'address',
        'post_code',
        'id_countries',
        'metadata',
    ];

    protected $returnType    = 'array';

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $useSoftDeletes = true;

    private $companies_fields = [
        'id',
        'name',
        'address',
        'post_code',
        'id_countries',
    ];

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

        $builder = $this->table('companies');
        $builder->select('*');
        return $builder->findAll();
    }

    public function get($id)
    {

        $builder = $this->table('companies');
        $builder->select('*');
        return $builder->find($id);
    }


    public function getCompaniesForAcademyUserId($academies_users_id)
    {

        $builder = $this->table('companies');
        $builder->select(
            'companies.*'
//            .','.$this->fieldsToString('users_fields','company_user')
            .',academies_companies.*'
            .',academies.*'
        );
        $builder->join('academies_companies', 'companies.id = academies_companies.companies_id');
        $builder->join('academies', 'academies_companies.academies_id = academies.id');
//        $builder->join('companies_users', 'companies.id = companies_users.companies_id');
//        $builder->join('users company_user', 'companies_users.users_id = company_user.id');
        //$builder->join('students', 'company_user.id = students.companies_users_id');
        //$builder->where('companies_users.profiles_id', 25); // show only company tutor profile


//        $builder->groupBy('report_types_id');
//        $builder->where('students_id', $academies_users_id);
        return $builder->findAll();
    }


    public function getAcademyUsers()
    {

        $builder = $this->table('companies');
        $builder->select('companies_users.*'
            .','.$this->fieldsToString('users_fields','users','user')."\n" //student user data
//            .',languages.name as languages_name, countries.name as countries_name'."\n"
        );
        $builder->join('companies_users', 'companies.id = companies_users.companies_id');
        $builder->join('users', 'companies_users.users_id = users.id');
        return $builder->findAll();
    }


    public function getAcademyUsersOfCompany($company_id)
    {

        $builder = $this->table('companies');
        $builder->select('companies_users.*');
        $builder->join('companies_users', 'companies.id = companies_users.companies_id');
        $builder->where('companies.id', $company_id);
        return $builder->findAll();
    }


}
