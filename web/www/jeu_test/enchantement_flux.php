<?php 

$req_comp = "select pcomp_modificateur, pcomp_pcomp_cod from perso_competences 
    where pcomp_perso_cod = $perso_cod and pcomp_pcomp_cod in (88, 102, 103)";
$db->query($req_comp);
$pa = 0;
$pa2 = 0;
if($db->next_record())
{
	$niveau = $db->f("pcomp_pcomp_cod");
	if ($niveau == 102)
	{
		$pa = $db->getparm_n(116) -1;
		$pa2 = $db->getparm_n(116) + 2;
	}
	else if ($niveau == 103)
	{
		$pa = $db->getparm_n(116) - 2;
		$pa2 = $db->getparm_n(116);
	}
	else
	{
		$pa = $db->getparm_n(116);
	}
	if(!isset($methode))
	{
		$methode = "debut";
	}

	switch($methode)
	{
		case "debut":
			$contenu_page .= '
				<p align="center"><br>Vous avez la possibilité de tester la puissance des vents magiques. Plusieurs méthodes peuvent se présenter à vous :<br>
				<form method="post" action="' . $PHP_SELF. '">
				<br><p align="center" class="soustitre2"><b>Analyse locale</b></p> 
				Cette recherche vous permettra de tenter de sonder les vents magiques proches de vous.<br>
				<input type="hidden" name="methode" value="detecter2">
				<input type="hidden" name="t_ench" value="' . $t_ench . '">
				<br><input type="submit" value="Rechercher ('. $pa .' PA)"  class="test">
				</form><br>';
			if ($niveau == 102 or $niveau == 103)
			{
				$contenu_page .= '<form method="post" action="' . $PHP_SELF. '">
					<br><p align="left" class="soustitre2"> <b>Recherche poussée.</b></p>
					Elle vous permettra de scruter une zone à quatre positions ou moins autour de vous.<br>	
					<input type="hidden" name="methode" value="detecter3">
					<input type="hidden" name="t_ench" value="' . $t_ench . '">
					<br><input type="submit" value="Rechercher ('. $pa2 .'PA)"  class="test">
					</form><br>
					</p>
					<br>';
			}
		break;

		case "detecter2":
			$req_pa = "select perso_pa from perso where perso_cod = $perso_cod";
			$db->query($req_pa);
			$db->next_record();
			if ($db->f("perso_pa") < $pa)
			{
				$contenu_page .= 'Vous n’avez pas assez de PA !';
				break;
			}
			else
			{
				$req_enl_pa = "update perso set perso_pa = perso_pa - $pa where perso_cod = $perso_cod";
				$db->query($req_enl_pa);
			}
			$contenu_page .= '<p><b>Vous levez le nez, et observez les vents magiques autour de vous.</b></p>
				<table background="../../images/fond5.gif" border="0" cellspacing="0" cellpadding="0" style="margin:15px;">';
			// POSITION DU JOUEUR
			$req_position = "select pos_x, pos_y, pos_etage
				from perso_position, positions
				where ppos_perso_cod = $perso_cod and ppos_pos_cod = pos_cod ";
			$db->query($req_position);
			$db->next_record();
			$position_x = $db->f("pos_x");
			$position_y = $db->f("pos_y");
			$perso_pos_etage = $db->f("pos_etage");
			for ($y = -2; $y < 4; $y++)
			{
				$contenu_page .= '<tr>';
				for ($x = -2; $x < 4; $x++)
				{
					if(($y * $y + $x * $x) < 8)
					{
						$db2 = new base_delain;
						$req_position = "select pos_magie from positions
							where pos_etage = $perso_pos_etage
								and pos_x = $position_x + $x
								and pos_y = $position_y - $y";
						$db2->query($req_position);
						$magie = 0;
						if ($db2->next_record())
						{
							$magie = $db2->f("pos_magie");
						}
						$pos_case_x = $position_x + $x;
						$pos_case_y = $position_y - $y;

						$contenu_page .= afficheVents($magie, $pos_case_x, $pos_case_y);
					}
					else
					{
						$contenu_page .= '<td></td>';
					}
				}
				$contenu_page .= '</tr>';
			}
			$contenu_page .= '</table>';
			$contenu_page .= afficheLegende();
		break;

		case "detecter3":
			$req_pa = "select perso_pa from perso where perso_cod = $perso_cod";
			$db->query($req_pa);
			$db->next_record();
			if ($db->f("perso_pa") < $pa2)
			{
				$contenu_page .= 'Vous n’avez pas assez de PA !';
				break;
			}
			else
			{
				$req_enl_pa = "update perso set perso_pa = perso_pa - $pa2 where perso_cod = $perso_cod";
				$db->query($req_enl_pa);
			}

			$contenu_page .= '<p><b>Vous levez le nez, et observez les vents magiques autour de vous.</b></p>
				<table background="../../images/fond5.gif" border="0" cellspacing="0" cellpadding="0" style="margin:15px;">';
			// POSITION DU JOUEUR
			$req_position = "select pos_x,pos_y,pos_etage
				from perso_position,positions
				where ppos_perso_cod = $perso_cod and ppos_pos_cod = pos_cod ";
			$db->query($req_position);
			$db->next_record();
			$position_x = $db->f("pos_x");
			$position_y = $db->f("pos_y");
			$perso_pos_etage = $db->f("pos_etage");
			for ($y = -4; $y < 5; $y++)
			{  
				$contenu_page .= '<tr>';
				for ($x = -4; $x < 5; $x++)
				{
					if(($y * $y + $x * $x) < 30)
					{
						$db2 = new base_delain;
						$req_position = "select pos_magie from positions
							where pos_etage = $perso_pos_etage
								and pos_x = $position_x + $x
								and pos_y = $position_y - $y";
						$db2->query($req_position);
						$magie = 0;
						if ($db2->next_record())
						{
							$magie = $db2->f("pos_magie");
						}
						$pos_case_x = $position_x + $x;
						$pos_case_y = $position_y - $y;

						$contenu_page .= afficheVents($magie, $pos_case_x, $pos_case_y);
					}
					else
					{
						$contenu_page .= '<td></td>';
					}
				}
				$contenu_page .= '</tr>';
			}
			$contenu_page .= '</table>';
			$contenu_page .= afficheLegende();
		break;
	}
}
else 
{
	$contenu_page .= "<p>Vous ne possédez pas la compétence nécessaire</p>";
}

