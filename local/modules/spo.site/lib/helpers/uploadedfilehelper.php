<?php
namespace Spo\Site\Helpers;

use Bitrix\Main;
use CFile;


class UploadedFileHelper
{
    const MODULE_ID = 'spo.site'; //todo перенести в другое место, вероятно, лучше в define

    public $uploadedFilesArray = array();

    public function __construct($key = false)
    {
        $this->uploadedFilesArray = $key ? $_FILES[$key] : $_FILES;
    }

    public function tryUploadFile($key)
    {
        $ufa = $this->uploadedFilesArray;

        $fileData = array(
            'type'      => $ufa['type'][$key],
            'tmp_name'  => $ufa['tmp_name'][$key],
            'size'      => $ufa['size'][$key],
            'name'      => $ufa['name'][$key],
            'del'       => 'Y',
            'MODULE_ID' => self::MODULE_ID,
        );

        if ($fileData['size'] === 0)
        {
            return null;
        }

//        if ($fileData['error'] === 0)
//        {
//            return 'error';
//        }

        return $this->uploadFile($fileData);
    }

    public function uploadFile($fileData)
    {
        //$fileData['old_file'] = $this->entity->getAbiturientProfile()->getIdentityDocumentScanFile();
        $fileId = CFile::SaveFile($fileData, 'abiturient');

        if(empty($fileId)){
            return null;
        }
        return intval($fileId);
    }

    public function hasFiles()
    {
        // если хоть один файл имеет размер отличный от 0, то файлы есть
        foreach($this->uploadedFilesArray['size'] as $fileSize){
            if($fileSize > 0)
            {
                return true;
            }
        }
        return false;
    }
}