<?php

namespace App\Http\Controllers;

use App\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    //
    public function tasksForAuthedUser(Request $request){
        return response()->json($request->user()->tasks);
    }
    public function create(Request $request){
        $task = Task::create(['name' => $request->name ,'date' => $request->date,"user_id" => $request->user()->id]);
        return response()->json(["success" =>true , "task"=>$task]);
    }

    public function update(Request $request){
        $task = Task::find($request->id);
        $task->date = $request->date;
        if($task->save()){
            return response()->json(true);
        }
    }
    public function delete(Request $request){
        $task = Task::where(["user_id" => $request->user()->id , "id" => $request->id]);
        if($task->delete($request->id)){
            return response()->json(true);
        }
    }
}
