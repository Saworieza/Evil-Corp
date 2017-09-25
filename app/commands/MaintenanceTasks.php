<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class MaintenanceTasks extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'linkr:maintenance';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Maintenance tasks';

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
        // delete interrupted uploads
        $files = Attachment::where('attachable_id',0)
            ->where('created_at', '<', date( 'Y-m-d H:i:s', strtotime("-30 days") ))
            ->get();

        foreach($files as $file)
        {
            $fileName = filesFolder().$file->code.'_'.$file->file_name;
            if(file_exists($fileName))
                unlink($fileName);

            $file->delete();
        }

        // delete read messages >30d old
        $rows = UserMessage::where('read',1)->where('created_at', '<',  date( 'Y-m-d H:i:s', strtotime("-30 days")))->delete();

        $rows = Chat::where('created_at', '<',  date( 'Y-m-d H:i:s', strtotime("-2 days")))->delete();

        Artisan::call('cache:clear');
        $this->info('maintenance tasks ok');
	}



}
