<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
class TaskController extends Controller
{
    public function index(){
        //모델 설정 이후에 가능, 로그인한 유저가 태스크만든 것을 보여줘 근데 최신 순부터 갖고와 라고 읽는다. (php artisan tinker에서 수정 후 가져오기 가능.
        $tasks = auth()->user()->tasks()->latest()->get();
        // $tasks = Task::latest()->where('user_id', auth()->id())->get();
        // 위에있는 where('user_id')는 sql query문의 where user_id=1; 과 같은 의미이다.
        return view('tasks.index',[
            'tasks'=>$tasks
        ]);
    }


    public function create(){
        return view('tasks.create');
    }


    public function store(){
        // validate는 required ,, 프론트엔드에서 삭제되는 걸 대신해서 해줌
        request()->validate([
            'title'=>'required',
            'body'=>'required'
        ]);

            $values = request(['title','body']);
            $values['user_id'] = auth()->id();

         $task = Task::create($values);
        //     [
        //    'title' => request('title'),
        //    'body' => request('body')
        // ]
        return redirect('/tasks/'. $task->id );
    }


    public function show(Task $task){
        // task 권한, url로 넘어가는 것을 방지할 수 있음
                    // if(auth()->id() != $task->user_id){
                        // abort(403);
                    // }
        // ↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑ 이거를 한 줄로 표현 가능 밑과 같이
         abort_if(auth()->id() != $task->user_id, 403);
         // 이것도 동일, 대신에 user.php에다가 코드를 짜줘야함
         // abort_if(!auth()->user()->owns($task), 403);
         // abort_unless(auth()->user()->owns($task), 403);

        return view('tasks.show', [
            'task'=>$task
        ]);
    }

    
    public function edit(Task $task){
        abort_if(auth()->id() != $task->user_id, 403);

        return view('tasks.edit',[
            'task'=>$task
        ]);
    }


    public function update(Task $task){
        request()->validate([
            'title'=>'required',
            'body'=>'required',
        ]);
        $task->update(request(['title','body']));
        return redirect('/tasks/'. $task->id );
    }

    public function destroy(Task $task){
        
        $task->delete();
        return redirect('/tasks');
    }











}
