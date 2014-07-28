<?php
/**
 * Created by PhpStorm.
 * User: kirill
 * Date: 02.12.13
 * Time: 16:37
 */

namespace System\Log;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

/**
 * Запись логов в еластик
 *
 * @package System\Log
 */
class EmlsElasticHandler extends AbstractProcessingHandler
{
    protected $format;
    protected $path;

    protected $handle;
    protected $token;

    protected $log_dir;
    protected $stream;

    function __construct($format = 'Y-m-d', $path = 'call_action', $level = Logger::DEBUG, $bubble = false)
    {
        $this->format = $format;
        $this->path = $path;
        $this->token = uniqid();

        parent::__construct($level, $bubble);
    }

    /**
     *
     * @param mixed $log_dir
     */
    public function setLogDir($log_dir)
    {
        $this->log_dir = $log_dir;
    }

    /**
     * Writes the record down to the log of the implementing handler
     *
     * @param  array $record
     * @return void
     */
    protected function write(array $record)
    {
        $message = $record['message'];
        if(!@iconv('utf-8', 'utf-8', $message)){
            $message = @iconv('windows-1251', 'utf-8', $message);
        }

        $data_array = [
            'message' => $message,
            'level' => $record['level'],
            'datetime' => $record['datetime']->format('Y-m-d\TH:i:s'),
            'token' => $this->token,
        ];

        $data_array += $record['context'];

        if($record['extra']){
            $data_array += array('extra' => $record['extra']);
        }

        $row_data = @json_encode($data_array);

        if($row_data){
            $this->failOver($record['datetime'], $record['channel'], $row_data);
        }
    }

    /**
     * FailOver for messages
     * @param \DateTime $date
     * @param $channel
     * @param $message
     */
    protected function failOver(\DateTime $date, $channel, $message)
    {
        if(null === $this->stream){
            if($this->log_dir){
                $file = $this->log_dir.'/emls-call-info-'.date($this->format).'.log';
                $this->stream = @fopen($file , 'a');
            }
        }

        if(is_resource($this->stream)){
            $bulk_data = json_encode(
                [
                    'create' => [
                        '_index' => $date->format($this->format),
                        '_type' => $channel
                    ]
                ])."\n".$message."\n";

            fwrite($this->stream, $bulk_data);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        if(is_resource($this->stream)){
            fclose($this->stream);
        }

        $this->stream = null;
    }

}