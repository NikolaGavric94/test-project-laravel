<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTaskRequest;
use App\Mail\TaskCreated;
use App\Models\Task;
use App\Models\User;
use App\Utils\Constants;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Mail;

class TaskController extends Controller
{
    public function create(CreateTaskRequest $request) {
        $date = $request->get('date');
        $time = $request->get('time');
        $mailableAt = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $time);
        $task = new Task([$request->get('description')]);
        $task->mailable_at = $mailableAt;
        /** @var Collection $users */$users = User::where('role', Constants::USER_ROLE_USER)->get();
        $task->users()->attach($users->pluck('id'));
        $task->save();

        $user = $users->take(1);
        $users = $users->diff($user);
        Mail::to($user)
            ->bcc($users)
            ->later($mailableAt, (new TaskCreated($task))->onQueue('emails'));
    }
}
