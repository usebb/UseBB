<?php

/*
	Copyright (C) 2003-2011 UseBB Team
	http://www.usebb.net
	
	$Id$
	
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
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
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
$faq[] = array('Moet ik me registreren?', 'Registratie kan vereist zijn om berichten in dit forum te plaatsen, dit hangt af of de administratoren het plaatsen van berichten door gasten toestaan (dit kan afzonderlijk per forum zijn). Over het algemeen is registreren een goed idee omdat het veel extra mogelijkheden biedt.');
$faq[] = array('Wat zijn de voordelen van het registreren?', 'Vooreerst krijg je een eigen account die gebonden is aan je bijnaam welke dan uitzonderlijk voor jezelf beschikbaar zal zijn. Je krijgt ook een eigen profielpagina waar je eventueel extra informatie over jezelf kan opgeven, en waar je verscheidene opties van het forum kan instellen, welke het veranderen van template set en vertaling kan inhouden. Een interessantie mogelijkheid is het inschrijven op onderwerpen.');
$faq[] = array('Wat heb ik nodig om een account te registreren?', 'Om te registreren dien je een bijnaam (gebruikersnaam) te kiezen welke de sleutel tot je account zal zijn. Merk op dat je deze gebruikersnaam niet zelf kan aanpassen, alhoewel je wel ten alle tijde je weergegeven naam kan veranderen terwijl je gebruikersnaam gelijk blijft. Je hebt ook een werkend e-mailadres nodig (omdat dit vereist kan zijn om je account te activeren) en je dient een wachtwoord te kiezen (welke je nadien kan aanpassen).');
$faq[] = array('Ik heb geen activatie-mail ontvangen!', 'Het is mogelijk dat je kan inloggen zonder te activeren. Indien niet, probeer een nieuw wachtwoord aan te vragen. Indien dit niet werkt, contacteer de administrator.');
$faq[] = array('Wat als ik mijn accountinformatie vergeet of mijn e-mailadres niet meer werkt?', 'Je kan altijd een nieuw wachtwoord aanvragen via de link op het inlogformulier. Indien je e-mailadres niet meer werkt of je het vergeten bent, neem contact op met een administrator. Hij of zij kan je het e-mailadres meedelen welke je gebruikte bij het registreren of indien nodig een nieuw e-mailadres instellen.');
$faq[] = array('Wat zijn moderators en administrators?', 'Een moderator is een persoon die &eacute;&eacute;n of meerdere forums controleert op niet-passende inhoud. Hij of zij kan mogelijks ook gebruikers in zijn/haar forum(s) helpen. Een administrator is een moderator op alle forums, ook kan deze de configuratie van het forum aanpassen en moderators aanstellen.');
$faq[] = array('Hoe word ik een moderator of administrator?', 'Normaal gezien is dit niet mogelijk, maar contacteer een administrator indien je ge&iuml;nteresseerd bent.');
$faq[] = array('Kan ik mijn rank aanpassen?', 'Nee, enkel administrators kunnen leden een aangepaste rank geven.');

$faq[] = array('--', 'Subscripties');
$faq[] = array('Hoe kan ik op een onderwerp inschrijven?', 'Onderaan elk onderwerp vind je een link om jezelf op een onderwerp in te schrijven. Je kan alleen inschrijven indien je ingelogd bent.');
$faq[] = array('Hoe kan ik van een onderwerp uitschrijven?', 'Je kan uitschrijven via de link onderaan het onderwerp of via het subscripties-overzicht in het paneel.');

$faq[] = array('--', 'Je Profiel');
$faq[] = array('Hoe kan ik mijn gebruikersnaam aanpassen?', 'Je kan je gebruikersnaam niet aanpassen. Enkel administrators hebben hiervoor permissie. Je kan hoewel ten alle tijde je weergegeven naam aanpassen.');
$faq[] = array('Wat is het verschil tussen Gebruikersnaam, Weergegeven naam en Echte naam?', 'Je gebruikersnaam, welke je gebruikt om in te loggen, is constant en kan niet door jezelf worden veranderd. Je weergegeven naam (welke bij het registreren gelijk is aan je gebruikersnaam) is de naam welke publiek wordt weergegeven. Deze naam kan je ten alle tijde veranderen. Merk op dat je nog steeds dient in te loggen met je onveranderlijke gebruikersnaam. Je echte naam, welke optioneel in te vullen is, is zichtbaar op je profielpagina.');
$faq[] = array('Mijn taal ontbreekt in het selectieveld!', 'Vraag een administrator of hij/zij een vertaling voor je taal wilt installeren.');
$faq[] = array('Waarom kan ik geen andere template selecteren?', 'Waarschijnlijk zijn er op dit forum geen andere templates beschikbaar.');

$faq[] = array('--', 'Onderwerpen en Berichten');
$faq[] = array('Wat zijn onderwerpen en berichten?', 'Een onderwerp is een groepering van berichten in een bepaald forum. Indien je de naam van een forum op de forum-index aanklikt, zie je een overzicht van de onderwerpen in dat forum.');
$faq[] = array('Wat zijn sticky onderwerpen?', 'Sticky onderwerpen blijven steeds bovenaan de andere onderwerpen in het forum staan. Doorgaans kunnen gewone leden geen sticky onderwerpen plaatsen, maar moderators en administrators wel.');
$faq[] = array('Waarom kan ik geen nieuwe onderwerpen of antwoorden plaatsen?', 'Indien er geen link verschijnt om nieuwe onderwerpen of antwoorden te plaatsen, dan heb je geen toestemming om nieuwe onderwerpen of berichten te plaatsen in dat forum. Dit kan een instelling zijn van het forum of het onderwerp kan gesloten zijn door een moderator. Contacteer een administrator bij twijfel.');
$faq[] = array('Wat zijn BBCode en smilies?', 'BBCode zijn tags welke je kan gebruiken om bepaalde elementen aan een bericht toe te voegen. Probeer er enkele en gebruik de voorbeeld-functie om het effect te zien. Smilies worden gebruikt om emoties in een bericht uit te drukken.');
$faq[] = array('Mijn berichtenteller verhoogt niet bij het plaatsen van berichten!', 'In bepaalde forums kan het verhogen van berichtentellers uitgeschakeld zijn. Dit is gewoonlijk zo bij test- of algemene forums.');
$faq[] = array('Waarom moet ik wachten tussen het plaatsen van twee berichten?', 'Flood-protectie wordt gebruikt om het spammen tegen te gaan of te voorkomen dat de server wordt overspoeld met aanvragen. Gelieve enkele ogenblikken te wachten en het bericht opnieuw te verzenden.');
$faq[] = array('Waarom blijven links in mijn handtekening en/of berichten onklikbaar?', 'Het forum kan een &quot;potenti&euml;le spammer&quot;-status aan nieuwe accounts toekennen, wat resulteert in verminderde rechten voor het posten van links op het forum. Meestal wordt deze status verwijderd na het plaatsen van een aantal berichten. Anderzijds of wanneer dit een probleem is, gelieve de administrator te contacteren.');

$faq[] = array('--', 'Diversen');
$faq[] = array('Ik ontvang geen enkele e-mail van dit forum!', 'Controleer of de e-mails niet worden geblokkeerd door antispam-software, of het e-mailadres in je profiel werkt niet meer.');
$faq[] = array('Wat is de RSS feed?', 'RSS feeds worden gebruikt om recente informatie van een website of forum weer te geven in een nieuws- of RSS-lezer of bepaalde browsers.');
$faq[] = array('Waar in mijn profiel kan ik mijn Google Talk-account plaatsen?', 'Google Talk is een IM-netwerk dat gebruik maakt van het Jabber/XMPP-protocol. Je kan je gebruikersnaam (volledig, inclusief de domeinnaam) opgeven in je profiel bij het Jabber/XMPP-veld.');

$faq[] = array('--', 'Omtrent UseBB');
$faq[] = array('Wie maakte dit forum? Wat is UseBB?', 'Dit forum, genaamd <em>UseBB</em>, wordt ontwikkeld door het UseBB Project. UseBB is Open Source-software vrijgegeven onder de GPL. Je kan UseBB gratis downloaden op <a href="http://www.usebb.net">www.usebb.net</a>. Merk op dat de administrator(s) van dit forum extra functionaliteit kunnen hebben toegevoegd.');
$faq[] = array('Zijn de makers van UseBB verantwoordelijk voor dit forum?', 'Nee, dit forum wordt beheerd door de webmaster van deze website. Het UseBB Project kan in geen geval aansprakelijk gesteld worden.');
$faq[] = array('Ik heb een klacht over dit forum. Tot wie richt ik mij?', 'Indien je een klacht hebt over de software van dit forum, ben je vrij dit te <a href="http://www.usebb.net">melden</a>. Voor elke andere klacht dien je je tot een administrator te begeven.');

?>
