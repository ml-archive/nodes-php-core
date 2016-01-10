<?php
namespace Nodes\Console;

use App\Console\Kernel as AppKernel;

class Kernel extends AppKernel
{
    public function __construct(\Illuminate\Contracts\Foundation\Application $app, \Illuminate\Contracts\Events\Dispatcher $events)
    {
        parent::__construct($app, $events);
        dd(':D');
    }

    public function handle($input, $output = null)
    {
        try {

            $this->bootstrap();

            return $this->getArtisan()->run($input, $output);
        } catch (Exception $e) {
            $this->reportException($e);

            $this->renderException($output, $e);

            return 1;
        } catch (Throwable $e) {
            $e = new FatalThrowableError($e);

            $this->reportException($e);

            $this->renderException($output, $e);

            return 1;
        }
    }

    protected function getArtisan()
    {
        dd('lol');
    }
}