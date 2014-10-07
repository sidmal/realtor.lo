<?php
/**
 * Created by PhpStorm.
 * User: dmitriysinichkin
 * Date: 01.10.14
 * Time: 13:00
 */

namespace Realtor\CallBundle\Command;

use Realtor\CallBundle\Entity\AtsCallData;
use Realtor\CallBundle\Exceptions\AtsCallDataException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AtsDataLoadCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('ats:data:load')
            ->setDescription('Загрузка данных о телефонных звонках со стороны АТС.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $environment = $this->getContainer()->get('kernel')->getEnvironment();

        $dataForLoad = $em->getRepository('CallBundle:Call')->getUniqueLinkedId();

        foreach($dataForLoad as $item){
            /** @var \Realtor\CallBundle\Entity\Call $item */
            if(in_array($environment, ['dev', 'test']) && !in_array($item->getId(), [4378, 4369, 4376, 4379, 4380])){
                continue;
            }

            if(!$atsData = $this->getAtsCallData($item->getLinkedId())){
                continue;
            }

            foreach($atsData as $atsDataItem) {
                $atsCallData = new AtsCallData();

                $atsCallData->setNrec($atsDataItem['nrec']);
                $atsCallData->setCallDate(new \DateTime($atsDataItem['calldate']));
                $atsCallData->setCallTime(new \DateTime($atsDataItem['calldate']));
                $atsCallData->setClid($atsDataItem['clid']);
                $atsCallData->setSrc($atsDataItem['src']);
                $atsCallData->setDst($atsDataItem['dst']);
                $atsCallData->setDcontext($atsDataItem['dcontext']);
                $atsCallData->setChannel($atsDataItem['channel']);
                $atsCallData->setDstchannel($atsDataItem['dstchannel']);
                $atsCallData->setLastapp($atsDataItem['lastapp']);
                $atsCallData->setLastdata($atsDataItem['lastdata']);
                $atsCallData->setDuration($atsDataItem['duration']);
                $atsCallData->setBillsec($atsDataItem['billsec']);
                $atsCallData->setDisposition($atsDataItem['disposition']);
                $atsCallData->setAmaflags($atsDataItem['amaflags']);
                $atsCallData->setAccountcode($atsDataItem['accountcode']);
                $atsCallData->setUniqueid($atsDataItem['uniqueid']);
                $atsCallData->setUserfield($atsDataItem['userfield']);

                $atsCallData->setXTag(empty($atsDataItem['x-tag']) ? null : $atsDataItem['x-tag']);
                $atsCallData->setXCid(empty($atsDataItem['x-cid']) ? null : $atsDataItem['x-cid']);
                $atsCallData->setXDid(empty($atsDataItem['x-did']) ? null : $atsDataItem['x-did']);
                $atsCallData->setXDialed(empty($atsDataItem['x-dialed']) ? null : $atsDataItem['x-dialed']);
                $atsCallData->setXSpec(empty($atsDataItem['x-spec']) ? null : $atsDataItem['x-spec']);
                $atsCallData->setXInsecure(empty($atsDataItem['x-insecure']) ? null : $atsDataItem['x-insecure']);
                $atsCallData->setXResult(empty($atsDataItem['x-result']) ? null : $atsDataItem['x-result']);
                $atsCallData->setXRecord(empty($atsDataItem['x-record']) ? null : $atsDataItem['x-record']);
                $atsCallData->setXDomain(empty($atsDataItem['x-domain']) ? null : $atsDataItem['x-domain']);
                $atsCallData->setLinkedid($atsDataItem['linkedid']);

                $atsCallData->setCallByApplication($item);

                $em->persist($atsCallData); $em->flush();
            }
        }

        $output->writeln('Загрузка данных окончена.');
    }

    private function getAtsCallData($linkedId)
    {
        if(!$this->getContainer()->hasParameter('ats_call_data_url')){
            throw new AtsCallDataException('Не указан url для загрузки данных о звонках на стороне АТС.');
        }

        if(!$this->getContainer()->hasParameter('ats_call_data_basic_login')){
            throw new AtsCallDataException('Не указан логин для basic авторизации на стороне АТС.');
        }

        if(!$this->getContainer()->hasParameter('ats_call_data_basic_password')){
            throw new AtsCallDataException('Не указан пароль для basic авторизации на стороне АТС.');
        }

        $url = $this->getContainer()->getParameter('ats_call_data_url');
        $basicLogin = $this->getContainer()->getParameter('ats_call_data_basic_login');
        $basicPassword = $this->getContainer()->getParameter('ats_call_data_basic_password');

        $result = [];
        $fieldIndex = [];

        $requestUrl = str_replace('http://', 'http://'.$basicLogin.':'.$basicPassword.'@', $url);
        $requestParams = ['schema' => 'CALL', 'id' => $linkedId];

        if(!$handle = fopen($requestUrl.'?'.http_build_query($requestParams), 'r')){
            throw new AtsCallDataException('Загрузка CSV с удаленного хоста не удалась.');
        }

        $row = 0;
        while(($line = fgetcsv($handle, null, "\t")) !== false){
            if($row == 0){
                $fieldIndex = $line;
            }
            else{
                $resultItem = [];
                foreach($line as $key => $value){
                    if(!empty($fieldIndex[$key])) {
                        $resultItem[$fieldIndex[$key]] = $value;
                    }
                }
                $result[] = $resultItem;
            }
            $row++;
        }
        fclose($handle);

        return (empty($result) || sizeof($result) <= 0) ? false : $result;
    }
} 