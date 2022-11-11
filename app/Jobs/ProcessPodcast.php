<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Role;
use Mail;
class ProcessPodcast implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $role = null;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Role $role)
    {
        $this->role = $role;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::raw('This is a simple text', function ($m) {
            $m->to('reshmi.2033@gmail.com')->subject('Email Subject');
          });
    }
}
