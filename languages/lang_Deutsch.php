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
// Initialize a new translations holder array
//
$lang = array();

//
// Define translations
//
$lang['Home'] = 'Start';
$lang['YourPanel'] = 'Dein Zugang';
$lang['Register'] = 'Registrieren';
$lang['FAQ'] = 'FAQ';
$lang['Search'] = 'Suchen';
$lang['ActiveTopics'] = 'Aktive Themen';
$lang['LogIn'] = 'Anmelden';
$lang['LogOut'] = 'Abmelden (%s)';
$lang['MemberList'] = 'Mitgliederliste';
$lang['StaffList'] = 'Mitarbeiterliste';
$lang['Statistics'] = 'Statistiken';
$lang['ContactAdmin'] = 'Admin kontaktieren';
$lang['Forum'] = 'Forum';
$lang['Topics'] = 'Themen';
$lang['Posts'] = 'Beitr&auml;ge';
$lang['LatestPost'] = 'Letzten Beitr&auml;ge';
$lang['RSSFeed'] = 'RSS Feed';
$lang['NewPosts'] = 'Neue Beitr&auml;ge';
$lang['NoNewPosts'] = 'Keine neuen Beitr&auml;ge';
$lang['LockedNewPosts'] = 'Gesperrt (Neue Beitr&auml;ge)';
$lang['LockedNoNewPosts'] = 'Gesperrt (Keine neuen Beitr&auml;ge)';
$lang['Locked'] = 'Gesperrt';
$lang['LastLogin'] = 'Letzte Anmeldung am';
$lang['VariousInfo'] = 'Verschiedene Informationen';
$lang['IndexStats'] = 'Dieses Forum enth&auml;lt %d Beitr&auml;ge zu %d Themen, die von %d Mitglieder geschrieben wurden.';
$lang['NewestMember'] = 'Wir begr&uuml;&szlig;en unser neuestes Mitglied: %s.';
$lang['OnlineUsers'] = '%d Mitglied[er] und %d Reisende[r] waren in den letzten %d Minuten online.';
$lang['Username'] = 'Benutzername';
$lang['CurrentPassword'] = 'Aktuelles Passwort';
$lang['UserID'] = 'Benutzer ID';
$lang['NoSuchForum'] = 'Das Forum %s existiert (nicht mehr)!';
$lang['WrongPassword'] = 'Entschuldigung, aber Dein Passwort ist nicht korrekt! Fordere ein neues Passwort &uuml;ber das Loginformular an,wenn Du Dein Passwort vergessen hast.';
$lang['Reset'] = 'Zur&uuml;cksetzen';
$lang['SendPassword'] = 'Neues Passwort zusenden';
$lang['RegisterNewAccount'] = 'Registriere einen neuen Zugang';
$lang['RememberMe'] = 'Angemeldet bleiben';
$lang['Yes'] = 'Ja';
$lang['No'] = 'Nein';
$lang['NotActivated'] = 'Dein Zugang %s ist noch nicht aktiviert worden. Bitte &uuml;berpruefe Deine Mailbox f&uuml;r Anweisungen, wie Du Deinen Zugang aktivieren kannst.';
$lang['Error'] = 'Fehler';
$lang['Profile'] = 'Profil von %s';
$lang['Level'] = 'Level';
$lang['Administrator'] = 'Administrator';
$lang['Moderator'] = 'Moderator';
$lang['Registered'] = 'Registriert';
$lang['Email'] = 'E-Mail Adresse';
$lang['ContactInfo'] = 'Kontakt';
$lang['Password'] = 'Passwort';
$lang['PasswordAgain'] = 'Passwort (nochmal)';
$lang['EverythingRequired'] = 'Alle Felder sind erforderlich!';
$lang['UserAlreadyExists'] = 'Entschuldigung, aber ein Benutzer namens %s exisiert schon in diesem Forum. Wenn es sich dabei um Deinen Zugang handelt, melde dich an oder fordere ein neues Passwort an. Falls es nicht Dein Zugang ist, w&auml;hle bitte einen anderen Benutzernamen.';
$lang['RegisteredNotActivated'] = 'Dein Zugang %s wurde erstellt. Eine E-Mail mit genauen Aktivierungsanweisungen wurde an %s versandt. Du musst Deinen Zugang aktivieren, bevor Du Dich in diesem Forum anmelden kannst.';
$lang['RegisteredActivated'] = 'Dein Zugang %s wurde erstellt. Eine E-Mail mit Deinen Benutzerdetails wurde an %s versandt. Du kannst Dich sofort am Forum anmelden.';
$lang['Never'] = 'Niemals';
$lang['Member'] = 'Mitglied';
$lang['RegistrationActivationEmailBody'] = 'Hallo,

 hier ist die Forensoftware von [board_name]. Du hast soeben einen neuen Zugang namens [account_name] angelegt, der noch einer Aktivierung bedarf. Bitte klicke auf den folgenden Link um Deinen Zugang zu reaktivieren:

