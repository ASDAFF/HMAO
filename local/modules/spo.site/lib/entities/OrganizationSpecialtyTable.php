<?php
namespace Spo\Site\Entities;

use Bitrix\Main\Entity;


/**
 * @Entity(repositoryClass="Spo\Site\Doctrine\Repositories\OrganizationSpecialtyRepository")
 * @Table(name="spo_organization_specialty")
 *
 * Class OrganizationSpecialty - направления  подготовки организации
 * (специальность, форма обучения, базовое образование)
 */
class OrganizationSpecialtyTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'spo_organization_specialty';
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField(
                'ORGANIZATION_SPECIALTY_ID',
                array(
                    'primary' => true,
                    'autocomplete' => true,
                    'column_name' => 'organization_specialty_id',
                    'unique' => true
                )
            ),
            new Entity\IntegerField(
                'ORGANIZATION_ID',
                array(
                    'required' => true,
                    'column_name' => 'organization_id',
                )
            ),
            new Entity\StringField(
                'IDSPECIALIZATION',
                array(
                    'column_name' => 'idspecialization',
                )
            ),
            new Entity\StringField(
                'NAMESPECIALIZATION',
                array(
                    'column_name' => 'namespecialization',
                )
            ),
            new Entity\StringField(
                'IDPROGRAM',
                array(
                    'column_name' => 'idprogram',
                )
            ),
            new Entity\ReferenceField(
                'ORGANIZATION',
                'Spo\Site\Entities\OrganizationTable',
                array(
                    '=this.ORGANIZATION_ID' => 'ref.ORGANIZATION_ID'
                )
            ),
            new Entity\IntegerField(
                'SPECIALTY_ID',
                array(
                    'required' => true,
                    'column_name' => 'specialty_id'
                )
            ),
            new Entity\IntegerField(
                'TARGETDIRECTION',
                array(
                   // 'required' => true,
                    'column_name' => 'targetdirection'
                )
            ),
            new Entity\ReferenceField(
                'SPECIALITY',
                'Spo\Site\Entities\SpecialtyTable',
                array(
                    '=this.SPECIALTY_ID' => 'ref.SPECIALTY_ID'
                )
            ),
            new Entity\IntegerField(
                'ORGANIZATION_SPECIALTY_STUDY_MODE',
                array(
                    'required' => true,
                    'column_name' => 'organization_specialty_study_mode'
                )
            ),
            new Entity\IntegerField(
                'ORGANIZATION_SPECIALTY_BASE_EDUCATION',
                array(
                    'required' => true,
                    'column_name' => 'organization_specialty_base_education'
                )
            ),
            new Entity\IntegerField(
                'ORGANIZATION_SPECIALTY_TRAINING_LEVEL',
                array(
                    'required' => true,
                    'column_name' => 'organization_specialty_training_level'
                )
            ),
            new Entity\IntegerField(
                'ORGANIZATION_SPECIALTY_TRAINING_TYPE',
                array(
                    'required' => true,
                    'column_name' => 'organization_specialty_training_type'
                )
            ),
            new Entity\IntegerField(
                'ORGANIZATION_SPECIALTY_STUDY_PERIOD',
                array(
                    'required' => true,
                    'column_name' => 'organization_specialty_study_period'
                )
            ),
            new Entity\IntegerField(
                'ORGANIZATION_SPECIALTY_PLANNED_ABITURIENTS_NUMBER',
                array(
                    'required' => true,
                    'column_name' => 'organization_specialty_planned_abiturients_number'
                )
            ),
            new Entity\IntegerField(
                'ORGANIZATION_SPECIALTY_PLANNED_GROUPS_NUMBER',
                array(
                    'required' => true,
                    'column_name' => 'organization_specialty_planned_groups_number'
                )
            ),
            new Entity\IntegerField(
                'ORGANIZATION_SPECIALTY_STATUS',
                array(
                    'required' => true,
                    'column_name' => 'organization_specialty_status'
                )
            ),
            new Entity\ReferenceField(
                'APPLICATION',
                'Spo\Site\Entities\ApplicationTable',
                array(
                    '=this.ORGANIZATION_SPECIALTY_ID' => 'ref.ORGANIZATION_SPECIALTY_ID'
                )
            ),
            new Entity\ReferenceField(
                'ORGANIZATION_SPECIALTY_EXAM',
                'Spo\Site\Entities\OrganizationSpecialtyExamTable',
                array(
                    '=this.ORGANIZATION_SPECIALTY_ID' => 'ref.ORGANIZATION_SPECIALTY_ID'
                )
            ),
            new Entity\ReferenceField(
                'ORGANIZATION_SPECIALTY_ADAPTATION',
                'Spo\Site\Entities\OrganizationSpecialtyAdaptationTable',
                array(
                    '=this.ORGANIZATION_SPECIALTY_ID' => 'ref.ORGANIZATION_SPECIALTY_ID'
                )
            ),
            new Entity\ReferenceField(
                'ADMISSION_PLAN',
                'Spo\Site\Entities\AdmissionPlanTable',
                array(
                    '=this.ORGANIZATION_SPECIALTY_ID' => 'ref.ORGANIZATION_SPECIALTY_ID'
                )
            ),
            new Entity\ReferenceField(
                'QUALIFICATION2ORGANIZATIONSPECIALTYTABLE',
                'Spo\Site\Entities\Qualification2OrganizationSpecialtyTable',
                array(
                    '=this.ORGANIZATION_SPECIALTY_ID' => 'ref.ORGANIZATION_SPECIALTY_ID'
                )
            ),
        );
    }
}