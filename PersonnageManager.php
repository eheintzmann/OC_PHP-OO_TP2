<?php

/*
 * PersonnageManager.php
 * 
 * Copyright 2015 Eric Heintzmann <heintzmann.eric@free.fr>
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 * 
 * 
 */

class PersonnageManager{
	private $_db;
	
	public function __construct($db){
		$this->setDb($db);
	}
	
	public function db(){
		return $this->_db;
	}
	
	public function setDb(PDO $db){
		$this->_db = $db;
	}
	
	public function ajouterPersonnage(Personnage $perso){
		$req = $this->db()->prepare('INSERT INTO Personnage(nom) VALUES(:nom);');
		//$req->bindValue(':id', $perso->id(), PDO::PARAM_INT);
		$req->bindValue(':nom', $perso->nom(), PDO::PARAM_STR);
		//$req->bindValue(':degats',$perso->degats(), PDO::PARAM_INT);
		$req->execute();
		
		$perso->hydrate([
						'id' => $this->db()->lastInsertId(),
						'degats' => 0
						]);
		
		
	}
	
	public function supprimerPersonnage(Personnage $perso){
		$req = $this->db()->prepare('DELETE FROM Personnage WHERE id = :id;');
		$req->bindValue(':id',$perso->id());
		$req->execute();
	}
	
	public function selectionnerPersonnage($x){
		if (is_int($x)){
			$tab = $this->db()->query('SELECT * FROM Personnage WHERE id = ' . $x)->fetch(PDO::FETCH_ASSOC);
			return new Personnage($tab);
		}
		elseif(is_string($x)){
			$tab = $this->db()->query('SELECT * FROM Personnage WHERE nom = "'.$x.'";')->fetch(PDO::FETCH_ASSOC);
			return new Personnage($tab);
			
		}
	}
	
	public function count(){
		return $this->db()->query('SELECT COUNT(id) AS nb_perso FROM Personnage;')->fetchColumn();
	}
	
	public function existe($x){
		if (is_int($x)){
			return (bool) -$this->db()->query('SELECT COUNT(*) FROM Personnage WHERE id = '.$x)->fetchColumn();
		}
		elseif(is_string($x)){
			return (bool) $this->db()->query('SELECT COUNT(*) FROM Personnage WHERE nom = "'. $x . '";')->fetchColumn();
		}
		else{
			return false;
		}
	}
	
	public function listerPersonnages(){
		$persos = [];
		$req = $this->db()->prepare('SELECT id, nom, degats FROM Personnage ORDER BY nom;');
		$req->execute();
		while ($donnees = $req->fetch(PDO::FETCH_ASSOC)){
			$persos[] = new Personnage($donnees);
		}
		return $persos;
	}
	
	public function updatePersonnage(Personnage $perso){
		$req = $this->db()->prepare('UPDATE Personnage SET nom=:nom, degats=:degats WHERE id=:id;');
		$req->bindValue(':nom', $perso->nom(), PDO::PARAM_STR);
		$req->bindValue(':degats', $perso->degats(), PDO::PARAM_INT);
		$req->bindValue(':id', $perso->id(), PDO::PARAM_INT);
		$req->execute();
	}
}
