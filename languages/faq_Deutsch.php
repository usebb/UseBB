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

$faq[] = array('--', 'Benutzerzug&auml;nge');
$faq[] = array('Muss ich mich f&uuml;r die Nutzung registrieren?', 'Eine Registrierung kann an diesem Forum eine Voraussetzung sein, um Beitr&auml;ge zu schreiben. Diese Einstellung h&auml;ngt davon ab, ob die Administratoren einen Gast-Zugang mit Schreibrechten aktiviert haben. Allgemein ist es aber eine gute Idee sich einen eigenen Zugang anzulegen um zus&auml;tzliche Features nutzen zu k&ouml;nnen.');

$faq[] = array('Was sind die Vorteile eines eigenen Zugangs?', '
Als allererstes bekommst Du einen Zugang mit Deinem eigenen Nickname, der nur Dir zu Verf&uuml;gung steht. Du bekommst dazu ein pers&ouml;nliches Profil das Du mit zus&auml;tzlichen Informationen &uuml;ber Deine Person f&uuml;llen kannst. Dazu kannst Du das Forum Deinen W&uuml;schen anpassen, indem Du mit der speziellen Options-Schaltfl&auml;che z.B. die Sprache oder die Design-Vorlage ver&auml;nderst.');

$faq[] = array('Welche Schritte bedarf es um einen Zugang zu registrieren?', '
Um einen eigenen Zugang zu aktivieren, suchst Du Dir einen Nickname (gleichzeitig Dein Benutzername) aus. Wenn Du m&ouml;chtest, kannst Du nat&uuml;rchlich auch Deinen richtigen Namen daf&uuml;r benutzen. Dann brauchst Du nur noch eine funktionierende Email-Adresse (je nach Einstellung des Forums) und ein gut gew&auml;hltes Passwort (dies kannst Du sp&auml;ter auch &auml;ndern).');
$faq[] = array('Ich habe mein Passwort vergessen - meine Email-Adresse funktioniert nicht mehr?', 'Du kannst jederzeit ein neues Passwort  &uuml;ber die Verkn&uuml;pfung beim Loginformular anfordern. Sollte jedoch Deine Email-Adresse nicht mehr funktionieren, kontaktiere bitte einen Administrator. Er/Sie k&ouml;nnte Deine neue Email-Adresse in Deinem Zugang eintragen.');

$faq[] = array('--', 'Themen rund um UseBB');
$faq[] = array('Wer hat dieses Forum programmiert?', '
Dieses Forum - <em>UseBB</em> - wurde vom UseBB Team entwickelt. UseBB ist eine Open Source Software, die unter der GPL Lizenz ver&ouml;ffentlicht wird. Du kannst UseBB kostenlos  unter der Adresse <a href="http://www.usebb.net">www.usebb.net</a> downloaden. Bitte beachte das die Administratoren zus&auml;tzliche Features in diese - ihre -  Version eingebaut haben k&ouml;nnten.');

$faq[] = array('Ich habe Probleme mit diesem Forum. An wen kann ich mich wenden?', '
Wenn Du nicht ein Problem mit dem Inhalt, sondern mit der Forumssoftware selbst hast, bist Du herzlich eingeladen dem <a href="http://www.usebb.net">UseBB Team</a> Deine Beschwerden mitzuteilen. F&uuml;r alle anderen Angelegenheiten wende Dich bitte an die Administratoren dieses Forums/Website.');
