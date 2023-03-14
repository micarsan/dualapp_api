<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\UsersModel;

class Users extends ResourceController
{
    use ResponseTrait;
    
    // get all users
    public function index()
    {
        $model = new UsersModel();
        $data = $model->getAll();
        return $this->respond($data, 200);
    }

    // get single user
    public function show($id = null)
    {
        $model = new UsersModel();
        $data = $model->get($id);
        //$data = $model->getWhere(['id' => $id])->getResult();
        if ($data) {
            return $this->respond($data);
        } else {
            return $this->failNotFound('No Data Found with id ' . $id);
        }
    }

    // create a user
    public function create()
    {
        $model = new UsersModel();
        $data = [
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'name' => $this->request->getPost('name'),
            'surname' => $this->request->getPost('surname'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address'),
            'post_code' => $this->request->getPost('post_code'),
            'languages_id' => 1,
            'countries_id' => 1,
        ];
        
        $model->insert($data);
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
        $model = new UsersModel();
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
                'languages_id' => 1,
                'countries_id' => 1,
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
                'languages_id' => 1,
                'countries_id' => 1,
            ];
        }
        
        // Insert into Database
        $model->update($id, $data);
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
        $model = new UsersModel();
        $data = $model->find($id);
        if ($data) {
            $model->delete($id);
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
