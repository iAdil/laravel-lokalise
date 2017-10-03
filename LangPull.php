<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Config;
use ZipArchive;

class LangPull extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lang:pull';

    protected $apikey = 'YOUR_API_KEY';
    protected $project = 'YOUR_PROJECT_ID';

    protected $tmpFileName = 'LocaliseArchive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return string
     */
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
        $response = $client->request('POST', 'https://api.lokalise.co/api/project/export', [
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
                    'name'     => 'type',
                    'contents' => 'php'
                ],
                [
                    'name'     => 'export_empty',
                    'contents' => 'base'
                ],
                [
                    'name'     => 'use_original',
                    'contents' => '1'
                ],
            ]
        ]);
        $body = $response->getBody();

        $details = json_decode($body);
        $file = $details->bundle->file;

        $remoteArchive = 'https://lokalise.co/'.$file;

        $langFilesArchive = $this->getRoot().'/storage/'.$this->tmpFileName.'.zip';

        // copy archive
        $this->copyArchive($remoteArchive, $langFilesArchive);

        // extract files
        $this->extractFiles($langFilesArchive);

        // delete tmp archive
        unlink($langFilesArchive);

    }


    /**
     * @param $remoteArchive
     * @param $langFilesArchive
     */
    protected function copyArchive($remoteArchive, $langFilesArchive)
    {
        if (!copy($remoteArchive, $langFilesArchive)) {
            print "Failed to copy $remoteArchive...".PHP_EOL;
            exit;
        }else{
            print "Localise file impoted".PHP_EOL;
        }
    }

    /**
     * @param $langFilesArchive
     */
    protected function extractFiles($langFilesArchive)
    {
        $zip = new ZipArchive;


        if ($zip->open($langFilesArchive) === TRUE) {
            $zip->extractTo($this->getRoot().'/resources/lang/');
            $zip->close();
            print "Files updated".PHP_EOL;
        } else {
            print "Error extract files".PHP_EOL;
        }
    }
}