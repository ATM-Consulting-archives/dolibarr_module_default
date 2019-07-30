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

if (!class_exists('SeedObject'))
{
	/**
	 * Needed if $form->showLinkedObjectBlock() is call or for session timeout on our module page
	 */
	define('INC_FROM_DOLIBARR', true);
	require_once dirname(__FILE__).'/../config.php';
}


class MyModule extends SeedObject
{
    /**
     * Canceled status
     */
    const STATUS_CANCELED = -1;
    /**
     * Draft status
     */
    const STATUS_DRAFT = 0;
	/**
	 * Validated status
	 */
	const STATUS_VALIDATED = 1;
	/**
	 * Refused status
	 */
	const STATUS_REFUSED = 3;
	/**
	 * Accepted status
	 */
	const STATUS_ACCEPTED = 4;

	/** @var array $TStatus Array of translate key for each const */
	public static $TStatus = array(
		self::STATUS_CANCELED => 'MyModuleStatusShortCanceled'
		,self::STATUS_DRAFT => 'MyModuleStatusShortDraft'
		,self::STATUS_VALIDATED => 'MyModuleStatusShortValidated'
//		,self::STATUS_REFUSED => 'MyModuleStatusShortRefused'
//		,self::STATUS_ACCEPTED => 'MyModuleStatusShortAccepted'
	);

	/** @var string $table_element Table name in SQL */
	public $table_element = 'mymodule';

	/** @var string $element Name of the element (tip for better integration in Dolibarr: this value should be the reflection of the class name with ucfirst() function) */
	public $element = 'mymodule';

    /** @var string $picto a picture file in [@...]/img/object_[...@].png  */
    public $picto = 'mymodule@mymodule';

	/** @var int $isextrafieldmanaged Enable the fictionalises of extrafields */
    public $isextrafieldmanaged = 1;

    /** @var int $ismultientitymanaged 0=No test on entity, 1=Test with field entity, 2=Test with link by societe */
    public $ismultientitymanaged = 1;

    /** @var string $ref Object reference */
	public $ref;

    /** @var int $entity Object entity */
	public $entity;

	/** @var int $status Object status */
	public $status;

    /** @var string $label Object label */
    public $label;

    /** @var string $description Object description */
    public $description;

    /**
     *  'type' is the field format.
     *  'label' the translation key.
     *  'enabled' is a condition when the field must be managed.
     *  'visible' says if field is visible in list (Examples: 0=Not visible, 1=Visible on list and create/update/view forms, 2=Visible on list only, 3=Visible on create/update/view form only (not list), 4=Visible on list and update/view form only (not create). Using a negative value means field is not shown by default on list but can be selected for viewing)
     *  'noteditable' says if field is not editable (1 or 0)
     *  'notnull' is set to 1 if not null in database. Set to -1 if we must set data to null if empty ('' or 0).
     *  'default' is a default value for creation (can still be replaced by the global setup of default values)
     *  'index' if we want an index in database.
     *  'foreignkey'=>'tablename.field' if the field is a foreign key (it is recommanded to name the field fk_...).
     *  'position' is the sort order of field.
     *  'searchall' is 1 if we want to search in this field when making a search from the quick search button.
     *  'isameasure' must be set to 1 if you want to have a total on list for this field. Field type must be summable like integer or double(24,8).
     *  'css' is the CSS style to use on field. For example: 'maxwidth200'
     *  'help' is a string visible as a tooltip on field
     *  'comment' is not used. You can store here any text of your choice. It is not used by application.
     *  'showoncombobox' if value of the field must be visible into the label of the combobox that list record
     *  'arraykeyval' to set list of value if type is a list of predefined values. For example: array("0"=>"Draft","1"=>"Active","-1"=>"Cancel")
     */

