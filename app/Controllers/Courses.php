<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\CoursesModel;

class Courses extends ResourceController
{
    use ResponseTrait;
    

    // get all students
    public function index()
    {
        $model = new CoursesModel();
        $data = $model->getAll();
        return $this->respond($data, 200);
    }

}