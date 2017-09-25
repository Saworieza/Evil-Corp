<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class UpgradeCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'linkr:upgrade';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Run this command after an application upgrade';

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
	 * @return mixed
	 */
	public function fire()
	{
        // new vars?

        Artisan::call('migrate', array('--force' => true));
        $this->info('App upgraded.');
	}



}
