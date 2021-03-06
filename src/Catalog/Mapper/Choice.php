<?php

namespace Catalog\Mapper;

use Zend\Stdlib\Hydrator\HydratorInterface;

class Choice extends AbstractMapper
{
    protected $tableName = 'catalog_choice';
    protected $relationalModel = '\Catalog\Model\Choice\Relational';
    protected $dbModel = '\Catalog\Model\Choice';
    protected $key = array('choice_id');

    public function find(array $data)
    {
        $select = $this->getSelect()
            ->where(array('choice_id' => (int) $data['choice_id']));
        return $this->selectOne($select);
    }

    public function getByOptionId($optionId)
    {
        $select = $this->getSelect()
            ->where(array('option_id' => (int) $optionId));
        return $this->selectMany($select);
    }

    public function insert($choice, $tableName=null, HydratorInterface $hydrator=null)
    {
        $choiceId = parent::insert($choice, $tableName, $hydrator);
        return $this->find(array('choice_id' => $choiceId));
    }

    public function addOption($choiceId, $optionId)
    {
        $table = 'catalog_choice_option';
        $row = array('choice_id' => $choiceId, 'option_id' => $optionId);
        $select = $this->getSelect($table)
            ->where($row);
        $result = $this->query($select);
        if (false === $result) {
            $this->insert($row, $table);
        }
    }
}
