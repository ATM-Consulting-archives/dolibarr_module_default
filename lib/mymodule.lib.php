<?php
/* Copyright (C) 2019 ATM Consulting <support@atm-consulting.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *	\file		lib/mymodule.lib.php
 *	\ingroup	mymodule
 *	\brief		This file is an example module library
 *				Put some comments here
 */

/**
 * @return array
 */
function mymoduleAdminPrepareHead()
{
    global $langs, $conf;

    $langs->load('mymodule@mymodule');

    $h = 0;
    $head = array();

    $head[$h][0] = dol_buildpath("/mymodule/admin/mymodule_setup.php", 1);
    $head[$h][1] = $langs->trans("Parameters");
    $head[$h][2] = 'settings';
    $h++;
    $head[$h][0] = dol_buildpath("/mymodule/admin/mymodule_extrafields.php", 1);
    $head[$h][1] = $langs->trans("ExtraFields");
    $head[$h][2] = 'extrafields';
    $h++;
    $head[$h][0] = dol_buildpath("/mymodule/admin/mymodule_about.php", 1);
    $head[$h][1] = $langs->trans("About");
    $head[$h][2] = 'about';
    $h++;

    // Show more tabs from modules
    // Entries must be declared in modules descriptor with line
    //$this->tabs = array(
    //	'entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__'
    //); // to add new tab
    //$this->tabs = array(
    //	'entity:-tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__'
    //); // to remove a tab
    complete_head_from_modules($conf, $langs, $object, $head, $h, 'mymodule');

    return $head;
}

/**
 * Return array of tabs to used on pages for third parties cards.
 *
 * @param 	MyModule	$object		Object company shown
 * @return 	array				Array of tabs
 */
function mymodule_prepare_head(MyModule $object)
{
    global $langs, $conf;
    $h = 0;
    $head = array();
    $head[$h][0] = dol_buildpath('/mymodule/mymodule_card.php', 1).'?id='.$object->id;
    $head[$h][1] = $langs->trans("MyModuleCard");
    $head[$h][2] = 'card';
    $h++;
	
	// Show more tabs from modules
    // Entries must be declared in modules descriptor with line
    // $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
    // $this->tabs = array('entity:-tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to remove a tab
    complete_head_from_modules($conf, $langs, $object, $head, $h, 'mymodule');
	
	return $head;
}

/**
 * @param Form      $form       Form object
 * @param MyModule  $object     MyModule object
 * @param string    $action     Triggered action
 * @return string
 */
function getFormConfirmMyModule($form, $object, $action)
{
    global $langs, $user;

    $formconfirm = '';

    if ($action === 'valid' && !empty($user->rights->mymodule->write))
    {
        $body = $langs->trans('ConfirmValidateMyModuleBody', $object->ref);
        $formconfirm = $form->formconfirm($_SERVER['PHP_SELF'] . '?id=' . $object->id, $langs->trans('ConfirmValidateMyModuleTitle'), $body, 'confirm_validate', '', 0, 1);
    }
    elseif ($action === 'accept' && !empty($user->rights->mymodule->write))
    {
        $body = $langs->trans('ConfirmAcceptMyModuleBody', $object->ref);
        $formconfirm = $form->formconfirm($_SERVER['PHP_SELF'] . '?id=' . $object->id, $langs->trans('ConfirmAcceptMyModuleTitle'), $body, 'confirm_accept', '', 0, 1);
    }
    elseif ($action === 'refuse' && !empty($user->rights->mymodule->write))
    {
        $body = $langs->trans('ConfirmRefuseMyModuleBody', $object->ref);
        $formconfirm = $form->formconfirm($_SERVER['PHP_SELF'] . '?id=' . $object->id, $langs->trans('ConfirmRefuseMyModuleTitle'), $body, 'confirm_refuse', '', 0, 1);
    }
    elseif ($action === 'reopen' && !empty($user->rights->mymodule->write))
    {
        $body = $langs->trans('ConfirmReopenMyModuleBody', $object->ref);
        $formconfirm = $form->formconfirm($_SERVER['PHP_SELF'] . '?id=' . $object->id, $langs->trans('ConfirmReopenMyModuleTitle'), $body, 'confirm_refuse', '', 0, 1);
    }
    elseif ($action === 'delete' && !empty($user->rights->mymodule->write))
    {
        $body = $langs->trans('ConfirmDeleteMyModuleBody');
        $formconfirm = $form->formconfirm($_SERVER['PHP_SELF'] . '?id=' . $object->id, $langs->trans('ConfirmDeleteMyModuleTitle'), $body, 'confirm_delete', '', 0, 1);
    }
    elseif ($action === 'clone' && !empty($user->rights->mymodule->write))
    {
        $body = $langs->trans('ConfirmCloneMyModuleBody', $object->ref);
        $formconfirm = $form->formconfirm($_SERVER['PHP_SELF'] . '?id=' . $object->id, $langs->trans('ConfirmCloneMyModuleTitle'), $body, 'confirm_clone', '', 0, 1);
    }
    elseif ($action === 'cancel' && !empty($user->rights->mymodule->write))
    {
        $body = $langs->trans('ConfirmCancelMyModuleBody', $object->ref);
        $formconfirm = $form->formconfirm($_SERVER['PHP_SELF'] . '?id=' . $object->id, $langs->trans('ConfirmCancelMyModuleTitle'), $body, 'confirm_cancel', '', 0, 1);
    }

    return $formconfirm;
}


function mymoduleBannerTab(WebHost $object){
    global $langs;

    $linkback = '<a href="' .dol_buildpath('/mymodule/mymodule_list.php', 1) . '?restore_lastsearch_values=1">' . $langs->trans('BackToList') . '</a>';


    $morehtmlref = '';

    if(!empty($object->label)){
        $morehtmlref.=' : '.$object->label;
    }

    $morehtmlref.='<div class="refidno">';

    // Ref bis
    /*$morehtmlref.=$form->editfieldkey("RefBis", 'ref_client', $object->ref_client, $object, $user->rights->webhost->write, 'string', '', 0, 1);
    $morehtmlref.=$form->editfieldval("RefBis", 'ref_client', $object->ref_client, $object, $user->rights->webhost->write, 'string', '', null, null, '', 1);
    */

    // Display a field output
    //$fieldKey = 'your object field';
    //$morehtmlref.= '<strong>'.$object->showOutputField($object->fields[$fieldKey], $fieldKey, $object->{$fieldKey}, '', '', '', 0).'</strong>';


    /*if(!empty($object->fk_soc))
    {
        $soc = new Societe($object->db);
        if($soc->fetch($object->fk_soc)>0)
        {
            // Thirdparty
            $morehtmlref.='<br>'.$langs->trans('ThirdParty') . ' : ' . $soc->getNomUrl(1);
        }
    }*/


    $morehtmlref.='</div>';


    $morehtmlstatus=''; //$object->getLibStatut(2); // pas besoin fait doublon
    dol_banner_tab($object, 'ref', $linkback, 1, 'ref', 'ref', $morehtmlref, '', 0, '', $morehtmlstatus);
}

