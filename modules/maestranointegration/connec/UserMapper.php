<?php

class UserMapper extends BaseMapper {
  public function __construct() {
    parent::__construct();

    $this->connec_entity_name = 'AppUser';
    $this->local_entity_name = 'Users';
    $this->connec_resource_name = 'app_users';
    $this->connec_resource_endpoint = 'app_users';
  }

  // Return the User local id
  protected function getId($user) {
    return $user->id;
  }

  // Return a local User by id
  protected function loadModelById($local_id) {
    $user = CRMEntity::getInstance("Users");
    $user->retrieve_entity_info($local_id, "Users");
    vtlib_setup_modulevars("Users", $user);
    $user->id = $local_id;
    $user->mode = 'edit';
    return $user;
  }

  // Return any existing User with same email
  public function matchLocalModel($user_hash) {
    global $adb;

    // Fetch record
    $query = "SELECT id from vtiger_users where email1=?";
    $result = $adb->pquery($query, array($this->email));
    if($result) { return $this->loadModelById($result->fields['id']); }
    
    return null;
  }

  // Map the Connec resource attributes onto the vTiger User
  protected function mapConnecResourceToModel($user_hash, $user) {
    if($this->is_set($user_hash['first_name'])) { $user->column_fields['first_name'] = $user_hash['first_name']; }
    if($this->is_set($user_hash['last_name'])) { $user->column_fields['last_name'] = $user_hash['last_name']; }

    if($this->is_set($user_hash['email']['address'])) {
      $user->column_fields['email1'] = $user_hash['email']['address'];
      $user->column_fields['user_name'] = $user_hash['email']['address'];
    }
    if($this->is_set($user_hash['email']['address2'])) {
      $user->column_fields['email2'] = $user_hash['email']['address2'];
      $user->column_fields['user_name'] = $user_hash['email']['address'];
    }

    if($this->is_set($user_hash['address_work'])) {
      if($this->is_set($user_hash['address_work']['billing'])) {
        $billing_address = $user_hash['address_work']['billing'];
        if($this->is_set($billing_address['line1'])) { $user->column_fields['address_street'] = $billing_address['line1']; }
        if($this->is_set($billing_address['city'])) { $user->column_fields['address_city'] = $billing_address['city']; }
        if($this->is_set($billing_address['region'])) { $user->column_fields['address_state'] = $billing_address['region']; }
        if($this->is_set($billing_address['postal_code'])) { $user->column_fields['address_postalcode'] = $billing_address['postal_code']; }
        if($this->is_set($billing_address['country'])) { $user->column_fields['address_country'] = $billing_address['country']; }
      }
      else if($this->is_set($user_hash['address_work']['shipping'])) {
        $shipping_address = $user_hash['address_work']['shipping'];
        if($this->is_set($shipping_address['line1'])) { $user->column_fields['address_street'] = $shipping_address['line1']; }
        if($this->is_set($shipping_address['city'])) { $user->column_fields['address_city'] = $shipping_address['city']; }
        if($this->is_set($shipping_address['region'])) { $user->column_fields['address_state'] = $shipping_address['region']; }
        if($this->is_set($shipping_address['postal_code'])) { $user->column_fields['address_postalcode'] = $shipping_address['postal_code']; }
        if($this->is_set($shipping_address['country'])) { $user->column_fields['address_country'] = $shipping_address['country']; }
      }
    }

    if($this->is_set($user_hash['phone_home'])) {
      if($this->is_set($user_hash['phone_home']['landline'])) { $user->column_fields['phone_home'] = $user_hash['phone_home']['landline']; }
    }
    
    if($this->is_set($user_hash['phone_work'])) {
      if($this->is_set($user_hash['phone_work']['landline'])) { $user->column_fields['phone_work'] = $user_hash['phone_work']['landline']; }
      if($this->is_set($user_hash['phone_work']['landline2'])) { $user->column_fields['phone_other'] = $user_hash['phone_work']['landline2']; }
      if($this->is_set($user_hash['phone_work']['mobile'])) { $user->column_fields['phone_mobile'] = $user_hash['phone_work']['mobile']; }
      if($this->is_set($user_hash['phone_work']['fax'])) { $user->column_fields['phone_fax'] = $user_hash['phone_work']['fax']; }
    }

    if($this->is_set($user_hash['is_admin'])) {
      if($user_hash['is_admin']) {
        $user->column_fields['is_admin'] = "on";
      }
      else {
        $user->column_fields['is_admin'] = "off";
      }
    }

  }