[activate_link]

 - Du kannst den Link aber natuerlich auch ueber Kopieren/Einfuegen in Deinen Browser einfuegen. Du kannst Dich danach mit folgenden Zugangsdaten anmelden:

Benutzername:[account_name]
Passwort: [password]

Wenn Du Dein Passwort vergessen hast, kannst Du ein Neues ueber den Link "Passwort vergessen" beim Loginformular anfordern.

[board_name]
[board_link]
[admin_email]';

$lang['SendpwdActivationEmailSubject'] = 'Neues Passwort';
$lang['NoForums'] = 'Dies ist ein leeres Forum. Der Administrator hat noch keine Bereiche angelegt.';
$lang['AlreadyActivated'] = 'Der Zugang mit der Kennung %d ist schon aktiviert worden.';
$lang['Activate'] = 'Aktivieren';
$lang['Activated'] = 'Dein Zugang %s wurde nun (re)aktiviert. Du kannst Dich nun mit dem Passwort, was Du mit der E-Mail erhalten hast, anmelden.';
$lang['WrongActivationKey'] = 'Der Zugang mit der Kennung %d konnte nicht aktiviert werden, da der Aktivierungscode inkorrekt ist. Bist Du sicher, dass Du in der Zwischenzeit kein neues Passwort angefordert hast?';
$lang['RegisterIt'] = 'Du kannst es &uuml;ber den \'Registrierungs\' Link erstellen.';
$lang['BoardClosed'] = 'Forum geschlossen';
$lang['SendpwdActivationEmailBody'] = 'Hallo,

hier ist die Forumsoftware von [board_name]. Du hast soeben ein neues Passwort fuer Deinen Zugang [account_name] angefordert. Bitte klicke auf den folgenden Link um Deinen Zugang zu reaktivieren:

[activate_link]

 - Du kannst den Link aber natuerlich auch ueber Kopieren/Einfuegen in Deinen Browser eingeben. Du kannst Dich danach mit folgenden Zugangsdaten anmelden:

Benutzername: [account_name]
Passwort:[password]

Wenn Du Dein Passwort vergessen hast, kannst Du ein Neues ueber den folgenden Link zum Loginformular anfordern: 

[board_name]
[board_link]
[admin_email]';

$lang['SendpwdEmailBody'] = 'Hallo,

 hier ist die Forumsoftware von [board_name]. Du hast soeben ein neues Passwort fuer Deinen Zugang [account_name] angefordert. Du kannst Dich  nun mit folgenden Zugangsdaten anmelden:

Benutzername: [account_name]
Passwort: [password]

Wenn Du Dein Passwort vergessen hast, kannst Du ein Neues ueber den  Loginformular des Forums anfordern:

[board_name]
[board_link]
[admin_email]';