    public $fields = array(

        'ref' => array(
            'type' => 'varchar(50)',
            'length' => 50,
            'label' => 'Ref',
            'enabled' => 1,
            'visible' => 1,
            'notnull' => 1,
            'showoncombobox' => 1,
            'index' => 1,
            'position' => 10,
            'searchall' => 1,
            'comment' => 'Reference of object'
        ),

        'entity' => array(
            'type' => 'integer',
            'label' => 'Entity',
            'enabled' => 1,
            'visible' => 0,
            'default' => 1,
            'notnull' => 1,
            'index' => 1,
            'position' => 20
        ),

        'status' => array(
            'type' => 'integer',
            'label' => 'Status',
            'enabled' => 1,
            'visible' => 0,
            'notnull' => 1,
            'default' => 0,
            'index' => 1,
            'position' => 30,
            'arrayofkeyval' => array(
		        self::STATUS_CANCELED => 'MyModuleStatusShortCanceled'
		        ,self::STATUS_DRAFT => 'MyModuleStatusShortDraft'
		        ,self::STATUS_VALIDATED => 'MyModuleStatusShortValidated'
        //		,self::STATUS_REFUSED => 'MyModuleStatusShortRefused'
        //		,self::STATUS_ACCEPTED => 'MyModuleStatusShortAccepted'
            )
        ),

// exemple of dictionary sell list
//        'fk_mymodule_type' => array(
//            'type' => 'sellist:c_mymodule_type:label:rowid::active=1',
//            'label' => 'Type',
//            'visible' => 1,
//            'enabled' => 1,
//            'position' => 50,
//            'index' => 1,
//            'help' => 'MyModuleDicionaryTypeHelp'
//        ),

        'label' => array(
            'type' => 'varchar(255)',
            'label' => 'Label',
            'enabled' => 1,
            'visible' => 1,
            'position' => 40,
            'searchall' => 1,
            'css' => 'minwidth200',
            'help' => 'Help text',
            'showoncombobox' => 1
        ),

        'fk_soc' => array(
            'type' => 'integer:Societe:societe/class/societe.class.php',
            'label' => 'ThirdParty',
            'visible' => 1,
            'enabled' => 1,
            'position' => 50,
            'index' => 1,
            'help' => 'LinkToThirparty'
        ),

        'description' => array(
            'type' => 'text', // or html for WYSWYG
            'label' => 'Description',
            'enabled' => 1,
            'visible' => -1, //  un bug sur la version 9.0 de Dolibarr necessite de mettre -1 pour ne pas apparaitre sur les listes au lieu de la valeur 3
            'position' => 60
        ),

//        'fk_user_valid' =>array(
//            'type' => 'integer',
//            'label' => 'UserValidation',
//            'enabled' => 1,
//            'visible' => -1,
//            'position' => 512
//        ),

        'import_key' => array(
            'type' => 'varchar(14)',
            'label' => 'ImportId',
            'enabled' => 1,
            'visible' => -2,
            'notnull' => -1,
            'index' => 0,
            'position' => 1000
        ),

    );





    /**
     * MyModule constructor.
     * @param DoliDB    $db    Database connector
     */
    public function __construct($db)
    {
		global $conf;

        parent::__construct($db);

		$this->init();

		$this->status = self::STATUS_DRAFT;
		$this->entity = $conf->entity;
    }

    /**
     * @param User $user User object
     * @return int
     */
    public function save($user)
    {
        if (!empty($this->is_clone))
        {
            // TODO determinate if auto generate
            $this->ref = '(PROV'.$this->id.')';
        }

        return $this->create($user);
    }


    /**
     * @see cloneObject
     * @return void
     */
    public function clearUniqueFields()
    {
        $this->ref = 'Copy of '.$this->ref;
    }


    /**
     * @param User $user User object
     * @return int
     */
    public function delete(User &$user)
    {
        $this->deleteObjectLinked();

        unset($this->fk_element); // avoid conflict with standard Dolibarr comportment
        return parent::delete($user);
    }

    /**
     * @return string
     */
    public function getRef()
    {
		if (preg_match('/^[\(]?PROV/i', $this->ref) || empty($this->ref))
		{
			return $this->getNextRef();
		}

		return $this->ref;
    }

    /**
     * @return string
     */
    private function getNextRef()
    {
		global $db,$conf;

		require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';

		$mask = !empty($conf->global->MYMODULE_REF_MASK) ? $conf->global->MYMODULE_REF_MASK : 'MM{yy}{mm}-{0000}';
		$ref = get_next_value($db, $mask, 'mymodule', 'ref');

		return $ref;
    }


    /**
     * @param User  $user   User object
     * @return int
     */
    public function setDraft($user)
    {
        if ($this->status === self::STATUS_VALIDATED)
        {
            $this->status = self::STATUS_DRAFT;
            $this->withChild = false;

            $ret = $this->update($user);
            if($ret  > 0 )
            {
                //$eventLabel = $langs->transnoentities(__CLASS__.__METHOD__.'Event', $this->ref );
                //$this->addActionComEvent($eventLabel);
            }
            return $ret;

        }

        return 0;
    }

    /**
     * @param User  $user   User object
     * @return int
     */
    public function setValid($user)
    {
        if ($this->status === self::STATUS_DRAFT)
        {
            // TODO determinate if auto generate
//            $this->ref = $this->getRef();
//            $this->fk_user_valid = $user->id;
            $this->status = self::STATUS_VALIDATED;
            $this->withChild = false;

            $ret = $this->update($user);
            if($ret  > 0 )
            {
                //$eventLabel = $langs->transnoentities(__CLASS__.__METHOD__.'Event', $this->ref );
                //$this->addActionComEvent($eventLabel);
            }
            return $ret;
        }

        return 0;
    }

