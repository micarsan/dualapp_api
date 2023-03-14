<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\CompaniesModel;
use App\Models\StudentsModel;

class Companies extends ResourceController
{
    use ResponseTrait;
    
    // get all companies
    public function index()
    {
        $model = new CompaniesModel();
        $data = $model->getAll();
        return $this->respond($data, 200);
    }



    // get single company
    public function show($id = null)
    {
        $model = new CompaniesModel();
        $data = $model->get($id);
        //$data = $model->getWhere(['id' => $id])->getResult();
        if ($data) {
            return $this->respond($data);
        } else {
            return $this->failNotFound('No Data Found with id ' . $id);
        }
    }


    // Get basic data of companies for an academy_id
    public function getCompaniesForAcademyUser($academy_user_id) {
        
        $companies_model = new CompaniesModel();
        $data = $companies_model->getCompaniesForAcademyUserId($academy_user_id);
        if ($data) {
            return $this->respond($data);
        } else {
            return $this->failNotFound('No Companies found for academy user ' . $academy_user_id);
        }
    }


    // Get all academy users
    public function getAcademyUsers() {
        
        $companies_model = new CompaniesModel();
        $data = $companies_model->getAcademyUsers();
        if ($data) {
            return $this->respond($data);
        } else {
            return $this->failNotFound('I can\' get all academy user.');
        }
    }


    // Get all academy users group by company id
    public function getAcademyUsersGroupCompanyId() {

        $companies_model = new CompaniesModel();
        $data = $companies_model->getAcademyUsers();
        if ($data) {

            $new_data = [];
            foreach( $data as $line ) {
                $key = $line['companies_id'];
                $new_data[$key][] = $line;
            }

            return $this->respond($new_data);
        } else {
            return $this->failNotFound('I can\' get all academy users.');
        }
    }


    // Get basic data of academy tutor for a company id
    public function getAcademyUsersOfCompany($company_id) {
        
        $companies_model = new CompaniesModel();
        $data = $companies_model->getAcademyUsersOfCompany($company_id);
        if ($data) {
            return $this->respond($data);
        } else {
            return $this->failNotFound('This company ('.$company_id.') has no academy user ');
        }
    }


    // create a company
    public function create()
    {
        $model = new CompaniesModel();
        $data = [
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'name' => $this->request->getPost('name'),
            'surname' => $this->request->getPost('surname'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address'),
            'post_code' => $this->request->getPost('post_code'),
            'languages_id' => $this->request->getPost('languages_id'),
            'countries_id' => $this->request->getPost('countries_id'),
        ];
        
//        $model->insert($data);
        $response = [
            'status'   => 201,
            'error'    => null,
            'messages' => [
                'success' => 'Data Saved'
            ]
        ];

        return $this->respondCreated($data, 201);
    }

    // update user
    public function update($id = null)
    {
        $model = new CompaniesModel();
        $json = $this->request->getJSON();
        if ($json) {
            $data = [
                'email' => $json->email,
                'password' => $json->password,
                'name' => $json->name,
                'surname' => $json->surname,
                'phone' => $json->phone,
                'address' => $json->address,
                'post_code' => $json->post_code,
                'languages_id' => $json->languages_id,
                'countries_id' => $json->countries_id,
            ];
        } else {
            //$input = $this->request->getRawInput();
            $data = [
                'email' => $this->request->getPost('email'),
                'password' => $this->request->getPost('password'),
                'name' => $this->request->getPost('name'),
                'surname' => $this->request->getPost('surname'),
                'phone' => $this->request->getPost('phone'),
                'address' => $this->request->getPost('address'),
                'post_code' => $this->request->getPost('post_code'),
                'languages_id' => $this->request->getPost('languages_id'),
                'countries_id' => $this->request->getPost('countries_id'),
            ];
        }
        
        // Insert into Database
//        $model->update($id, $data);
        $response = [
            'status'   => 200,
            'error'    => null,
            'messages' => [
                'success' => "User $id Updated"
            ]
        ];
        return $this->respond($response);
    }

    // delete product
    public function delete($id = null)
    {
        $model = new CompaniesModel();
        $data = $model->find($id);
        if ($data) {
//            $model->delete($id);
            $response = [
                'status'   => 200,
                'error'    => null,
                'messages' => [
                    'success' => 'Data Deleted'
                ]
            ];
            return $this->respondDeleted($response);
        } else {
            return $this->failNotFound("User $id not found.");
        }
    }
}
