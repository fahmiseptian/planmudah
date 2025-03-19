<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Models\User;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class TaskController extends Controller
{
    protected $data = [];
    protected $Model = [];
    public function __construct()
    {
        // Load Models
        $this->Model['Project'] = new Project();
        $this->Model['Task'] = new Task();
        $this->Model['User'] = new User();

        try {
            if ($user = JWTAuth::parseToken()->authenticate()) {
                $this->data['member_id'] = $user->id;
            } else {
                // Jika tidak ada token, member_id dan member_type tidak diset atau diatur default
                $this->data['member_id'] = null;
            }
        } catch (JWTException $e) {
            // Jika token tidak valid atau tidak ditemukan, atur nilai default
            $this->data['member_id'] = null;
        }
    }
    public function createTask(Request $request){
        $validatedData = $request->validate([
            'code_project' => 'required|string|max:255',
            'name' => 'nullable|string',
            'status' => 'required|string|max:255',
            'due_date' => 'required|date',
        ]);

        $data = [
            'name' => $validatedData['name'],
            'code_project' => $validatedData['code_project'],
            'status' => $validatedData['status'],
            'due_date' => $validatedData['due_date'],
            'created_by' => $this->data['member_id']
        ];

        $create = $this->Model['Task']->createTask($data);
        if ($create) {
            return response()->json([
                'message' => 'Berhasil membuat Task baru',
                'success' => true
            ],200);
        }
        return response()->json([
            'message' => 'Gagal membuat Task baru',
            'success' => false
        ],500);
    }
    public function detailTask($id) {
        $data = $this->Model['Task']->getTaskById($id);
        return response()->json([
            'data' => $data,
            'success' => true
        ],200);
    }
    public function updateTask(Request $request){
        $validatedData = $request->validate([
            'name' => 'nullable|string',
            'status' => 'nullable|string|max:255',
            'due_date' => 'nullable|date',
        ]);

        $data = [
            'updated_by' => $this->data['member_id']
        ];

        foreach ($validatedData as $key => $value) {
            if (!is_null($value)) {
                $data[$key] = $value;
            }
        }

        $update = $this->Model['Task']->updateTask($request->id, $data);
        if ($update) {
            return response()->json([
                'message' => 'Berhasil update Task',
                'success' => true
            ],200);
        }
        return response()->json([
            'message' => 'Gagal update Task',
            'success' => false
        ],500);
    }
    public function deleteTask($id) {
        $data = $this->Model['Task']->deleteTask($id);
        return response()->json([
            'message' => 'Berhasil Menghapus Task',
            'success' => true
        ],200);
    }
}