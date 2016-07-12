<?php

use Spo\Site\Doctrine\Entities\SpecialtyGroup;
use Spo\Site\Doctrine\Entities\Specialty;
use Spo\Site\Doctrine\Entities\Qualification;
use Spo\Site\Doctrine\Entities\Qualification2Specialty;

// Коды укрупненных групп профессий. Коды профессий; Наименования укрупненных групп профессий. Наименования профессий; Квалификация(и) квалифицированного рабочего и служащего

$_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__) . '/../../../../../');
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];

require($DOCUMENT_ROOT . '/bitrix/modules/main/include/prolog_before.php');


if (!CModule::IncludeModule('spo.site')) {
    die('spo.site module not found');
}

if (($csvFile = fopen('specialties.csv', 'r')) == false)
    die('file opening error');

const TYPE_SPECIALTY = 1;
const TYPE_SPECIALTY_GROUP = 2;
const TYPE_UNKNOWN = 3;


function getDataType(array $data)
{
    // Должно быть три элемента в массиве
    if (count($data) != 3)
        return TYPE_UNKNOWN;

    // Строка с группой специальностей
    if (empty($data[2]) && strpos($data[0], '.00.00'))
        return TYPE_SPECIALTY_GROUP;

    // Строка со специальностью
    if (!empty($data[0]) && !empty($data[1]) && !empty($data[2]))
        return TYPE_SPECIALTY;

    return TYPE_UNKNOWN;
}

function createSpecialtyGroup($data)
{
    $groupTitle = $data[1];
    $groupCode = $data[0];

    $repository = D::$em->getRepository('Spo\Site\Doctrine\Entities\SpecialtyGroup');
    $group = $repository->findOneBy(array(
        'specialtyGroupCode' => $groupCode,
    ));

    // Если такой группы специальностей нет - создадим её
    if (!$group) {
        $group = new SpecialtyGroup();
        $group->setCode($groupCode)->setTitle($groupTitle);
        D::$em->persist($group);
        D::$em->flush($group);
    }
}

function createSpecialty($data)
{
    $specialtyCode = $data[0];
    $specialtyTitle = $data[1];

    $specialtyRepository = D::$em->getRepository('Spo\Site\Doctrine\Entities\Specialty');
    $specialty = $specialtyRepository->findOneBy(array('specialtyCode' => $specialtyCode));

    // Находим группу для данной специальности
    $groupCode = substr($specialtyCode, 0, 2) . '.00.00';
    $specialtyGroupRepository = D::$em->getRepository('Spo\Site\Doctrine\Entities\SpecialtyGroup');
    $specialtyGroup = $specialtyGroupRepository->findOneBy(array(
        'specialtyGroupCode' => $groupCode,
    ));

    if (!$specialtyGroup) {
        echo 'SpecialtyGroup ' . $groupCode . ' not found for specialty ' . $specialtyCode . PHP_EOL;
        return false;
    }

    if (!$specialty) {
        $specialty = new Specialty();
        $specialty->setCode($specialtyCode)->setTitle($specialtyTitle)->setDescription('');
    }

    $specialty->setSpecialtyGroup($specialtyGroup);
    D::$em->persist($specialty);
    D::$em->flush($specialty);

    return $specialty;
}

function bindQualificationsToSpecialty(Specialty $specialty, $qualificationsString)
{
    // Строку вида "Квалификация1 Квалификация вторая Квалификация3 ..." разбиваем на элементы
    $qualificationString = preg_replace('/(\s+)([А-Я])/u', ';${2}', $qualificationsString);
    $qualificationsTitleArray = explode(';', $qualificationString);
    $qualificationModelsArray = array();

    foreach ($qualificationsTitleArray as $qualificationTitle) {
        $qualificationRepository = D::$em->getRepository('Spo\Site\Doctrine\Entities\Qualification');
        $qualificationModel = $qualificationRepository->findOneBy(array('qualificationTitle' => $qualificationTitle));

        if (!$qualificationModel) {
            $qualificationModel = new Qualification();
            $qualificationModel->setTitle($qualificationTitle);
            D::$em->persist($qualificationModel);
            D::$em->flush($qualificationModel);
        }

        $qualificationModelsArray[] = $qualificationModel;
    }

    // Проверяем, все ли квалификации привязаны к данной специальности. Если нет - создаём нужные связи
    $qualification2SpecialtyRepository = D::$em->getRepository('Spo\Site\Doctrine\Entities\Qualification2Specialty');

    foreach ($qualificationModelsArray as $qualification) {
        /** @var Qualification $qualification */
        $relation = $qualification2SpecialtyRepository->find(array(
            'qualificationId' => $qualification->getId(),
            'specialtyId' => $specialty->getId(),
        ));

        if (!$relation) {
            $relation = new Qualification2Specialty($specialty->getId(), $qualification->getId());
            $relation->setQualification($qualification)->setSpecialty($specialty);
            D::$em->persist($relation);
            D::$em->flush($relation);
        }
    }
}


while (($data = fgetcsv($csvFile, null, ';')) !== false ) {
    $dataType = getDataType($data);

    switch ($dataType) {
        case TYPE_UNKNOWN:
            echo 'Unknown data type' . PHP_EOL;
            \Spo\Site\Util\CVarDumper::dump($data, 100, false);
            echo PHP_EOL;
            break;
        case TYPE_SPECIALTY_GROUP:
            createSpecialtyGroup($data);
            break;
        case TYPE_SPECIALTY:
            $specialty = createSpecialty($data);

            if ($specialty) {
                bindQualificationsToSpecialty($specialty, $data[2]);
            }

            break;
    }


}


