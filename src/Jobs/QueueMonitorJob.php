<?php

declare(strict_types=1);

namespace Nodes\Jobs;

use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class QueueMonitorJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * fire.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @param \Illuminate\Contracts\Queue\Job $job
     * @param                                 $queuName
     *
     * @return void
     */
    public function fire(Job $job, $queuName)
    {
        (new Client())->patch('https://nstack.io.st/api/queues/monitors/'.$queuName);

        $job->delete();
    }
}
