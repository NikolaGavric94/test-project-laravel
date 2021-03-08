<?php

namespace App\Mail;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TaskCreated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The task instance.
     *
     * @var \App\Models\Task
     */
    public $task;

    /**
     * Create a new message instance.
     *
     * @param $task Task
     * @return void
     */
    public function __construct($task)
    {
        $this->task = $task;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.tasks.created');
    }
}
