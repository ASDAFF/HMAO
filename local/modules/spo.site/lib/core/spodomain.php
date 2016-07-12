<?php
namespace Spo\Site\Core;

use Bitrix\Main\SystemException;
use D;
use Doctrine\ORM\Mapping\Entity;
use Spo\Site\Util\CVarDumper;
use Spo\Site\Doctrine\Entities as SpoEntities;
use Symfony\Component\Validator\Constraints\BlankValidator;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\ConstraintViolation;
use Doctrine\DBAL\DBALException;

class SPODomain
{
	protected $entity = null;
	protected $entityCollection = null;
	protected $totalCount = 0;
    protected $flushableDoctrineEntityCollection = array();

	public $errors = array();

	public function __construct($entity = null, $entityCollection = null, $totalCount = 0)
	{
		// TODO можно сделать контроль над типами entity и элементами entityCollection, если унаследовать все
		// TODO сущности от базового класса SPOEntity. Время покажет, нужно ли.

		$this->entity = $entity;
		$this->entityCollection = $entityCollection;
        $this->totalCount = $totalCount;
	}

	public function getModel()
	{
		return $this->entity;
	}

	public function getEntityCollection()
	{
		return $this->entityCollection;
	}

    public function getTotalCount()
    {
        return $this->totalCount;
    }

    protected function removeEntity($entity)
    {
        //D::$em->remove($entity);
        $this->flushableDoctrineEntityCollection[] = $entity;
    }

    protected function persistEntity($entity)
    {
        //D::$em->persist($entity);

        // если элемент уже присутствует в массиве, то повторно его не добавляем
        if(array_search($entity, $this->flushableDoctrineEntityCollection, true) !== false)
        {
            return;
        }

        $this->flushableDoctrineEntityCollection[] = $entity;
    }

	public function save()
	{
        try
        {
            //D::$em->flush($this->flushableDoctrineEntityCollection);
            //$this->flushableDoctrineEntityCollection = array();
            return true;
        }
        catch(DBALException $ex)
        {
            $this->addError($ex->getMessage());
            return false;
        }
	}

	/**
	 * @param $message
	 * @param string $entityPropertyName
	 * @param string $entityName
	 */
	public function addError($message,  $entityName = '', $entityPropertyName = '')
	{
		$this->errors[] = array(
			'message' => $message,
			'property' => $entityPropertyName,
			'entity' => $entityName,
		);
	}

	public function validate()
	{
		/*foreach ($this->flushableDoctrineEntityCollection as $entity)
		{
			$errors = D::$v->validate($entity);
			$result = array();
			if ($errors->count()) {
				// @var ConstraintViolation $error
				foreach ($errors as $error) {
					$result[] = array(
						'message' => $error->getMessage(),
						'property' => $error->getPropertyPath(),
						'entity' => self::get_real_class($entity),
					);
				}

				$this->errors = array_merge($this->errors, $result);
			}
		}*/

		if (!empty($this->errors))
			return false;

		return true;
	}

    public function remove()
    {
        $this->removeEntity($this->getModel());
    }

	/**
	 * Obtains an object class name without namespaces
	 */
	private static function get_real_class($obj) {
		$className = get_class($obj);

		if (preg_match('@\\\\([\w]+)$@', $className, $matches)) {
			$className = $matches[1];
		}

		return $className;
	}

	public function getErrors()
	{
		return $this->errors;
	}

}