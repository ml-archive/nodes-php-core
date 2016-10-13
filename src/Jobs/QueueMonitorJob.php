<?php
declare(strict_types=1);

namespace Nodes\Jobs;

use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class QueueMonitorJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $queueName;

    /**
     * QueueMonitorJob constructor
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @param string $queueName
     */
    public function __construct(string $queueName)
    {
        $this->queueName = $queueName;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        (new Client())->patch('https://nstack2.like.st/api/queues/monitors/' . $this->queueName);
    }
}
