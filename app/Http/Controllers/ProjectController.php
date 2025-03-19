<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Models\User;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProjectController extends Controller
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

    public function createProject(Request $request){
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'status' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $data = [
            'name' => $validatedData['name'],
            'deskripsi' => $validatedData['deskripsi'],
            'status' => $validatedData['status'],
            'start_date' => $validatedData['start_date'],
            'end_date' => $validatedData['end_date'],
            'created_by' => $this->data['member_id']
        ];

        $create = $this->Model['Project']->createProject($data);
        if ($create) {
            return response()->json([
                'message' => 'Berhasil membuat Project baru',
                'success' => true
            ],200);
        }
        return response()->json([
            'message' => 'Gagal membuat Project baru',
            'success' => false
        ],500);
    }

    public function listProject() {
        $data = $this->Model['Project']->listProject($this->data['member_id']);
        return response()->json([
            'data' => $data,
            'success' => true
        ],200);
    }
    public function detailProject($code) {
        $data = $this->Model['Project']->getProjectById($code,$this->data['member_id']);
        return response()->json([
            'data' => $data,
            'success' => true
        ],200);
    }
    public function updateProject(Request $request) {
        $validatedData = $request->validate([
            'name' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'status' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        $data = [
            'updated_by' => $this->data['member_id']
        ];

        foreach ($validatedData as $key => $value) {
            if (!is_null($value)) {
                $data[$key] = $value;
            }
        }
        $update = $this->Model['Project']->updateProject($request->input('code_project'),$data);
        if ($update) {
            return response()->json([
                'message' => 'Berhasil update Project',
                'success' => true
            ],200);
        }
        return response()->json([
            'message' => 'Gagal update Project',
            'success' => false
        ],500);
    }
    public function deleteProject($code) {
        $delete = $this->Model['Project']->deleteProject($code,$this->data['member_id']);
        if ($delete) {
            return response()->json([
                'message' => 'Berhasil Mengahapus Project',
                'success' => true
            ],200);
        }
        return response()->json([
            'message' => 'Gagal Mengahapus Project',
            'success' => true
        ],500);
    }
}