<?php
namespace App\Http\Controllers;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
class TaskController extends Controller {
    public function index(Request $request): View {
        $projects = Project::orderBy('name')->get();
        $selectedProjectId = $request->integer('project_id') ?: $projects->first()?->id;
        $tasks = Task::with('project')->when($selectedProjectId, fn($q) => $q->where('project_id', $selectedProjectId))->orderBy('priority')->orderBy('id')->get();
        return view('tasks.index', compact('projects', 'tasks', 'selectedProjectId'));
    }
    public function store(Request $request): RedirectResponse {
        $data = $request->validate(['name'=>'required|string|max:255','project_id'=>'required|integer|exists:projects,id']);
        $data['priority'] = (Task::where('project_id', $data['project_id'])->max('priority') ?? 0) + 1;
        Task::create($data);
        return redirect()->route('tasks.index', ['project_id'=>$data['project_id']])->with('success','Task created.');
    }
    public function update(Request $request, Task $task): RedirectResponse {
        $data = $request->validate(['name'=>'required|string|max:255','project_id'=>'required|integer|exists:projects,id']);
        $oldProject = $task->project_id; $task->update($data);
        if ((int)$oldProject !== (int)$data['project_id']) { $this->renumber((int)$oldProject); $this->renumber((int)$data['project_id']); }
        return redirect()->route('tasks.index', ['project_id'=>$data['project_id']])->with('success','Task updated.');
    }
    public function destroy(Task $task): RedirectResponse {
        $projectId = (int)$task->project_id; $task->delete(); $this->renumber($projectId);
        return redirect()->route('tasks.index', ['project_id'=>$projectId])->with('success','Task deleted.');
    }
    public function reorder(Request $request): JsonResponse {
        $data = $request->validate(['project_id'=>'required|integer|exists:projects,id','task_ids'=>'required|array|min:1','task_ids.*'=>'required|integer|distinct|exists:tasks,id']);
        $projectId = (int)$data['project_id']; $ids = array_map('intval',$data['task_ids']);
        $valid = Task::where('project_id',$projectId)->whereIn('id',$ids)->pluck('id')->map(fn($id)=>(int)$id)->all();
        if (count($valid) !== count($ids) || count(array_diff($ids,$valid))) return response()->json(['message'=>'Tasks must belong to the selected project.'],422);
        foreach ($ids as $index=>$id) Task::where('project_id',$projectId)->where('id',$id)->update(['priority'=>$index+1]);
        return response()->json(['message'=>'Task order saved.']);
    }
    private function renumber(int $projectId): void {
        Task::where('project_id',$projectId)->orderBy('priority')->orderBy('id')->get()->each(fn(Task $task, int $index)=>$task->update(['priority'=>$index+1]));
    }
}
