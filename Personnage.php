<?php

/*
 * Personnage.php
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

abstract class Personnage{
	protected $_id;
	protected $_nom;
	protected $_degats;
	
	const CEST_MOI = 1;
	const PERSONNAGE_TUE = 2;
	const PERSONNAGE_FRAPPE = 3;
	
	public function __construct(array $tab){
		$this->hydrate($tab);
	}
	
	public function id(){
		return $this->_id;
	}
	
	public function nom(){
		return $this->_nom;
	}
	
	public function degats(){
		return $this->_degats;
	}
	
	public function niveau(){
		return $this->_niveau;
	}
	
	public function experience(){
		return $this->_experience;
	}
	
	public function setId($id){
		$id = (int) $id;
		if ($id > 0){
			$this->_id = $id;
		}
	}
	
	public function setNom($nom){
		if (is_string($nom)){
			$this->_nom = $nom;
		}
	}
	
	public function setDegats($degats){
		$degats = (int) $degats;
		if ( $degats >= 0 && $degats <= 100 ){
			$this->_degats = $degats;
		}
	}
	
	public function setNiveau($niveau){
		$niveau = (int) $niveau;
		if (niveau>=1){
			$this->_niveau = $niveau;
		}
	}
	
	public function setExperience($xp){
		$niveau = (int) $xp;
		if (xp >= 0 && xp <= 100){
			$this->_experience = $niveau;
		}
	}
	
	public function frapperPesonnage(Personnage $p){
		if ($p->id() == $this->id()){
			return self::CEST_MOI;
		}
		else {
			return $p->recevoirDegats();
		}		
	}
	
	public function recevoirDegats(){
		$this->setDegats($this->degats() + 5);
		if ($this->degats() >= 100){
			return self::PERSONNAGE_TUE;
		}
		else {
			return self::PERSONNAGE_FRAPPE;
		}
	}
	
	public function hydrate(array $tab){
		foreach($tab as $key => $value){
			$method = 'set'.ucfirst($key);
			if (method_exists($this, $method)){
				$this->$method($value);
			}
		}
	}
	public static function nomValide($nom){
		if (is_string($nom) && !empty($nom)){
			return true;
		}
		else {
			return false;
		}
	}
}	
?>
