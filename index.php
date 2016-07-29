<?php
function chargerClasse($classe){
	require $classe.'.php';
}
spl_autoload_register('chargerClasse');
session_start();
if(isset($_GET['deconnexion'])){
	session_destroy();
	header('Location: .');
	exit();
}
if(isset($_SESSION['perso'])){
	$perso = $_SESSION['perso'];
}
$db = new PDO('mysql:host=localhost;dbname=mini_jeu_de_combat;charset=utf8', 'tp', 'pt', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
$pm = new PersonnageManager($db);

if (isset($_POST['nom'])){
	$nom = $_POST['nom'];
	if (isset($_POST['creer'])){
		if (Personnage::nomValide($nom)){
			if ( $pm->existe($nom)){
				$message = 'Le personnage nommé <em>"'.htmlspecialchars($nom).'"</em> existe déjà';
			}
			else{
				$perso = new Personnage(['nom'  => $nom]);
				$pm->ajouterPersonnage($perso);
			}
		}
		else{
			$message = 'Le nom <em>"'.htmlspecialchars($nom).'"</em> est invalide';
		}
	}
	elseif(isset($_POST['utiliser'])){
		if ($pm->existe($nom)){
			$perso = $pm->selectionnerPersonnage($nom);
		}
		else{
			$message = 'Le personnage <em>"'.htmlspecialchars($nom).'"</em> n\'existe pas';
		}
	}
}
elseif(isset($_GET['frapper'])){
	if ($pm->existe($_GET['frapper'])){
		$perso2 = $pm->selectionnerPersonnage($_GET['frapper']);
		switch ($perso->frapperPesonnage($perso2)){
			case Personnage::CEST_MOI:
				$message = 'Le personnage <em>"'.$perso->nom().'"</em> ne peut se frapper lui-même';
				break;
			case Personnage::PERSONNAGE_FRAPPE:
				$message = htmlspecialchars($perso->nom()).' a frappé '.htmlspecialchars($perso2->nom());
				$pm->updatePersonnage($perso2);
				break;
			case Personnage::PERSONNAGE_TUE:
				$message = htmlspecialchars($perso->nom()).' a frappé et tué '.htmlspecialchars($perso2->nom());
				$pm->supprimerPersonnage($perso2);
				break;
			default:
				$message = "Erreur inconnue";
		}
	}
	else {
		$message = 'le personnage "<em>' . htmlspecialchars($_GET['frapper']) . '"</em> n\'existe pas et ne peut dons être frappé'; 
	}
}
if(isset($perso)){
	$_SESSION['perso'] = $perso;
}
?>
<!DOCTYPE html>
<html lang="fr">
<!--
   index.php
   
   Copyright 2015 Eric Heintzmann <heintzmann.eric@free.fr>
   
   This program is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.
   
   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.
   
   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Software
   Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
   MA 02110-1301, USA.
   
   
-->

<head>
<meta charset="utf-8" />
<title>Mini jeu de combat</title>
<link rel="stylesheet" href="style.css">
<meta name="generator" content="Geany 1.26" />
</head>

<body>
<?php
if (isset($_SESSION['perso'])){
	echo '<div id="deconnexion"><a href="?deconnexion=yes">Changer de personnage</a></div>'."\n";
}
?>
	<p id="nb_persos">Il existe <?php echo $pm->count(); ?> personnage(s) dans la BDD</p>

<?php
if(isset($message)){
	echo '<p id="message">'.$message.'</p>'."\n";
}
if(isset($perso)){
?>
	<fieldset><legend> Mon personnage</legend>
		<table>
			<tr><td>id : </td><td><?php echo $perso->id() ?></td></tr>
			<tr><td>nom : </td><td><?php echo htmlspecialchars($perso->nom()); ?></td></tr>
			<tr><td>dégâts : </td><td><?php echo $perso->degats() ?></td></tr>
		</table>
	</fieldset>
	
	<fieldset>
		<legend>Ennemi(s) à frapper</legend>
		<table>
		<?php
		foreach($pm->listerPersonnages() as $ennemi){
			if (! ($perso->id() == $ennemi->id())){
				echo '<tr><td><a href="?frapper='.$ennemi->nom().'" >'.htmlspecialchars($ennemi->nom()).'</a></td><td> (dégâts : '.$ennemi->degats().')</td></tr>'."\n";
			}
		}
		?>
		</table>
	</fieldset>
<?php	
}
else {
?>	
	<form action="./" method="post" >
		<p>
			<label for="nom" >Nom : </label>
			<input type="text" name="nom" id="nom" list="liste_persos" placeholder="Ex: Aragorn" autocomplete="off" />
			<datalist id="liste_persos" >
				<?php 
				$persos = $pm->listerPersonnages();
				foreach($persos as $perso) {
					echo '<option value = "'.htmlspecialchars($perso->nom()).'">'.htmlspecialchars($perso->nom()).'</option>'."\n";
				}
				?>
			</datalist>
		</p>
		<input type="submit" value="Utiliser personnage" name="utiliser" />
		<input Type="submit" value="Créer personnage" name="creer" />
	</form>
<?php
}
?>
</body>

</html>
