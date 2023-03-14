<?php

namespace App\Models;

use CodeIgniter\Model;

class UsersModel extends Model
{
    protected $table         = 'users';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'email',
        'password',
        'name',
        'surname',
        'dni',
        'birthdate',
        'phone',
        'address',
        'post_code',
        'countries_id',
        'languages_id',
        'metadata'
    ];
    protected $validationRules = [
        'email'        => 'required|valid_email|is_unique[users.email]',
        'password'     => 'required|min_length[8]',
        'name'         => 'required',
        'surname'      => 'required',
    ];

    protected $returnType    = 'array';

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $useSoftDeletes = true;

    protected $validationMessages = [
        'email' => [
            'is_unique' => 'Sorry. That email has already been taken. Please choose another.',
        ],
    ];

    public function getAll() {

        $builder = $this->table('users');
        $builder->select('users.*, languages.name as languages_name, countries.name as countries_name');
        $builder->join('languages', 'users.languages_id = languages.id');
        $builder->join('countries', 'users.countries_id = countries.id');
        return $builder->findAll();
    
    }

    public function get($id) {

        $builder = $this->table('users');
        $builder->select('users.*, languages.name as languages_name, countries.name as countries_name');
        $builder->join('languages', 'users.languages_id = languages.id');
        $builder->join('countries', 'users.countries_id = countries.id');
        return $builder->find($id);
    
    }


    public function insertUser( $data ){

        if( empty($data['email']) | empty($data['password']) ) {
            return false;
        }

        $new_data = Array();

        foreach($this->allowedFields as $field){
            if( !empty( $data[$field] ) && $field != 'id' ) {
                $new_data[$field] = $data[$field];
            }
        }

        $builder = $this->table('users');
        $builder->insert($new_data);

    }

    


}
