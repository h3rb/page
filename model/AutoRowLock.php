<?php // Table: AutoRowLock

 class AutoRowLock extends Model {

  public function IsLocked($lock) {
   global $auth;
   return ( intval($auth['ID']) != intval($lock['r_Auth']) && !$this->Expired($lock) );
  }

  public function Expired($lock) {
   return ( strtotime('now') - intval($lock['Timestamp']) > EDIT_LOCK_TIMEOUT );
  }

  public function LockedTo($lock) {
   if ( false_or_null($lock) ) return FALSE;
   global $database;
   $a_model=new Auth($database);
   $user=$a_model->Get($lock['r_Auth']);
   if ( false_or_null($user) ) return FALSE;
   return $user['username'];
  }

  public function LockedByMe($lock) {
   global $auth;
   return ( intval($auth['ID']) === intval($lock['r_Auth']) );
  }

  public function RefreshLock($lock) {
//   plog("Refreshed Lock");
   $this->Update(
    array("Timestamp"=>strtotime('now')),
    array('ID'=>$lock['ID'])
   );
  }

  public function LockToMe($table, $id) {
   global $auth;
   return $this->Insert(array(
    'T'=>$table,
    'I'=>$id,
    'r_Auth'=>$auth['ID'],
    'Timestamp'=>strtotime('now')
   ));
  }

 };
