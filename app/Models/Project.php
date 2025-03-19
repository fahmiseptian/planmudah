<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model {
    use HasFactory;
    protected $primaryKey = 'id_project';
    protected $table = 'projects';
    protected $fillable = ['code_project', 'id_project', 'deskripsi', 'status', 'cover', 'start_date', 'end_date', 'created_by','deleted_at'];
    protected $hidden = ['id_project'];
    public $incrementing = true;

    /**
     * Private function to generate unique project code
     */
    
    private function generateCodeProject($name) {
        $initials = strtoupper(substr(preg_replace('/\s+/', '', $name), 0, 4));
        $randomNumber = mt_rand(1000, max: 9999);
        $code = $initials . $randomNumber;
        // Pastikan kode unik di database
        while (self::where('code_project', $code)->exists()) {
            $randomNumber = mt_rand(1000, 9999);
            $code = $initials . $randomNumber;
        }

        return $code;
    }
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'id_project', 'id_project');
    }
    public static function createProject($data) {
        $project = new self();
        $project->code_project = $project->generateCodeProject($data['name']);
        $project->name = $data['name'];
        $project->deskripsi = $data['deskripsi'] ?? null;
        $project->status = $data['status'] ?? 'pending';
        $project->cover = $data['cover'] ?? null;
        $project->start_date = $data['start_date'] ?? null;
        $project->end_date = $data['end_date'] ?? null;
        $project->created_by = $data['created_by'];
        $project->save();

        return $project;
    }
    public function listProject($id_user) {
        return self::where('created_by', $id_user)->whereNull('deleted_at')->get();
    }
    public function getProjectById($code, $id_user) {
        return self::with(['tasks' => function ($query) {
            $query->whereNull('deleted_at');
        }])->where('code_project', $code)->where('created_by', $id_user)->first();
    }
    public function updateProject($code, $data) {
        $update = self::where('code_project', $code)->update($data);
        return $update;
    }
    public function deleteProject($code, $id_user) {
        $project = self::where('code_project', $code)->where('created_by', $id_user)->first();
        if ($project) {
            $project->update(['deleted_at' => now()]);
            return true;
        }
        return false;
    }  
}