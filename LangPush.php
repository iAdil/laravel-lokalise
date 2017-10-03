<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;

class LangPush extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lang:push';

    protected $apikey = 'YOUR_API_KEY';
    protected $project = 'YOUR_PROJECT_ID';

    protected $langDir = './resources/lang';

    protected $langs = [
        'ru' =>[
            'code' => 'ru'
        ],
        'az' =>[
            'code' => 'az'
        ],
        'en' =>[
            'code' => 'en'
        ],

    ];
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send english translation to lokali.se';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->getLangFilesList();
    }

    protected function getRoot()
    {
        return dirname(__FILE__).'/../../../';
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $client = new \GuzzleHttp\Client();

        foreach($this->files as $langCode => $langFiles){
            foreach($langFiles as $file){
                $fileContent = file_get_contents($file['dirname'].'/'.$file['basename']);
                $response = $client->request('POST', 'https://api.lokalise.co/api/project/import', [
                    'multipart' => [
                        [
                            'name'     => 'api_token',
                            'contents' => $this->apikey
                        ],
                        [
                            'name'     => 'id',
                            'contents' => $this->project
                        ],
                        [
                            'name'     => 'replace',
                            'contents' => '0'
                        ],
                        [
                            'name'     => 'distinguish',
                            'contents' => '1'
                        ],
                        [
                            'name'     => 'lang_iso',
                            'contents' => $langCode
                        ],
                        [
                            'name'     => 'file',
                            'contents' => $fileContent,
                            'filename' => $file['basename'],
                        ],
                    ]
                ]);
                $body = $response->getBody();

                print 'Lang: '.$langCode.PHP_EOL;
                print 'File: '.$file['basename'].PHP_EOL;
                print 'Result: '.$body.PHP_EOL;
            }
        }
    }

    /**
     * get all lang files from dir
     */
    protected function getLangFilesList()
    {
        // prepare files path
        $pullPath = $this->getRoot().$this->langDir.'/';

        // get files info
        foreach ($this->langs as $lang_code => $lang_value){
            $filesList = \File::files($pullPath.$lang_code);
            foreach ($filesList as $file){
                $this->files[$lang_code][] = pathinfo($file);
            }
        }
    }
}