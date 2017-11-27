<?php

if (!class_exists('TObjetStd'))
{
	/**
	 * Needed if $form->showLinkedObjectBlock() is call
	 */
	define('INC_FROM_DOLIBARR', true);
	require_once dirname(__FILE__).'/../config.php';
}


class TMyModule extends TObjetStd
{
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
	
	public static $TStatus = array(
		self::STATUS_DRAFT => 'Draft'
		,self::STATUS_VALIDATED => 'Validate'
		,self::STATUS_REFUSED => 'Refuse'
		,self::STATUS_ACCEPTED => 'Accept'
	);


	public function __construct()
	{
		global $conf,$langs,$db;
		
		$this->set_table(MAIN_DB_PREFIX.'mymodule');
		
		$this->add_champs('ref', array('type' => 'string', 'length' => 80, 'index' => true));
		$this->add_champs('label', array('type' => 'string'));
		$this->add_champs('status', array('type' => 'integer'));
		
		$this->add_champs('entity,fk_user_author', array('type' => 'integer', 'index' => true));
//		$this->add_champs('date_other,date_other_2', array('type' => 'date'));
//		$this->add_champs('note', array('type' => 'text'));
		
		$this->_init_vars();
		$this->start();
		
//		$this->setChild('TMyModuleChild','fk_mymodule');
		
		if (!class_exists('GenericObject')) require_once DOL_DOCUMENT_ROOT.'/core/class/genericobject.class.php';
		$this->generic = new GenericObject($db);
		$this->generic->table_element = $this->get_table();
		$this->generic->element = 'mymodule';
		
		$this->status = self::STATUS_DRAFT;
		$this->entity = $conf->entity;
	}

	public function save(&$PDOdb, $addprov=false)
	{
		global $user;
		
		if (!$this->getId()) $this->fk_user_author = $user->id;
		
		$res = parent::save($PDOdb);
		
		if ($addprov || !empty($this->is_clone))
		{
			$this->ref = '(PROV'.$this->getId().')';
			
			if (!empty($this->is_clone)) $this->status = self::STATUS_DRAFT;
			
			$wc = $this->withChild;
			$this->withChild = false;
			$res = parent::save($PDOdb);
			$this->withChild = $wc;
		}
		
		return $res;
	}
	
	public function load(&$PDOdb, $id, $loadChild = true)
	{
		global $db;
		
		$res = parent::load($PDOdb, $id, $loadChild);
		
		$this->generic->id = $this->getId();
		$this->generic->ref = $this->ref;
		
		if ($loadChild) $this->fetchObjectLinked();
		
		return $res;
	}
	
	public function delete(&$PDOdb)
	{
		$this->generic->deleteObjectLinked();
		
		parent::delete($PDOdb);
	}
	
	public function fetchObjectLinked()
	{
		$this->generic->fetchObjectLinked($this->getId());
	}

	public function setDraft(&$PDOdb)
	{
		if ($this->status == self::STATUS_VALIDATED)
		{
			$this->status = self::STATUS_DRAFT;
			$this->withChild = false;
			
			return parent::save($PDOdb);
		}
		
		return 0;
	}
	
	public function setValid(&$PDOdb)
	{
//		global $user;
		
		$this->ref = $this->getNumero();
		$this->status = self::STATUS_VALIDATED;
		
		return parent::save($PDOdb);
	}
	
	public function getNumero()
	{
		if (preg_match('/^[\(]?PROV/i', $this->ref) || empty($this->ref))
		{
			return $this->getNextNumero();
		}
		
		return $this->ref;
	}
	
	private function getNextNumero()
	{
		global $db,$conf;
		
		require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
		
		$mask = !empty($conf->global->MYMODULE_REF_MASK) ? $conf->global->MYMODULE_REF_MASK : 'MM{yy}{mm}-{0000}';
		$numero = get_next_value($db, $mask, 'mymodule', 'ref');
		
		return $numero;
	}
	
	public function setRefused(&$PDOdb)
	{
//		global $user;
		
		$this->status = self::STATUS_REFUSED;
		$this->withChild = false;
		
		return parent::save($PDOdb);
	}
	
