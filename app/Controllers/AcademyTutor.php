<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\AcademyTutorModel;
use App\Models\CoursesModel;

class AcademyTutor extends ResourceController
{
    use ResponseTrait;
    

    public function index() {
        return $this->getTeachers();
    }


    // get all teachers
    public function getTeachers()
    {
        //$model = new CoursesModel();
        $model = new AcademyTutorModel();
        $data = $model->getTeachers();
        return $this->respond($data, 200);
    }

}