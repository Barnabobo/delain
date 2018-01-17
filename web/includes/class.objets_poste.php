<?php

/**
 * includes/class.objets_poste.php
 */

/**
 * Class objets_poste
 *
 * Gère les objets BDD de la table objets_poste
 */


class objets_poste
{
	//-------------- Constante de l'objet objets_poste
	private $frais_de_port_par_kilo = 100 ;
	private $delai_livraison = "5 DAYS" ;				
	private $delai_confiscation = "2 MONTHS" ;				
  
	//-------------- Données de l'objet récupérer/sauvegarder en DB
    var $opost_cod ;
    var $opost_colis_cod ;
    var $opost_obj_cod ;
    var $opost_emet_perso_cod ;
    var $opost_dest_perso_cod ;    
    var $opost_date_poste ;   
    var $opost_prix_demande ;   
	  

    function __construct()
    {  
		$this->opost_date_poste = date('Y-m-d H:i:s');
    }

    /**
     * Charge dans la classe un enregistrement de objets_poste
     * @global bdd_mysql $pdo
     * @param integer $code => PK
     * @return boolean => false si non trouvé
     */
    function charge($code)
    {
        $pdo    = new bddpdo;
        $req    = "select * from objets_poste where opost_cod = ?";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($code), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;
        }
        $this->opost_cod          	   = $result['opost_cod'];
        $this->opost_colis_cod         = $result['opost_colis_cod'];
        $this->opost_obj_cod           = $result['opost_obj_cod'];
        $this->opost_emet_perso_cod    = $result['opost_emet_perso_cod'];
        $this->opost_dest_perso_cod    = $result['opost_dest_perso_cod'];
        $this->opost_date_poste        = $result['opost_date_poste'];
        $this->opost_prix_demande      = $result['opost_prix_demande'];
        return true;
    }

    /**
     * Stocke l'enregistrement courant dans la BDD
     * @global bdd_mysql $pdo
     * @param boolean $new => true si new enregistrement (insert), false si existant (update)
     */
    function stocke($new = false)
    {
        $pdo = new bddpdo;
        if ($new)
        {
            $req  = "insert into objets_poste (
                         opost_colis_cod,
                         opost_obj_cod,
                         opost_emet_perso_cod,
                         opost_dest_perso_cod,
                         opost_date_poste,
						 opost_prix_demande)
                    values
                    (
                        :opost_colis_cod,
                        :opost_obj_cod,
                        :opost_emet_perso_cod,
                        :opost_dest_perso_cod,
                        :opost_date_poste,
                        :opost_prix_demande
                    )
    returning opost_cod as id";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
               ":opost_colis_cod"          => $this->opost_colis_cod,
               ":opost_obj_cod"            => $this->opost_obj_cod,
               ":opost_emet_perso_cod"     => $this->opost_emet_perso_cod,
               ":opost_dest_perso_cod"     => $this->opost_dest_perso_cod,
               ":opost_date_poste"         => $this->opost_date_poste,
               ":opost_prix_demande"       => $this->opost_prix_demande
               ), $stmt);


            $temp = $stmt->fetch();
            $this->charge($temp['id']);
        }
        else
        {
            $req  = "update objets_poste
                    set
                opost_colis_cod =       :opost_colis_cod,
                opost_obj_cod =         :opost_obj_cod,
                opost_emet_perso_cod =  :opost_emet_perso_cod,
                opost_dest_perso_cod =  :opost_dest_perso_cod,
                opost_date_poste =      :opost_date_poste,
                opost_prix_demande =    :opost_prix_demande
            where opost_cod = :opost_cod ";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(
               ":opost_cod"             => $this->opost_cod,
               ":opost_colis_cod"       => $this->opost_colis_cod,
               ":opost_obj_cod"         => $this->opost_obj_cod,
               ":opost_emet_perso_cod"  => $this->opost_emet_perso_cod,
               ":opost_dest_perso_cod"  => $this->opost_dest_perso_cod,
               ":opost_date_poste"      => $this->opost_date_poste,
               ":opost_prix_demande"    => $this->opost_prix_demande
               ), $stmt);
        }
    }

	 /**
     * supprime l'enregistrement de objets_poste
     * @global bdd_mysql $pdo
     * @param integer $code => PK (si non fournie alors suppression de l'ojet chargé)
     * @return boolean => false pas réussi a supprimer
     */
    function supprime($code="")
    {
		if ($code=="") $code = $this->opost_cod;
        $pdo    = new bddpdo;
        $req    = "DELETE from objets_poste where opost_cod = ?";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array($code), $stmt);
  	    if ($stmt->rowCount()==0) 
        {
            return false;
        }

        return true;
    }
	
    /**
     * Retourne un tableau de tous les enregistrements
     * @global bdd_mysql $pdo
     * @return \objets_poste
     */
    function getAll()
    {
        $retour = array();
        $pdo    = new bddpdo;
        $req    = "select opost_cod from objets_poste order by opost_cod";
        $stmt   = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            $temp     = new objets_poste;
            $temp->charge($result["opost_cod"]);
            $retour[] = $temp;
            unset($temp);
        }
        return $retour;
    }
	
    public function __call($name, $arguments)
    {
        switch (substr($name, 0, 6))
        {
            case 'getBy_':
                if (property_exists($this, substr($name, 6)))
                {
                    $retour = array();
                    $pdo    = new bddpdo;
                    $req    = "select opost_cod from objets_poste where " . substr($name, 6) . " = ? order by opost_cod";
                    $stmt   = $pdo->prepare($req);
                    $stmt   = $pdo->execute(array($arguments[0]), $stmt);
                    while ($result = $stmt->fetch())
                    {
                        $temp     = new objets_poste;
                        $temp->charge($result["opost_cod"]);
                        $retour[] = $temp;
                        unset($temp);
                    }
                    if (count($retour) == 0)
                    {
                        return false;
                    }
                    return $retour;
                }
                else
                {
                    die('Unknown variable ' . substr($name,6));
                }
                break;

            default:
                die('Unknown method.');
        }
    }
	
	//----------------------------------------
    /**
     * Retourne les frais de port en fonction du poids d'un objet
     * @param integer $poids => poids de l'objet	 
     * @global bdd_mysql $pdo
     * @return numeric
     */
    function getFraisDePort($poids)
    {
		return $poids * $this->frais_de_port_par_kilo ;             
    }	

	//----------------------------------------
    /**
     * Retourne la date de livraison d'un objet 
     * @global bdd_mysql $pdo
     * @return date
     */
    function getDateLivraison()
    {
		return date('Y-m-d H:i:s', strtotime($this->opost_date_poste.' '. $this->delai_livraison));       
    }	

	//----------------------------------------
    /**
     * Retourne vrai sil l'objet peut être retirer de la poste 
     * @global bdd_mysql $pdo
     * @return boolean
     */
    function estLivrable()
    {
		return date('Y-m-d H:i:s') >= $this->getDateLivraison() ;       
    }	
	
	//----------------------------------------
    /**
     * Retourne la date de confiscation d'un objet 
     * @global bdd_mysql $pdo
     * @return date
     */
    function getDateConfiscation()
    {
		return date('Y-m-d H:i:s', strtotime($this->opost_date_poste.' '. $this->delai_confiscation));       
    }			

	//----------------------------------------
    /**
     * Retourne vrai si l'objet doit-être confisqué par la poste 
     * @global bdd_mysql $pdo
     * @return boolean
     */
    function estConfiscable()
    {
		return date('Y-m-d H:i:s') >= $this->getDateConfiscation() ;       
    }	

	//----------------------------------------
    /**
     * Supprime l'objet de la poste ainsi que l'objet lui-meme si il est confiscable
     * @global bdd_mysql $pdo
     * @param integer $perso_cod => le perso qui est à l'origine de la confiscation (emmeteur ou destinataire)		 
     * @return boolean : true si l'objet a été bien confisqué
     */
    function Confisque($perso_cod)
    {
		if (!$this->estConfiscable())
		{
			return false;
		}
		
	    $pdo    = new bddpdo;	

		/********************************************************************/
		// On vérifie que l’objet existe et on recupère son nom generique au passage
		/********************************************************************/
		$req    = "SELECT obj_nom_generique FROM objets INNER JOIN objet_generique on gobj_cod = obj_gobj_cod WHERE obj_cod = :opost_obj_cod;";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":opost_obj_cod" => $this->opost_obj_cod), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;	// Anomalie !
        }	
		$nom_objet = $result["obj_nom_generique"] ; 
		

		//$this->supprime();		//supression de l'objet dans la base avant l'objet lui même (car il possèdes des clés étrangères)

		/********************************************************************/
		// On supprime l'objet de la base identification et objets
		$req    = "DELETE FROM perso_identifie_objet WHERE pio_obj_cod = :opost_obj_cod;";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":opost_obj_cod" => $this->opost_obj_cod), $stmt);	
		
		/********************************************************************/
		// On supprime l'objet et de la base des objets		
		$req    = "DELETE FROM objets WHERE obj_cod = :opost_obj_cod;";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":opost_obj_cod" => $this->opost_obj_cod), $stmt);		
		
		/********************************************************************/
		// Préparation de l'objet pour gérer les lignes d'évènements (une par perso)
		/********************************************************************/
		$ligne_evt = new ligne_evt;
		$ligne_evt->levt_tevt_cod = 103;		
		$ligne_evt->levt_lu = 'N';
		$ligne_evt->levt_visible = 'O';

		// evenement pour l'emetteur
		$ligne_evt->levt_perso_cod1 = $this->opost_emet_perso_cod   ;
		$ligne_evt->levt_cible = $this->opost_dest_perso_cod  ;			
		$ligne_evt->levt_texte = "L’objet « " . $nom_objet . " » (" . (1*$this->opost_obj_cod) . ") envoyé par [perso_cod1] pour [cible] a été consfisqué par le relais de la poste.";
		$ligne_evt->stocke(true);		// Nouvel évènement	

		// evenement pour le destinataire (si le perso du destinataire n'a pas été détruit)
		if ($this->opost_dest_perso_cod)
		{
			$ligne_evt->levt_perso_cod1 = $this->opost_dest_perso_cod   ;
			$ligne_evt->levt_cible = $this->opost__perso_cod  ;			
			$ligne_evt->levt_texte = "L’objet « " . $nom_objet . " » (" . (1*$this->opost_obj_cod) . ") envoyé par [cible] pour [perso_cod1] a été consfisqué par le relais de la poste.";
			$ligne_evt->stocke(true);		// Nouvel évènement	
		}
    }	

	
    /**
     * Retourne un tableau du matos qu'un perso peut envoyer par les relais de la poste
     * @global bdd_mysql $pdo
     * @param $perso_cod : chercher dans l'inventaire de ce perso 
	 * @return array
     */
	 
    function getObjetsDeposableRelaisPoste($perso_cod)
    { 
	    $retour = array();
		
        $pdo    = new bddpdo;
        $req    = "select obj_etat, tobj_cod, gobj_cod, tobj_libelle, CASE WHEN perobj_identifie = 'N' THEN obj_nom_generique ELSE obj_nom END obj_nom, obj_cod, obj_poids, gobj_tobj_cod, gobj_url
	               from perso_objets
	               INNER JOIN objets ON obj_cod = perobj_obj_cod
	               INNER JOIN objet_generique ON gobj_cod = obj_gobj_cod
	               INNER JOIN type_objet ON tobj_cod = gobj_tobj_cod
	               WHERE perobj_perso_cod = :perobj_perso_cod
	               AND perobj_equipe = 'N'
	               AND perobj_identifie = 'O'
	               AND gobj_tobj_cod not in (5,11,14,22,28,30,34)
	               ORDER BY tobj_libelle,gobj_nom ";

        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":perobj_perso_cod" => $perso_cod), $stmt);
		while ( $result = $stmt->fetch() )
        { 
            $retour[] = $result ;
        }

        return $retour;		
	}
		
    /**
     * Retourne true si l'opération c'est bien déroulée
	 * Cette fonction réalise les actions nécéssaires dans l'inventaire du perso emmeteur pour pour lui retirer l'objet à la poste:
	 *   - Suppression des transactions en cours sur l'objet s'il y en avaient
	 *   - suppression de l'objet de l'inventaire
	 *   - Ajout d'une ligne d'évenement
	 *   - sauvegarde le present objet
     * @global bdd_mysql $pdo
	 * @return boolean
     */
	 
    function deposeObjetRelaisPoste()
    { 
        $pdo    = new bddpdo;
		
		/********************************************************************/
		// On vérifie que l’objet est bien dans l’inventaire du perso, on recupère son nom generique au passage
		/********************************************************************/
		$req    = "SELECT perobj_cod, obj_nom_generique 
				   	FROM perso_objets 
					INNER JOIN objets ON obj_cod = perobj_obj_cod 
					INNER JOIN objet_generique on gobj_cod = obj_gobj_cod 
					WHERE perobj_perso_cod = :opost_emet_perso_cod AND perobj_obj_cod = :opost_obj_cod;";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":opost_emet_perso_cod" => $this->opost_emet_perso_cod,":opost_obj_cod" => $this->opost_obj_cod), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;	// Anomalie (objet pas/plus dans dans l'inventaire)!
        }	
		$nom_objet = $result["obj_nom_generique"] ; 

		/********************************************************************/
		// Préparation de l'objet pour gérer les lignes d'évènements 
		/********************************************************************/
		$ligne_evt = new ligne_evt;
		$ligne_evt->levt_perso_cod1 =  $this->opost_emet_perso_cod ;
				
		/********************************************************************/
		// On supprime les transactions sur l'objet s'il y en avait 
		/********************************************************************/
		$req    = "DELETE FROM transaction WHERE tran_obj_cod = :opost_obj_cod;";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":opost_obj_cod" => $this->opost_obj_cod), $stmt);
		if ($stmt->rowCount()>0) 
		{
			// Mettre la ligne d'événement correpondante
			$ligne_evt->levt_tevt_cod = 17;			
			$ligne_evt->levt_texte = "La transaction en cours sur l’objet « " . $nom_objet . " » (" . (1*$this->opost_obj_cod) . ") a été annulée !";
			$ligne_evt->levt_lu = 'O';
			$ligne_evt->levt_visible = 'N';
			$ligne_evt->stocke(true);	// Nouvel évènement
		}
		
		/********************************************************************/
		// On supprime l'objet de l'inventaire (il est maintenant à la poste)
		$req    = "DELETE FROM perso_objets WHERE perobj_obj_cod = :opost_obj_cod;";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":opost_obj_cod" => $this->opost_obj_cod), $stmt);		
	
		// Cet un nouveau dépot, il faut l'ajouter dans la base de donnée
		$this->stocke(true);
					
		// Mettre la ligne d'événement correpondante
		$ligne_evt->levt_tevt_cod = 101;					
		$ligne_evt->levt_texte = "[perso_cod1] a déposé l’objet « " . $nom_objet . " » (" . (1*$this->opost_obj_cod) . ") au relais de la poste pour [cible].";
		$ligne_evt->levt_cible = $this->opost_dest_perso_cod ;			
		$ligne_evt->levt_lu = 'N';
		$ligne_evt->levt_visible = 'O';
		$ligne_evt->stocke(true);		// Nouvel évènement	
		
		return true;
	}
		
    /**
     * Retourne true si l'opération c'est bien déroulée
	 * Cette fonction réalise les actions nécéssaires dans l'inventaire du perso destinataire pour ajouter un objet qui vient d'être retiré:
	 *   - ajout de l'objet de l'inventaire	 
	 *   - Ajout d'une ligne d'évenement
	 *   - supression du présent objet de la base 
     * @global bdd_mysql $pdo
	 * @return boolean
     */
	 
    function retraitObjetRelaisPoste()
    { 
        $pdo    = new bddpdo;
		
		/********************************************************************/
		// Vérifier si l'objet a déjà été identifié par le perso
		/********************************************************************/
		$req    = "SELECT pio_cod FROM perso_identifie_objet WHERE pio_perso_cod=:pio_perso_cod AND pio_obj_cod=:pio_obj_cod ";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":pio_perso_cod" => $this->opost_dest_perso_cod,":pio_obj_cod" => $this->opost_obj_cod), $stmt);
        $perobj_identifie = ($stmt->rowCount()==0) ? 'N' : 'O' ;

		
		/********************************************************************/
		// On insert l'objet dans l’inventaire du perso
		/********************************************************************/
		$req    = "INSERT INTO perso_objets (perobj_perso_cod, perobj_obj_cod, perobj_identifie) VALUES(:perobj_perso_cod, :perobj_obj_cod, :perobj_identifie);";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":perobj_perso_cod" => $this->opost_dest_perso_cod,":perobj_obj_cod" => $this->opost_obj_cod, ":perobj_identifie" => $perobj_identifie), $stmt);
        if ($stmt->rowCount()==0) 
        {
            return false;	// Anomalie !
        }	

		// L'objet a été insérer dans l'inventaire on le supprime immediatement du relais poste (action en base)
		$this->supprime();	
		
		/********************************************************************/
		// On vérifie que l’objet est bien dans l’inventaire du perso, on recupère son nom generique au passage
		/********************************************************************/
		$req    = "SELECT perobj_cod, obj_nom_generique 
				   	FROM perso_objets 
					INNER JOIN objets ON obj_cod = perobj_obj_cod 
					INNER JOIN objet_generique on gobj_cod = obj_gobj_cod 
					WHERE perobj_perso_cod = :opost_dest_perso_cod AND perobj_obj_cod = :opost_obj_cod;";
        $stmt   = $pdo->prepare($req);
        $stmt   = $pdo->execute(array(":opost_dest_perso_cod" => $this->opost_dest_perso_cod,":opost_obj_cod" => $this->opost_obj_cod), $stmt);
        if (!$result = $stmt->fetch())
        {
            return false;	// Anomalie !
        }	
		$nom_objet = $result["obj_nom_generique"] ; 
		
		/********************************************************************/
		// Préparation de l'objet pour gérer les lignes d'évènements 
		/********************************************************************/
		$ligne_evt = new ligne_evt;
		$ligne_evt->levt_perso_cod1 =  $this->opost_dest_perso_cod ;							

		// Mettre la ligne d'événement correpondante
		$ligne_evt->levt_tevt_cod = 102;				 //"[perso_cod1] a retiré un objet au relais de la poste."	
		$ligne_evt->levt_texte = "Au relais de la poste [perso_cod1] a retiré l’objet « " . $nom_objet . " » (" . (1*$this->opost_obj_cod) . ") envoyé par [cible].";
		$ligne_evt->levt_cible = $this->opost_emet_perso_cod ;			
		$ligne_evt->levt_lu = 'N';
		$ligne_evt->levt_visible = 'O';
		$ligne_evt->stocke(true);		// Nouvel évènement	
		
		return true;	
	}	
}
