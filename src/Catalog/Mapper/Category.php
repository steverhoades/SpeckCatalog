<?php

namespace Catalog\Mapper;

class Category extends AbstractMapper
{
    protected $tableName = 'catalog_category';
    protected $relationalModel = '\Catalog\Model\Category\Relational';
    protected $dbModel = '\Catalog\Model\Category';
    protected $key = array('category_id');

    public function find(array $data)
    {
        $select = $this->getSelect()
            ->where(array('category_id' => (int) $data['category_id']));
        return $this->selectOne($select);
    }

    public function getChildCategories($parentCategoryId=null, $siteId=1)
    {
        $linker = 'catalog_category_website';
        $table  = $this->getTableName();
        $joinString = $linker . '.category_id = ' . $table . '.category_id';

        $where = $this->where()->equalTo('website_id', $siteId);
        if (null === $parentCategoryId) {
            $where->isNull('parent_category_id');
        } else {
            $where->equalTo('parent_category_id', $parentCategoryId);
        }

        $query = $this->getSelect()
            ->join($linker, $joinString)
            ->where($where);
        return $this->selectMany($query);
    }

    //this method requires no linkers are orphaned.
    public function getCrumbs($category, $crumbs=array())
    {
        $crumbs[] = $category->getName();

        $linkerTable = 'catalog_category_website';
        $where = array('category_id' => $category->getCategoryId());

        $query = $this->getSelect($linkerTable)
            ->where($where)
            ->limit(1);
        $linker = $this->query($query);

        if ($linker) {
            $parent = $this->find(array('category_id' => $linker['parent_category_id']));
            return $this->getCrumbs($parent, $crumbs);
        }

        return array_reverse($crumbs);
    }

    public function addProduct($categoryId, $productId, $siteId=1)
    {
        $table = 'catalog_category_product';
        $row = array(
            'category_id' => $categoryId,
            'product_id' => $productId,
            'website_id' => $siteId,
        );
        $select = $this->getSelect($table)
            ->where($row);
        $result = $this->query($select);
        if (false === $result) {
            $this->insert($row, $table);
        }
    }

    public function addCategory($parentCategoryId = null, $categoryId, $siteId=1)
    {
        $where = $this->where()
            ->equalTo('website_id', $siteId)
            ->equalTo('category_id', $categoryId);
        if (null === $parentCategoryId) {
            $where->isNull('parent_category_id');
        } else {
            $where->equalTo('parent_category_id', $parentCategoryId);
        }
        $table = 'catalog_category_website';
        $row = array(
            'category_id' => $categoryId,
            'parent_category_id' => $parentCategoryId,
            'website_id' => $siteId,
        );
        $select = $this->getSelect($table)
            ->where($where);
        $result = $this->query($select);
        if (false === $result) {
            $this->insert($row, $table);
        }
    }
}