$lang['SendpwdEmailSubject'] = 'Neues Passwort';
$lang['SendpwdActivated'] = 'Ein neues Passwort f&uuml;r Deinen Zugang %s wurde erstellt und an die E-Mail Adresse %s geschickt. Du kannst Dich nun mit dem neuen Passwort anmelden.';
$lang['SendpwdNotActivated'] = 'Ein neues Passwort f&uuml;r Deinen Zugang %s wurde erstellt und an die E-Mailadresse %s verschickt. Desweiteren enth&auml;lt die E-Mail Informationen um Deinen Zugang zu reaktivieren.';
$lang['ForumIndex'] = 'Forum Index';
$lang['MissingFields'] = 'Die folgenden Felder wurden nicht ausgef&uuml;llt oder sind inkorrekt: %s.';
$lang['TermsOfUseContent'] = '
Du akzeptierst das alle Beitraege in diesem Forum Meinungen der jeweiligen Autoren ausdruecken, die nicht mit den offiziellen Ansichten der Administration, Moderatoren oder des Webmasters uebereinstimmen muessen.

Du stimmst ueberein, das Du keine beleidigenden, anstoessigen, obszoenen oder unangebrachten Inhalt oder Inhalt, der gesetzlich verboten ist, in diesem Forum zu posten.

Ein Verstoss kann eine moegliche Sperrung oder Loeschung Deines Accounts oder eine Abuse-Meldung bei Deinem Internet Service Provider nach sichziehen.

Deshalb wird u.a. auch Deine IP-Adresse bei jedem Deiner Beitraege gespeichert.

Du akzeptierst auch, das Administratoren und Moderatoren das Recht haben jederzeit Deine Beitraege zu editieren, zu loeschen, zu verschieben und Deine Themen zu sperren, wenn sie die Ansicht haben, das dies noetig ist.

Alle Informationen die Du in diesem Forum beitraegst, wird in einem Datenbanksystem für zukuenftige Zwecke gespeichert. Die Administration wird diese Informationen nicht gegen Deinen Willen an Dritte weitergeben.

Jedoch haften weder Webmaster, Administratoren oder Moderatoren, noch das UseBB-Team fuer Informationlecks in Folge eines Hacking-Versuchs.

Dieses System benutzt Cookies um temporaere Informationen auf Deinem lokalen Computer zu speichern. Desweiteren kann ein Cookie auch genutzt werden um Deinen Benutzernamen und Dein Passwort in einer verschluesselten Variante fuer einen automatischen Anmeldeprozess zu sichern.

Wenn Du keinerlei Cookies verwenden moechtest, wende Dich bitte an Deinen Browserhersteller bzw. an die Dokumentation Deines Browsers um die Cookiefunktion zu deaktivieren.