    /**
     * @param User  $user   User object
     * @return int
     */
    public function setAccepted($user)
    {
        if ($this->status === self::STATUS_VALIDATED)
        {
            $this->status = self::STATUS_ACCEPTED;
            $this->withChild = false;

       
            $ret = $this->update($user);
            if($ret  > 0 )
            {
                //$eventLabel = $langs->transnoentities(__CLASS__.__METHOD__.'Event', $this->ref );
                //$this->addActionComEvent($eventLabel);
            }
            return $ret;
        }
        return 0;
    }

    /**
     * @param User  $user   User object
     * @return int
     */
    public function setRefused($user)
    {
        if ($this->status === self::STATUS_VALIDATED)
        {
            $this->status = self::STATUS_REFUSED;
            $this->withChild = false;

            $ret = $this->update($user);
            if($ret  > 0 )
            {
                //$eventLabel = $langs->transnoentities(__CLASS__.__METHOD__.'Event', $this->ref );
                //$this->addActionComEvent($eventLabel);
            }
            return $ret;
        }

        return 0;
    }

    /**
     * @param User  $user   User object
     * @return int
     */
    public function setReopen($user)
    {
        if ($this->status === self::STATUS_ACCEPTED || $this->status === self::STATUS_REFUSED)
        {
            $this->status = self::STATUS_VALIDATED;
            $this->withChild = false;

            $ret = $this->update($user);
            if($ret  > 0 )
            {
                //$eventLabel = $langs->transnoentities(__CLASS__.__METHOD__.'Event', $this->ref );
                //$this->addActionComEvent($eventLabel);
            }
            return $ret;
        }

        return 0;
    }


    /**
     * @param int    $withpicto     Add picto into link
     * @param string $moreparams    Add more parameters in the URL
     * @return string
     */
    public function getNomUrl($withpicto = 0, $moreparams = '')
    {
		global $langs;

        $result='';
        $label = '<u>' . $langs->trans("ShowMyModule") . '</u>';
        if (! empty($this->ref)) $label.= '<br><b>'.$langs->trans('Ref').':</b> '.$this->ref;

        $linkclose = '" title="'.dol_escape_htmltag($label, 1).'" class="classfortooltip">';
        $link = '<a href="'.dol_buildpath('/mymodule/mymodule_card.php', 1).'?id='.$this->id.urlencode($moreparams).$linkclose;

        $linkend='</a>';


        if ($withpicto) $result.=($link.img_object($label, $this->picto, 'class="paddingright classfortooltip valignmiddle"').$linkend);
        if ($withpicto && $withpicto != 2) $result.=' ';

        $result.=$link.$this->ref.$linkend;

        return $result;
    }


    function addActionComEvent($label, $note = ''){
        global $user;

        $object = new ActionComm($this->db);
        $object->code = 'AC_OTH_AUTO';
        $object->type_code = $object->code; // if missing there is an error
        $object->label = $label;
        $object->note_private = $note;

        $object->datep = time();

        $object->fk_element = $this->id;    // Id of record
        $object->elementid = 0;    // Id of record alternative for API
        $object->elementtype = $this->element;   // Type of record. This if property ->element of object linked to.

        $object->socid = $this->fk_soc;
        $object->userownerid = $user->id;
        $object->percentage = -1;


        $newEventId = $object->create($user);
        if($newEventId < 1)
        {
            dol_syslog(__CLASS__ . ":".__METHOD__." launched by " . __FILE__ . ". id=" . $this->id.' error code : '.$object->error, LOG_ERR);
            return -1;
        }

    }

    /**
     * @param int       $id             Identifiant
     * @param null      $ref            Ref
     * @param int       $withpicto      Add picto into link
     * @param string    $moreparams     Add more parameters in the URL
     * @return string
     */
    public static function getStaticNomUrl($id, $ref = null, $withpicto = 0, $moreparams = '')
    {
		global $db;

		$object = new MyModule($db);
		$object->fetch($id, false, $ref);

		return $object->getNomUrl($withpicto, $moreparams);
    }


    /**
     * @param int $mode     0=Long label, 1=Short label, 2=Picto + Short label, 3=Picto, 4=Picto + Long label, 5=Short label + Picto, 6=Long label + Picto
     * @return string
     */
    public function getLibStatut($mode = 0)
    {
        return self::LibStatut($this->status, $mode);
    }

