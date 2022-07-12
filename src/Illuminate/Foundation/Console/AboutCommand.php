<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Composer;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'about')]
class AboutCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'about';

    /**
     * The name of the console command.
     *
     * This name is used to identify the command during lazy loading.
     *
     * @var string|null
     *
     * @deprecated
     */
    protected static $defaultName = 'about';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'About your application\'s environment';

    /**
     * The Composer instance.
     *
     * @var \Illuminate\Support\Composer
     */
    protected $composer;

    /**
     * The data to display.
     *
     * @var array
     */
    protected static $data = [];

    /**
     * Create a new command instance.
     *
     * @param  \Illuminate\Support\Composer  $composer
     * @return void
     */
    public function __construct(Composer $composer)
    {
        parent::__construct();

        $this->composer = $composer;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->gatherEnvironmentData();
        $this->gatherDriverData();

        collect(static::$data)->sortBy(function ($data, $key) {
            $index = array_search($key, ['Environment', 'Drivers']);

            if ($index === false) {
                return 99;
            }

            return $index;
        })
        ->each(function ($data, $section) {
            $this->newLine();
            $this->components->twoColumnDetail('  <fg=green;options=bold>'.$section.'</>');

            sort($data);

            foreach ($data as $detail) {
                [$label, $value] = $detail;

                $this->components->twoColumnDetail($label, $value);
            }
        });

        return 0;
    }

    /**
     * Gather data about the application's environment.
     *
     * @return array
     */
    protected function gatherEnvironmentData()
    {
        static::add('Environment', [
            'Laravel Version' => app()->version(),
            'PHP Version' => phpversion(),
            'Composer Version' => $this->composer->getVersion() ?? '<fg=yellow;options=bold>-</>',
            'Environment' => app()->environment(),
            'App Debug' => config('app.debug') ? '<fg=yellow;options=bold>ENABLED</>' : 'DISABLED',
            'App Name' => config('app.name'),
            'App URL' => config('app.url'),
            'App Root' => $this->laravel->basePath(),
            'Web Root' => $this->laravel->make('path.public'),
        ]);
    }

    /**
     * Gather data about the drivers configured in the application.
     *
     * @return array
     */
    protected function gatherDriverData()
    {
        static::add('Drivers', array_filter([
            'Broadcasting' => config('broadcasting.default'),
            'Database' => config('database.default'),
            'Mail' => config('mail.default'),
            'Octane' => config('octane.server'),
            'Queue' => config('queue.default'),
            'Session' => config('session.driver'),
            'Scout' => config('scout.driver'),
        ]));
    }

    /**
     * Add additional data to the output.
     *
     * @param  string|array  $data
     * @param  string|null  $value
     * @return void
     */
    public static function add(string $section, $data, string $value = null)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                self::$data[$section][] = [$key, $value];
            }
        } else {
            self::$data[$section][] = [$data, $value];
        }
    }
}
