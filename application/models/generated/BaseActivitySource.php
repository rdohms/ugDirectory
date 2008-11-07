<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @property integer $groupId
 * @property string $atype
 * @property string $target
 * @property $Group
 * @property $ActivityType
 */
abstract class BaseActivitySource extends Doctrine_Record
{
  public function setTableDefinition()
  {
    $this->setTableName('activity_sources');
    $this->hasColumn('group_id as groupId', 'integer', null, array('type' => 'integer'));
    $this->hasColumn('atype', 'string', 5, array('type' => 'string', 'length' => '5'));
    $this->hasColumn('target', 'string', 100, array('type' => 'string', 'length' => '100'));
  }

  public function setUp()
  {
    $this->hasOne('Group', array('local' => 'group_id',
                                 'foreign' => 'id'));

    $this->hasOne('ActivityType', array('local' => 'atype',
                                        'foreign' => 'atype'));
  }
}