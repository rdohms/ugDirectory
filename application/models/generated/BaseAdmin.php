<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @property integer $userId
 * @property integer $groupId
 * @property string $role
 * @property $Group
 * @property $User
 */
abstract class BaseAdmin extends Doctrine_Record
{
  public function setTableDefinition()
  {
    $this->setTableName('admins');
    $this->hasColumn('user_id as userId', 'integer', null, array('type' => 'integer'));
    $this->hasColumn('group_id as groupId', 'integer', null, array('type' => 'integer'));
    $this->hasColumn('role', 'string', 100, array('type' => 'string', 'length' => '100'));
  }

  public function setUp()
  {
    $this->hasOne('Group', array('local' => 'group_id',
                                 'foreign' => 'id'));

    $this->hasOne('User', array('local' => 'user_id',
                                'foreign' => 'id'));
  }
}