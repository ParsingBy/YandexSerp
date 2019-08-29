<?php
namespace ParsingBy\YandexSerp\Console;

use App\Console\Kernel as ConsoleKernel;
use Illuminate\Console\Scheduling\Schedule;
use ParsingBy\YandexSerp\YandexSerp;
use ParsingBy\YandexSerp\YandexSerpJobs;

class Kernel extends ConsoleKernel
{
    /**
     * Define the package's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        parent::schedule($schedule);

        $schedule->call(function () {
            (new YandexSerp)->doCreatePagesToParse();
        })->name('ProxyManager_YandexSerp_doCreatePagesToParse')->everyMinute()->withoutOverlapping();

        $schedule->call(function () {
            (new YandexSerpJobs)->doParsePages();
        })->name('ProxyManager_YandexSerpJobs_doParsePages')->everyMinute()->withoutOverlapping();
    }
}