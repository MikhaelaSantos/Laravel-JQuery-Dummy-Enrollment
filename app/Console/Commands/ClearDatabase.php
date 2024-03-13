<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all the data from the database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */

     public function handle()
    {
      if ($this->confirm('This will delete all data from the database. Are you sure?')) {
        $this->call('migrate:refresh', [
            '--seed' => true,
            '--force' => true,
        ]);

        $this->info('Database cleared successfully.');
      } else {
          $this->info('Operation canceled.');
      }

      return 0;
    }
}
