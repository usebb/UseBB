<?php

/*
	Copyright (C) 2003-2005 UseBB Team
	http://www.usebb.net
	
	$Header$
	
	This file is part of UseBB.
	
	UseBB is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	UseBB is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with UseBB; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

//
// Die when called directly in browser
//
if ( !defined('INCLUDED') )
	exit();

//
// Initialize a new faq holder array
//
$faq = array();

//
// Define headings and questions
//

$faq[] = array('--', 'Gebruikersaccounts');
$faq[] = array('Is registratie verplicht?', 'Registratie kan vereist zijn voor het plaaten van berichten in dit forum, dit hangt af of de administrator(s) het plaatsen van berichten door gasten toestaan (kan verschillend zijn per forum). Over het algemeen is registreren een goed idee daar het extra functionaliteit biedt.');
$faq[] = array('Wat zijn de voordelen van registreren?', 'Vooreerst krijg je een persoonlijke account met je bijnaam welke enkel voor jou beschikbaar zal zijn. Je krijgt ook een persoonlijk profiel waar je desgewenst bijkomende informatie over jezelf kan plaatsen en je kan het forum aanapssen aan je wensen via de Instellingen Bewerken-functie, welke ook het veranderen van taalpakket of template kan inhouden.');
$faq[] = array('Wat heb ik nodig om een account te registreren?', 'Om te registreren dien je een bijnaam (gebruikersnaam) te kiezen, welke de sleutel tot je account zal zijn. Indien je dat wenst, kan je ook je echte naam gebruiken in plaats van je bijnaam. Merk op dat je na het registreren je gebruikersnaam niet zelf kan aanpassen. Je hebt ook een werkend e-mail adres nodig (dit kan vereist zijn voor het activeren van je account) en je dient een wachtwoord te kiezen (welke je steeds kan aanpassen).');
$faq[] = array('Wat als ik mijn wachtwoord vergeet of mijn e-mail adres niet meer werkt?', 'Je kan altijd een nieuw wachtwoord aanvragen via de link op het inlogformulier. Indien je e-mail adres niet meer werkt, contacteer een administrator. Hij/zij kan je account aanpassen aan je nieuwe e-mail adres.');

$faq[] = array('--', 'Omtrent UseBB');
$faq[] = array('Wie maakte dit forum?', 'Dit discussieforum, genaamd <em>UseBB</em>, wordt ontwikkeld door het UseBB Team. UseBB is Open Source-software vrijgegeven onder de GPL-licentie. Je kan UseBB gratis downloaden van <a href="http://www.usebb.net">www.usebb.net</a>. Merk op dat de administrator(s) van dit forum bijkomende functionaliteit kunnen hebben toegevoegd.');
$faq[] = array('Ik heb een klacht over dit forum. Aan wie richt ik mij?', 'Indien je een klacht hebt over de forumsoftware zelf, niet de inhoud, ben je vrij dit aan het UseBB Team te <a href="http://www.usebb.net">melden</a>. Voor elke andere klacht dien je je te wenden tot de administrator(s) van dit forum/deze website.');

?>
