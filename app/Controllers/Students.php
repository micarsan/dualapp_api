<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\StudentsModel;
use App\Models\UsersModel;
use App\Models\ReportsModel;
use App\Models\CoursesHoursModel;

class Students extends ResourceController
{
    use ResponseTrait;
    

    // get all students
    public function index()
    {
        return $this->getStudentsForAcademyUser();
        
        $model = new StudentsModel();
        $data = $model->getAll();
        return $this->respond($data, 200);
    }



    // get single student
    public function show($id = null)
    {
        $model = new StudentsModel();
        $data = $model->get($id);
        //$data = $model->getWhere(['id' => $id])->getResult();
        if ($data) {
            return $this->respond($data);
        } else {
            return $this->failNotFound('No Data Found with id ' . $id);
        }
    }



    // Get all data of students for one teacher
    public function getStudentsForAcademyUser($academy_user_id=null)
    {

        $studentsModel = new StudentsModel();
        
        // Get all data of student, academy tutor and company tutor
$data = $studentsModel->getStudentForAcademiesUserId();
        //$data = $studentsModel->getStudentForAcademiesUserId($academy_user_id);
        if ($data) {

            $reportsModel = new ReportsModel();
            $coursesHoursModel = new CoursesHoursModel();

            foreach( $data as $index => $row ) {

                

                // Get total hours doit of reports group by type
                $tmp = $reportsModel->getTotalHoursByType($row['student_id']);
                if($tmp) {
                    foreach( $tmp as $tmp_row ) {
                        
                        $report_types_id = $tmp_row['report_types_id'];
                        $data[$index]['hours'][$report_types_id]['report_types_id'] = $tmp_row['report_types_id'];
                        $data[$index]['hours'][$report_types_id]['report_types_name'] = $tmp_row['report_types_name'];
                        $data[$index]['hours'][$report_types_id]['hours_total'] = $tmp_row['hours_total'];

                    }
                } else {
                    $data[$index]['hours'][1]['report_types_id'] = 1;
                    $data[$index]['hours'][1]['report_types_name'] = 'DUAL';
                    $data[$index]['hours'][1]['hours_cursed'] = 0;
                    $data[$index]['hours'][2]['report_types_id'] = 2;
                    $data[$index]['hours'][2]['report_types_name'] = 'FCT';
                    $data[$index]['hours'][2]['hours_cursed'] = 0;
                }

                // Get total hours of course
                $tmp = $coursesHoursModel->getHoursForStudenAndCourses($row['student_id'], $row['course_id']);

                if($tmp) {
                    foreach( $tmp as $tmp_row ) {
                        
                        $report_types_id = $tmp_row['report_types_id'];
                        $data[$index]['hours'][$report_types_id]['course_hours'] = $tmp_row['course_hours'];
                        
                        if( !empty($data[$index]['hours'][$report_types_id]['hours_total']) &&
                            !empty($data[$index]['hours'][$report_types_id]['course_hours']) ) {
                            
                            $data[$index]['hours'][$report_types_id]['percent_complete'] = round( ($data[$index]['hours'][$report_types_id]['hours_total'] / $data[$index]['hours'][$report_types_id]['course_hours'] * 100));
                        } else {
                            $data[$index]['hours'][$report_types_id]['percent_complete'] = 0;
                        }
                    }
                } else {
                    $data[$index]['hours'][1]['course_hours'] = 0;
                    $data[$index]['hours'][2]['course_hours'] = 0;
                    $data[$index]['hours'][1]['percent_complete'] = 0;
                    $data[$index]['hours'][2]['percent_complete'] = 0;
                    }

                // Set percent of course complete

            }
            
            return $this->respond($data);
        } else {
            return $this->failNotFound('No Data Found with for academic user id ' . $academy_user_id);
        }
    }



    public function create()
	{
		$data = $this->request->getPost();

        // users table
        $model = new UsersModel();
        if( !empty($data['observations']) ) {
            $array_tmp['observations'] = $data['observations'];
            $data['metadata'] = json_encode($array_tmp);
        }

        if( !$model->insert($data) ){
            die( print_r($model->errors()) );
        }
        
        $data['users_id'] = $model->insertID();


        // students table
        $model = new StudentsModel();
           
        if( !$model->insert($data) ){
            die( print_r($model->errors()) );
        }
        
        $data['students_id'] = $model->insertID();

        
        // courses_hours table (total hours for each student and report_type)
        $model = new coursesHoursModel();

        if( !empty($data['courses_hours'][1]) ) { //DUAL
            
            $data['report_types_id'] = 1;
            $data['hours'] = $data['courses_hours'][1];
            
            if( !$model->insert($data) ){
                die( print_r($model->errors()) );
            }
        }

        if( !empty($data['courses_hours'][2]) ) { //FCT
            
            $data['report_types_id'] = 2;
            $data['hours'] = $data['courses_hours'][2];
            
            if( !$model->insert($data) ){
                die( print_r($model->errors()) );
            }
        }

        $response = [
            'status'   => 201,
            'error'    => null,
            'messages' => [
                'success' => 'Data Saved'
            ]
        ];

        header('Location: ' . $_SERVER['HTTP_REFERER']);
        //return $this->respondCreated( $this->respondCreated($data), 201 );

	}


    // update user
    public function update($id = null)
    {
        $data = $this->request->getRawInput();	

        // students table
        $model = new StudentsModel();
        $user = $model->find($id);

		if ( !$user ) {
			return $this->failNotFound('Student not found (id:'.$id.')');
		}
           
        $_newData = array();
        $_newData['academies_users_id'] = $data['academies_users_id'];
        $_newData['companies_users_id'] = $data['companies_users_id'];
        $_newData['courses_id'] = $data['courses_id'];
        
        if( !$model->update($id, $_newData) ){
            die( print_r($model->errors()) );
        }
        unset($_newData);

        
        // users table
        $users_id = $user['users_id'];
        $model = new UsersModel();

        $user = $model->find($users_id);

		if ( !$user ) {
			return $this->failNotFound('User not found (id:'.$id.')');
		}

        if( isset($data['password']) && empty($data['password']) ) {
            unset($data['password']);
        }
        if( $data['email'] == $user['email'] ){
            unset($data['email']);
        }

        if( !$model->update($users_id, $data) ){
            die( print_r($model->errors()) );
        }
        
/** hay que hacer esto 

        // courses_hours table (total hours for each student and report_type)
        $model = new coursesHoursModel();


        if( !empty($data['courses_hours'][1]) ) { //DUAL
            
            $data['report_types_id'] = 1;
            $data['hours'] = $data['courses_hours'][1];
            
            if( !$model->insert($data) ){
                die( print_r($model->errors()) );
            }
        }

        if( !empty($data['courses_hours'][2]) ) { //FCT
            
            $data['report_types_id'] = 2;
            $data['hours'] = $data['courses_hours'][2];
            
            if( !$model->insert($data) ){
                die( print_r($model->errors()) );
            }
        }
*/

        return $this->respondUpdated( $data, 201 );
        //header('Location: ' . $_SERVER['HTTP_REFERER']);
    }

    // delete product
    public function delete($id = null)
    {
echo 'delete!! id:'.$id;
exit;
        $model = new StudentsModel();
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