Durch das Klicken auf die "Ich akzeptiere"-Schaltflaeche stimmst Du unseren Nutzungsbedingen zu.
';
$lang['TermsOfUse'] = 'Nutzungsbedingungen';
$lang['RegistrationActivationEmailSubject'] = 'Zugangsaktivierung';
$lang['NeedToBeLoggedIn'] = 'F&uuml;r diese Aktion musst Du angemeldet sein. Klicke auf \'Anmelden\' um Dich einzuloggen oder auf \'Registrierung\' um einen neuen Zugang zu erstellen.';
$lang['WrongEmail'] = 'Entschuldigung, aber %s ist nicht die korrekte E-Mailadresse f&uuml;r Deinen Zugang %s. Wenn Du Dich an Deine E-Mailadresse nicht mehr erinnern kannst, kontaktiere bitte einen Administrator.';
$lang['Topic'] = 'Thema';
$lang['Author'] = 'Autor';
$lang['Replies'] = 'Antworten';
$lang['Views'] = 'Angesehen';
$lang['Note'] = 'Notiz';
$lang['Hidden'] = 'Versteckt';
$lang['ACP'] = 'Administrationsleiste';
$lang['SendMessage'] = 'Nachricht senden';
$lang['NoViewableForums'] = 'Mit Deinen aktuellen Zugangsrechten hast Du keine Berechtigungen irgendein Forum zu betreten. Wenn Du nicht angemeldet bist, tu es jetzt. Wenn Du angemeldet bist, solltest Du wahrscheinlich gar nicht hier sein.';
$lang['Rank'] = 'Rang';
$lang['Location'] = 'Wohnort';
$lang['Website'] = 'Website';
$lang['Occupation'] = 'Beruf';
$lang['Interests'] = 'Interessen';
$lang['MSNM'] = 'MSN Messenger';
$lang['YahooM'] = 'Yahoo Messenger';
$lang['AIM'] = 'AIM';
$lang['ICQ'] = 'ICQ';
$lang['Jabber'] = 'Jabber';
$lang['BannedIP'] = 'Deine IP address %s wurde f&uuml;r dieses Forum gesperrt.';
$lang['Avatar'] = 'Avatar';
$lang['AvatarURL'] = 'Avatar URL';
$lang['BannedUser'] = 'Gesperrter Zugang';
$lang['BannedUserExplain'] = 'Dein Zugang %s wurde f&uuml;r dieses Forum aus folgendem Grund gesperrt:';
$lang['BannedUsername'] = 'Der Benutzername %s wurde f&uuml;r dieses Forum gesperrt. Bitte w&auml;hle einen anderen Namen.';
$lang['BannedEmail'] = 'Die E-Mail Adresse %s wurde f&uuml;r dieses Forum gesperrt. Bitte gebe eine andere Adresse an.';
$lang['PostsPerDay'] = 'Beitr&auml;ge pro Tag';
$lang['BoardClosedOnlyAdmins'] = 'Nur Administratoren k&ouml;nnen sich an einem geschlossenen Forum anmelden.';
$lang['NoPosts'] = 'Keine Beitr&auml;ge';
$lang['NoActivetopics'] = 'Dieses Board hat momentan keine aktiven Themen.';
$lang['AuthorDate'] = 'Von %s am %s';
$lang['ByAuthor'] = 'Von: %s';
$lang['OnDate'] = 'Am: %s';
$lang['Re'] = 'Re:';
$lang['MailForm'] = 'Eine E-Mail an %s senden';
$lang['SendEmail'] = 'Eine Nachricht an %s senden';
$lang['NoMails'] = 'Der Benutzer m&ouml;chte keine E-Mails empfangen.';
$lang['UserEmailBody'] = 'Hallo,

hier ist die Forumsoftware von [board_name]. Der Benutzer [username] hat Dir ueber unser Forum eine Nachricht geschickt:

[board_name]
[board_link]
[admin_email]

-----

[body]';
$lang['EmailSent'] = 'Deine E-Mail an %s wurde erfolgreich weitergeleitet!';
$lang['To'] = 'An';
$lang['From'] = 'Von';
$lang['Subject'] = 'Betreff';
$lang['Body'] = 'Text';
$lang['Send'] = 'Senden';
$lang['EditProfile'] = 'Profil editieren';
$lang['EditOptions'] = 'Optionen ver&auml;ndern';
$lang['EditPasswd'] = 'Passwort &auml;ndern';
$lang['PanelHome'] = '&Uuml;berblick';
$lang['NewEmailNotActivated'] = 'Dein Profil wurde erfolgreich geaendert. Weil Du Deine E-Mailadresse fuer den Zugang %s veraendert hast, musst Du ihn reaktivieren. Eine E-Mail mit den benoetigten Anweisungen wurde an %s verschickt. In der Zwischenzeit wurdest Du sicherheitshalber vom System abgemeldet.';
$lang['Required'] = 'Erforderlich';
$lang['ViewProfile'] = 'Profil ansehen';
$lang['NewEmailActivationEmailBody'] = 'Hallo,

hier ist die Forumsoftware von [board_name]. Du hast soeben Deine E-Mailadresse fuer Deinen Zugang [account_name] geaendert, und musst nun die Aenderungen bestaetigen. Bitte klicke auf den folgenden Link oder benutze Kopieren/Einfuegen um den Link in Deinem Browser zu verfolgen: 

[activate_link]

Danach kannst Du Dich mit folgenden Zugangsdaten am Forum anmelden:

Benutzername: [account_name]
Passwort: [password]
Wenn Du Dein Passwort vergessen hast, kannst Du ein Neues ueber den Forumlink zum Loginformular anfordern.