function afficheVents($magie, $x, $y)
{
	global $db;
	$image = '';
	$resultat = '';
	$position = ' Position : X = ' . $x . ' / Y = ' . $y;
	if ($magie < $db->getparm_n(114) && $magie > 500)
	{
		$texte = 'Les vents magiques sont trop faibles ici ! La magie peut amplifier ces vents, mais qui sait dire de combien...';
		$image = 'automap_1_0.gif';
	}
	else if ($magie <= 5000 && $magie >= $db->getparm_n(114))
	{	
		$texte = 'Les vents magiques sont très favorables ici. Profitez en avant qu’ils ne faiblissent !';
		$image = 'automap13vert.gif';
	}
	else if ($magie > 5000)
	{	
		$texte = 'Les vents magiques sont bien trop puissants ! Vous prenez un risque en effectuant des opérations de forgeamage ici !';
		$image = 'automap_1_2.gif';
	}
	else
	{
		$texte = 'Rien ne souffle ici...';
		$image = 'automap_1_1.gif';
	}
	$resultat = '<td width="13" height="13" ><img height="9" src="../../images/' . $image . '" title="' . $texte . $position . '" /></td>';
	return $resultat;
}

function afficheLegende()
{
	$resultat = '<div><p><b>Légende</b></p>';
	$resultat .= '<p><img height="9" src="../../images/automap_1_1.gif" title="Rien ne souffle ici..." /> Rien ne souffle ici...</p>';
	$resultat .= '<p><img height="9" src="../../images/automap_1_0.gif" title="Les vents magiques sont trop faibles ici ! La magie peut amplifier ces vents, mais qui sait dire de combien..." /> Les vents magiques sont trop faibles ici ! La magie peut amplifier ces vents, mais qui sait dire de combien...</p>';
	$resultat .= '<p><img height="9" src="../../images/automap13vert.gif" title="Les vents magiques sont très favorables ici. Profitez en avant qu’ils ne faiblissent !" /> Les vents magiques sont très favorables ici. Profitez en avant qu’ils ne faiblissent !</p>';
	$resultat .= '<p><img height="9" src="../../images/automap_1_2.gif" title="Les vents magiques sont bien trop puissants ! Vous prenez un risque en effectuant des opérations de forgeamage ici !" /> Les vents magiques sont bien trop puissants ! Vous prenez un risque en effectuant des opérations de forgeamage ici !</p>';
	$resultat .= '</div>';
	
	return $resultat;
}

?>
