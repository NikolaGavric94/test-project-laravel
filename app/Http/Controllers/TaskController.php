<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTaskRequest;
use App\Http\Response\HttpResponse;
use App\Mail\TaskCreated;
use App\Models\Task;
use App\Models\User;
use App\Utils\Constants;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TaskController extends Controller
{
    public function create(CreateTaskRequest $request) {
        $date = $request->get('date');
        $time = $request->get('time');
        $mailableAt = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $time);
        $task = new Task;
        $task->description = $request->get('description');
        $task->mailable_at = $mailableAt;
        /** @var Collection $users */$users = User::where('role', Constants::USER_ROLE_USER)->get();
        $task->save();
        $task->users()->attach($users->pluck('id'));

        $user = $users->take(1);
        $users = $users->diff($user);

        $diffInMinutes = $mailableAt->diffInMinutes(now());
        // After email is sent we will have a listener for email sent event which will create a job
        // Idea is after mail is sent we will queue a job to run in now()->addMinutes(60)
        // The queue worker will pick it up in 60 minutes and check if the task
        // has been marked completed, if not, it will set completed to false
        Mail::to($user)
            ->bcc($users)
            ->later(now()->addMinutes($diffInMinutes), (new TaskCreated($task))->onQueue('emails'));

        return HttpResponse::response('Success', ['diff' => $diffInMinutes]);
    }

    // Written here for the functionality, this can be pulled into another layer
    public function top10ThisWeek() {
        $top10Users = User::where('role', Constants::USER_ROLE_USER)->withCount(['tasks' => function($query) {
            $query->where('task_user.finished', true)
                ->where('task_user.completed', true)
                ->where('task_user.created_at', '>=', Carbon::now()->subDays(Carbon::now()->dayOfWeek)->toDateString());
        }])->orderBy('tasks_count', 'desc')
        ->get()
        ->reject(function($user) {
            return $user->tasks_count === 0;
        })
        ->map(function($user) {
            return '[Id: ' . $user->id . ' User: ' . $user->email . ' count: ' . $user->tasks_count . '] ';
        });

        Log::channel('debug')->info('users: ' . $top10Users);
    }
}