[board_name]
[board_link]
[admin_email]';
$lang['NewEmailActivationEmailSubject'] = 'Reaktivierung des Zugangs';
$lang['Signature'] = 'Signatur';
$lang['SessionInfo'] = 'Sitzungsinformation';
$lang['SessionID'] = 'Sitzungsnummer ID';
$lang['IPAddress'] = 'IP Adresse';
$lang['Seconds'] = 'Sekunde(n)';
$lang['Updated'] = 'Aktualisiert';
$lang['Pages'] = 'Seiten';
$lang['AutoLogin'] = 'Automatische Anmeldung';
$lang['Enabled'] = 'Aktiviert';
$lang['Disabled'] = 'Deaktivert';
$lang['Enable'] = 'Aktivieren';
$lang['Disable'] = 'Deaktivieren';
$lang['AutoLoginSet'] = 'Das Cookie f&uuml;r die automatische Anmeldung wurde gesetzt';
$lang['AutoLoginUnset'] = 'Das Cookie f&uuml;r die automatische Anmeldung wurde entfernt.';
$lang['RegistrationEmailBody'] = 'Hallo,

hier ist die Forumsoftware von [board_name]. Du hast soeben einen Zugang namens [account_name] erstellt. Du kannst Dich nun mit folgenden Zugangsdaten am Forum anmelden:

Benutzername: [account_name]
Passwort: [password]


Wenn Du Dein Passwort vergessen hast, kannst Du ein Neues ueber den Link zum Loginformular anfordern. Danke f&uuml;r Deine Registrierung!

[board_name]
[board_link]
[admin_email]';

