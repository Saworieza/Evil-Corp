<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class MailerCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'linkr:sendmail';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Send pending emails';

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

        $mails = DB::table('mailer')
            ->where('try', '<', '10')
            ->orderBy('to')
            ->orderBy('created_at')
            ->get();

        $messages =[];
        $i=0;

        while($i < count($mails))
        {
            $to = $mails[$i]->to;
            $msg = [];
            $ids = [];
            while ( $i < count($mails) && $mails[$i]->to == $to)
            {
                $msg[] = $mails[$i]->body;
                $ids[] = $mails[$i]->id;
                $i++;
            }

            $messages[] = ['to'=>$to, 'msgs'=>$msg, 'ids'=>$ids, 'sent'=>false];
        }

        foreach($messages as &$message)
        {

            try
            {
                 $view = checkForCustomView('emails.notification');

                 Mail::send($view, ["messages"=>$message['msgs'], "msgs"=>$message['msgs']], function($mail) use ($message)
                   {
                        $mail->to($message['to'])
                            ->subject(trans('email.notifications', ['site_name'=> Config::get('app.app_title', 'Linkr')]));

                   });

                 $message['sent'] = true;
            } catch (Exception $e) {
                $this->error("Error sendig email to: ".$message['to']);
                $this->error($e->getMessage());
            }


        }

        if( count(Mail::failures()) > 0 )
        {
            foreach(Mail::failures() as $email_address) {
                foreach($messages as &$message){
                    if($message['to'] == $email_address) $message['sent'] = false;
                }
            }

        }

        foreach($messages as $message)
        {
            if($message['sent'])
            {
                DB::table('mailer')->whereIn('id', $message['ids'])->delete();
            } else {
                DB::table('mailer')->whereIn('id', $message['ids'])->increment('try');
            }
        }


	}



}
