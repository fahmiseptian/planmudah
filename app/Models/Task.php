<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Task extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_task';
    protected $table = 'tasks';
    protected $fillable = ['id_task ', 'id_project', 'name', 'status', 'due_date', 'created_by'];
    protected $hidden = ['id_task'];
    public $incrementing = true;

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    private function getIdProject($code){
        return Project::where('code_project', $code)->value('id_project');
    }

    public function createTask($data)
    {
        $task = new self();
        $task->id_project = $this->getIdProject($data['code_project']);
        $task->name = $data['name'];
        $task->status = $data['status'];
        $task->due_date = $data['due_date'];
        $task->created_by = $data['created_by'];
        $task->save();

        return $task;
    }
    public function listTask() {
        return $this->belongsTo('App\Models\Project', 'id_project', 'id_project')->whereNull('deleted_at')->whereHas('project', function ($query) {
            $query->whereNull('deleted_at');
        });
    }
    public function getTaskById($id) {
        return self::where('id_task', $id)->first();
    }
    public function updateTask($id, $data) {
        return self::where('id_task', $id)->update($data);
    }
    public function deleteTask($id) {
        return self::where('id_task', $id)->update(['deleted_at' => now()]);
    }
}
