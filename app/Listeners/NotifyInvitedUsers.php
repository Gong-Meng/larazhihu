<?php

namespace App\Listeners;

use App\Events\PublishQuestion;
use App\Models\User;
use App\Notifications\YouWereInvited;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyInvitedUsers
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\PublishQuestion  $event
     * @return void
     */
    public function handle(PublishQuestion $event)
    {
        //
        User::whereIn('name', $event->question->invitedUsers())
            ->get()
            ->each(function ($user) use ($event){
                $user->notify(new YouWereInvited($event->question));
            });
    }
}