    /**
     * @param int       $status   Status
     * @param int       $mode     0=Long label, 1=Short label, 2=Picto + Short label, 3=Picto, 4=Picto + Long label, 5=Short label + Picto, 6=Long label + Picto
     * @return string
     */
    public static function LibStatut($status, $mode)
    {
		global $langs;

		$langs->load('mymodule@mymodule');
        $res = '';

        if ($status==self::STATUS_CANCELED) { $statusType='status9'; $statusLabel=$langs->trans('MyModuleStatusCancel'); $statusLabelShort=$langs->trans('MyModuleStatusShortCancel'); }
        elseif ($status==self::STATUS_DRAFT) { $statusType='status0'; $statusLabel=$langs->trans('MyModuleStatusDraft'); $statusLabelShort=$langs->trans('MyModuleStatusShortDraft'); }
        elseif ($status==self::STATUS_VALIDATED) { $statusType='status1'; $statusLabel=$langs->trans('MyModuleStatusValidated'); $statusLabelShort=$langs->trans('MyModuleStatusShortValidate'); }
        elseif ($status==self::STATUS_REFUSED) { $statusType='status5'; $statusLabel=$langs->trans('MyModuleStatusRefused'); $statusLabelShort=$langs->trans('MyModuleStatusShortRefused'); }
        elseif ($status==self::STATUS_ACCEPTED) { $statusType='status6'; $statusLabel=$langs->trans('MyModuleStatusAccepted'); $statusLabelShort=$langs->trans('MyModuleStatusShortAccepted'); }

        if (function_exists('dolGetStatus'))
        {
            $res = dolGetStatus($statusLabel, $statusLabelShort, '', $statusType, $mode);
        }
        else
        {
            if ($mode == 0) $res = $statusLabel;
            elseif ($mode == 1) $res = $statusLabelShort;
            elseif ($mode == 2) $res = img_picto($statusLabel, $statusType).$statusLabelShort;
            elseif ($mode == 3) $res = img_picto($statusLabel, $statusType);
            elseif ($mode == 4) $res = img_picto($statusLabel, $statusType).$statusLabel;
            elseif ($mode == 5) $res = $statusLabelShort.img_picto($statusLabel, $statusType);
            elseif ($mode == 6) $res = $statusLabel.img_picto($statusLabel, $statusType);
        }
        
        return $res;
    }

    /**
     * Return HTML string to put an input field into a page
     * Code very similar with showInputField of extra fields
     *
     * @param  array   		$val	       Array of properties for field to show
     * @param  string  		$key           Key of attribute
     * @param  string  		$value         Preselected value to show (for date type it must be in timestamp format, for amount or price it must be a php numeric value)
     * @param  string  		$moreparam     To add more parameters on html input tag
     * @param  string  		$keysuffix     Prefix string to add into name and id of field (can be used to avoid duplicate names)
     * @param  string  		$keyprefix     Suffix string to add into name and id of field (can be used to avoid duplicate names)
     * @param  string|int	$morecss       Value for css to define style/length of field. May also be a numeric.
     * @return string
     */
    public function showInputField($val, $key, $value, $moreparam = '', $keysuffix = '', $keyprefix = '', $morecss = 0)
    {
        global $conf, $langs, $form;

        $out = parent::showInputField($val, $key, $value, $moreparam, $keysuffix, $keyprefix, $morecss);

        return $out;
}

    /**
     * Return HTML string to show a field into a page
     * Code very similar with showOutputField of extra fields
     *
     * @param  array   $val		       Array of properties of field to show
     * @param  string  $key            Key of attribute
     * @param  string  $value          Preselected value to show (for date type it must be in timestamp format, for amount or price it must be a php numeric value)
     * @param  string  $moreparam      To add more parametes on html input tag
     * @param  string  $keysuffix      Prefix string to add into name and id of field (can be used to avoid duplicate names)
     * @param  string  $keyprefix      Suffix string to add into name and id of field (can be used to avoid duplicate names)
     * @param  mixed   $morecss        Value for css to define size. May also be a numeric.
     * @return string
     */
    public function showOutputField($val, $key, $value, $moreparam = '', $keysuffix = '', $keyprefix = '', $morecss = '')
    {
        global $conf, $langs, $form;

        // TODO : quite a fixer autant le faire dans le seed object
        // patch for dolibarr 9.0 show PR : https://github.com/Dolibarr/dolibarr/pull/11571
        if(preg_match('/^sellist:(.*):(.*):(.*):(.*)/i', $val['type'], $reg)) {
            $val['param']['options'] = array($reg[1] . ':' . $reg[2] . ':' . $reg[3] . ':' . $reg[4] => 'N');
            $val['type'] = 'sellist';
        }

        $out = parent::showOutputField($val, $key, $value, $moreparam, $keysuffix, $keyprefix, $morecss);


        return $out;
    }

}


//class WebHostDet extends SeedObject
//{
//    public $table_element = 'mymoduledet';
//
//    public $element = 'mymoduledet';
//
//
//    /**
//     * MyModuleDet constructor.
//     * @param DoliDB    $db    Database connector
//     */
//    public function __construct($db)
//    {
//        $this->db = $db;
//
//        $this->init();
//    }
//}