$lang['RegistrationEmailSubject'] = 'Zugangsregistrierung';
$lang['PublicEmail'] = 'E-Mailadresse im Forum anzeigen';
$lang['PublicLastLogin'] = 'Letztes Anmeldedatum im Forum anzeigen';
$lang['DateFormat'] = 'Format des Datums';
$lang['DateFormatHelp'] = 'Die Syntax des Datumformats entspricht der %s Funktion in PHP.';
$lang['Again'] = 'Nochmal';
$lang['NewPassword'] = 'Neues Passwort';
$lang['NewPasswordAgain'] = 'Neues Passwort (nochmal)';
$lang['PasswordEdited'] = 'Dein Passwort wurde erfolgreich ge&auml;ndert.';
$lang['DetailedOnlineList'] = 'Detaillierte  Onlineliste';
$lang['OptionsEdited'] = 'Deine Profiloptionen wurde erfolgreich ge&auml;ndert.';
$lang['ProfileEdited'] = 'Dein Profil wurde erfolgreich ge&auml;ndert.';
$lang['Started'] = 'Gestartet';
$lang['Minutes'] = 'Minute(n)';
$lang['Hours'] = 'Stunde(n)';
$lang['Days'] = 'Tag(e)';
$lang['Weeks'] = 'Woche(n)';
$lang['TotalTime'] = 'Totale Zeit';
$lang['NoTopics'] = 'Dieses Forum enth&auml;lt noch keine Beitr&auml;ge. Du k&ouml;nntest das erste Thema er&ouml;ffnen!';
$lang['NotPermitted'] = 'Du hast nicht die ben&ouml;tigten Berechtigungen um dies zu tun. Im Zweifel, kontaktiere bitte einen Administrator.';
$lang['Language'] = 'Sprache';
$lang['Template'] = 'Vorlage';
$lang['NoSuchMember'] = 'Das Mitglied %s existiert (nicht mehr) in diesem Forum.';
$lang['FeatureDisabledBecauseCookiesDisabled'] = 'Dieses Feature wurde deaktiviert, weil das Forum weder die Cookies Deines Browsers schreiben noch lesen kann.';
$lang['LogOutConfirm'] = 'Willst Du Dich wirklich abmelden? Das Cookie f&uuml;r den automatischen Anmeldeprozess w&uuml;rde gel&ouml;scht werden.';
$lang['Cancel'] = 'Abbrechen';
$lang['Timezone'] = 'Zeitzone';
$lang['DST'] = 'Sommerzeit';
$lang['Sticky'] = 'Sticky';
$lang['PostNewTopic'] = 'Neues Thema starten';
$lang['ForumIsLocked'] = 'Forum ist gesperrt';
$lang['NoSuchTopic'] = 'Das Thema %s existiert (nicht mehr) in diesem Forum.';
$lang['PostReply'] = 'Antwort schreiben';
$lang['TopicIsLocked'] = 'Thema ist gesperrt';
$lang['Post'] = 'Beitrag';
$lang['Edit'] = 'Editieren';
$lang['Delete'] = 'L&ouml;schen';
$lang['Quote'] = 'Zitieren';
$lang['Wrote'] = '%s schrieb';
$lang['ViewingIP'] = 'IP: %s';
$lang['ReplyTo'] = 'Antworten an "%s"';
$lang['TopicIsLockedExplain'] = 'Das Thema in dem Du versucht zu schreiben, wurde gesperrt. Nur noch authorisierte Personen k&ouml;nnen Antworten schreiben.';
$lang['Content'] = 'Inhalt';
$lang['Options'] = 'Optionen';
$lang['EnableBBCode'] = 'BBCode aktivieren.';
$lang['EnableSmilies'] = 'Smilies aktivieren.';
$lang['EnableSig'] = 'Signaturen aktivieren.';
$lang['EnableHTML'] = 'HTML aktivieren.';
$lang['LockTopicAfterPost'] = 'Thema nach Posting sperren.';
$lang['Guest'] = 'Gast';
$lang['BackToPrevious'] = 'Zur&uuml;ck zur vorherigen Seite';
$lang['NoSuchPost'] = 'Der Beitrag %s existiert (nicht mehr) in diesem Forum.';
$lang['UserPostedImage'] = 'Von Benutzern angeh&auml;ngte Bilder';
$lang['ForumIsLockedExplain'] = 'Dieses Forum ist gesperrt. Nur authorisierte Personen k&ouml;nnen neue Themen er&ouml;ffnen.';
$lang['MakeTopicSticky'] = 'Thema mit Spucke anpappen.';
$lang['QuickReply'] = 'Feature Schnelle Antwort aktivieren';
$lang['ReturnToTopicAfterPosting'] = 'Nach dem Schreiben zum Posting zur&uuml;ckspringen';
$lang['Moderators'] = 'Moderatoren: %s.';
$lang['Nobody'] = 'Niemand';
$lang['DeleteTopic'] = 'Thema l&ouml;schen';
$lang['MoveTopic'] = 'Thema verschieben';
$lang['LockTopic'] = 'Thema sperren';
$lang['UnlockTopic'] = 'Thema entsperren';
$lang['MakeSticky'] = 'Mit Spucke anpappen';
$lang['ConfirmDeleteTopic'] = 'Willst Du wirklich das Thema %s im Forum %s l&ouml;schen? Dieser Vorgang ist nicht r&uuml;ckg&auml;nglich zu machen!';
$lang['MakeNormalTopic'] = 'Normales Thema rausmachen';
$lang['OldForum'] = 'Altes Forum';
$lang['NewForum'] = 'Neues Forum';
$lang['IAccept'] = 'Ich akzeptiere';
$lang['IDontAccept'] = 'Ich lehne ab';
$lang['OpenLinksNewWindow'] = 'Open external links in new windows';
$lang['HideAllAvatars'] = 'Avatare ausblenden';
$lang['HideUserinfo'] = 'Benutzerinformationen ausblenden';
$lang['HideAllSignatures'] = 'Signaturen ausblenden';
$lang['HideFromOnlineList'] = 'Unsichtbarkeit auf der Onlineliste aktivieren';
$lang['PageLinks'] = 'Seite: %s';
$lang['Preview'] = 'Vorschau';
$lang['DeletePost'] = 'Beitrag l&ouml;schen';
$lang['ConfirmDeletePost'] = 'Willst Du wirklich diesen Beitrag im Thema %s l&ouml;schen? Dieser Vorgang ist nicht r&uuml;ckg&auml;nglich zu machen!';
$lang['EditPost'] = 'Beitrag editieren';
$lang['PostEditInfo'] = 'Das letzte Mal editiert von %s am %s.';