	public function setAccepted(&$PDOdb)
	{
//		global $user;
		
		$this->status = self::STATUS_ACCEPTED;
		$this->withChild = false;
		
		return parent::save($PDOdb);
	}
	
	public function getNomUrl($withpicto=0, $get_params='')
	{
		global $langs;

        $result='';
        $label = '<u>' . $langs->trans("ShowMyModule") . '</u>';
        if (! empty($this->ref)) $label.= '<br><b>'.$langs->trans('Ref').':</b> '.$this->ref;
        
        $linkclose = '" title="'.dol_escape_htmltag($label, 1).'" class="classfortooltip">';
        $link = '<a href="'.dol_buildpath('/mymodule/card.php', 1).'?id='.$this->getId(). $get_params .$linkclose;
       
        $linkend='</a>';

        $picto='generic';
		
        if ($withpicto) $result.=($link.img_object($label, $picto, 'class="classfortooltip"').$linkend);
        if ($withpicto && $withpicto != 2) $result.=' ';
		
        $result.=$link.$this->ref.$linkend;
		
        return $result;
	}
	
	public static function getStaticNomUrl($id, $withpicto=0)
	{
		global $PDOdb;
		
		if (empty($PDOdb)) $PDOdb = new TPDOdb;
		
		$object = new TMyModule;
		$object->load($PDOdb, $id, false);
		
		return $object->getNomUrl($withpicto);
	}
	
	public function getLibStatut($mode=0)
    {
        return self::LibStatut($this->status, $mode);
    }
	
	public static function LibStatut($status, $mode)
	{
		global $langs;
		$langs->load('mymodule@mymodule');

		if ($status==self::STATUS_DRAFT) { $statustrans='statut0'; $keytrans='MyModuleStatusDraft'; $shortkeytrans='Draft'; }
		if ($status==self::STATUS_VALIDATED) { $statustrans='statut1'; $keytrans='MyModuleStatusValidated'; $shortkeytrans='Validate'; }
		if ($status==self::STATUS_REFUSED) { $statustrans='statut5'; $keytrans='MyModuleStatusRefused'; $shortkeytrans='Refused'; }
		if ($status==self::STATUS_ACCEPTED) { $statustrans='statut6'; $keytrans='MyModuleStatusAccepted'; $shortkeytrans='Accepted'; }

		
		if ($mode == 0) return img_picto($langs->trans($keytrans), $statustrans);
		elseif ($mode == 1) return img_picto($langs->trans($keytrans), $statustrans).' '.$langs->trans($keytrans);
		elseif ($mode == 2) return $langs->trans($keytrans).' '.img_picto($langs->trans($keytrans), $statustrans);
		elseif ($mode == 3) return img_picto($langs->trans($keytrans), $statustrans).' '.$langs->trans($shortkeytrans);
		elseif ($mode == 4) return $langs->trans($shortkeytrans).' '.img_picto($langs->trans($keytrans), $statustrans);
	}
	
}

/**
 * Class needed if link exists with dolibarr object from element_element and call from $form->showLinkedObjectBlock()
 */
class Mymodule extends TMyModule
{
	private $PDOdb;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->PDOdb = new TPDOdb;
	}
	
	function fetch($id)
	{
		return $this->load($this->PDOdb, $id);
	}
}

/*
class TMyModuleChild extends TObjetStd
{
	public function __construct()
	{
		$this->set_table(MAIN_DB_PREFIX.'mymodule_child');
		
		$this->add_champs('fk_mymodule', array('type' => 'integer', 'index' => true));
//		$this->add_champs('fk_user', array('type' => 'integer', 'index' => true)); // link n_n with user for example
		
		$this->_init_vars();
		$this->start();
		
		$this->user = null;
	}
	
	public function load(&$PDOdb, $id, $loadChild=true)
	{
		$res = parent::load($PDOdb, $id, $loadChild);
		
		return $res;
	}
	
	public function loadBy(&$PDOdb, $value, $field, $annexe = false)
	{
		$res = parent::loadBy($PDOdb, $value, $field, $annexe);
		
		return $res;
	}
	
}
*/