  // Map the vTiger User to a Connec User hash
  protected function mapModelToConnecResource($user) {
    $user_hash = array();

    // Map attributes
    $user_hash['first_name'] = $user->column_fields['first_name'];
    $user_hash['last_name'] = $user->column_fields['last_name'];

    $address = array();
    $billing_address = array();
    $billing_address['line1'] = $user->column_fields['address_street'];
    $billing_address['city'] = $user->column_fields['address_city'];
    $billing_address['region'] = $user->column_fields['address_state'];
    $billing_address['postal_code'] = $user->column_fields['address_postalcode'];
    $billing_address['country'] = $user->column_fields['address_country'];
    if(!empty($billing_address)) { $address['billing'] = $billing_address; }
    if(!empty($address)) { $user_hash['address_work'] = $address; }

    $phone_work_hash = array();
    $phone_work_hash['landline'] = $user->column_fields['phone_work'];
    $phone_work_hash['landline2'] = $user->column_fields['phone_other'];
    $phone_work_hash['fax'] = $user->column_fields['phone_fax'];
    $phone_work_hash['mobile'] = $user->column_fields['phone_mobile'];
    if(!empty($phone_work_hash)) { $user_hash['phone_work'] = $phone_work_hash; }

    $phone_home_hash = array();
    $phone_home_hash['landline'] = $user->column_fields['phone_home'];
    if(!empty($phone_home_hash)) { $user_hash['phone_home'] = $phone_home_hash; }

    $email_hash = array();
    $email_hash['address'] = $user->column_fields['email1'];
    $email_hash['address2'] = $user->column_fields['email2'];
    if(!empty($email_hash)) { $user_hash['email'] = $email_hash; }

    // Not sure below is relevant
    // // Find the teams (Groups) corresponding to the provided role
    // if($this->is_set($user->column_fields['roleid'])) {
    //   $db = PearDatabase::getInstance();
    //   $groupIdsContainingThisRole = array();
    //   $roleId = $user->column_fields['roleid'];
      
    //   $resultGroupIds = $db->pquery('SELECT vtiger_group2role.groupid FROM vtiger_group2role WHERE vtiger_group2role.roleid=?',array($roleId));
    //   for($j=0;$j<$db->num_rows($resultGroupIds);$j++) {
    //     $groupId = $db->query_result($resultGroupIds,$j,'groupid');
    //     if(!in_array($groupId, $groupIdsContainingThisRole)) {
    //       array_push($groupIdsContainingThisRole, $groupId);
    //     }
    //   }
      
    //   // TODO map corresponding team and add them to the Connec! push
    // }

    return $user_hash;
  }

  // Persist the vTiger User
  protected function persistLocalModel($user, $resource_hash) {
    // Will be used to check if the user already exist locally
    $mno_id_map = MnoIdMap::findMnoIdMapByMnoIdAndEntityName($resource_hash['id'], 'APPUSER', 'USERS');

    $user->save("Users", false);
    // We make sure that the mnoIdMap is created before we start to parse the user's teams to bloc kany eventual recursive loop
    $this->findOrCreateIdMap($resource_hash, $user);

    // Add user to corresponding teams
    if (!$mno_id_map) {
      // User does not exist locally => we add him to its teams
      $mno_teams = $resource_hash['teams'];
      $team_mapper = new TeamMapper();
      for($j=0;$j<count($mno_teams);$j++) {
        $team_hash = $mno_teams[$j];
        // Retrieve the local group if exists or create it based on data from Connec! Entity::Team otherwise
        $group_model = $team_mapper->fetchConnecResource($team_hash['id']);
        $group_members = array_keys($group_model->getMembers()["Users"]);
        // At this stage, the user should exist (has been saved before), and have an id defined
        array_push($group_members, "Users:" . json_encode($user->id));
        $group_model->set('group_members', $group_members);
        // Will save the new users list for the team
        $team_mapper->persistLocalModel($group_model,null);
      }
    }
  }
}
