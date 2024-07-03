
<?php
/*****************************************************************************
 *                                                        © 2013 Cart-Power   *
 *           __   ______           __        ____                             *
 *          / /  / ____/___ ______/ /_      / __ \____ _      _____  _____    *
 *      __ / /  / /   / __ `/ ___/ __/_____/ /_/ / __ \ | /| / / _ \/ ___/    *
 *     / // /  / /___/ /_/ / /  / /_/_____/ ____/ /_/ / |/ |/ /  __/ /        *
 *    /_//_/   \____/\__,_/_/   \__/     /_/    \____/|__/|__/\___/_/         *
 *                                                                            *
 *                                                                            *
 * -------------------------------------------------------------------------- *
 * This is commercial software, only users who have purchased a valid license *
 * and  accept to the terms of the License Agreement can install and use this *
 * program.                                                                   *
 * -------------------------------------------------------------------------- *
 * website: https://store.cart-power.com                                      *
 * email:   sales@cart-power.com                                              *
 ******************************************************************************/

use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

//HOOOOOOOKS


function fn_settings_variants_addons_cp_default_usergroup_admin_usergroups() {
    return db_get_hash_single_array("SELECT ?:usergroups.usergroup_id,?:usergroup_descriptions.usergroup FROM ?:usergroups LEFT JOIN ?:usergroup_descriptions ON ?:usergroups.usergroup_id=?:usergroup_descriptions.usergroup_id  WHERE ?:usergroup_descriptions.lang_code=?s AND ?:usergroups.type = ?s AND ?:usergroups.status=?s", array('usergroup_id','usergroup'),DESCR_SL, 'A','A');
}

function fn_settings_variants_addons_cp_default_usergroup_customer_usergroups() {
    return db_get_hash_single_array("SELECT ?:usergroups.usergroup_id,?:usergroup_descriptions.usergroup FROM ?:usergroups LEFT JOIN ?:usergroup_descriptions ON ?:usergroups.usergroup_id=?:usergroup_descriptions.usergroup_id  WHERE ?:usergroup_descriptions.lang_code=?s AND ?:usergroups.type = ?s AND ?:usergroups.status=?s", array('usergroup_id','usergroup'),DESCR_SL, 'C','A');
}

function fn_cp_default_usergroup_update_profile($action, $user_data, $current_user_data) {
	if($action == 'add') {

		$user_type = $user_data['user_type'] == 'V' ? 'A' : $user_data['user_type'];
        if($user_data['user_type']=='A') {
            $usergroups=Registry::get('addons.cp_default_usergroup.admin_usergroups');

        } elseif($user_data['user_type']=='C') {
            $usergroups=Registry::get('addons.cp_default_usergroup.customer_usergroups');
        }

        if(!empty($usergroups)) {
            foreach($usergroups as $k => $v) {
                $data = array(
				    'user_id' => $user_data['user_id'],
				    'usergroup_id' => $k,
				    'status' => 'A'
			    );
                $usergroup_ids[]=$k;
			    $result = db_query("INSERT INTO ?:usergroup_links SET ?u", $data);
            }
            if(Registry::get('addons.cp_default_usergroup.notify_user')=='Y') {
                fn_send_usergroup_status_notification($user_data['user_id'], $usergroup_ids, 'A');
            }
        }
    }
}
