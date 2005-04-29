<?php

/*
	Copyright (C) 2003-2005 UseBB Team
	http://www.usebb.net
	
	$Header$
	
	This file is part of UseBB.
	
	UseBB is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at je option) any later version.
	
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
// Translation settings
// Uncomment and change when necessary for translations
//
#$lang['character_encoding'] = 'iso-8859-1';
$lang['language_code'] = 'nl';
#$lang['text_direction'] = 'ltr';

//
// Define translations
//
$lang['Home'] = 'Beginpagina';
$lang['YourPanel'] = 'Je Paneel';
$lang['Register'] = 'Registreren';
$lang['FAQ'] = 'Hulp';
$lang['Search'] = 'Zoeken';
$lang['ActiveTopics'] = 'Actieve Onderwerpen';
$lang['LogIn'] = 'Inloggen';
$lang['LogOut'] = 'Uitloggen (%s)';
$lang['MemberList'] = 'Ledenlijst';
$lang['StaffList'] = 'Staflijst';
$lang['Statistics'] = 'Statistieken';
$lang['ContactAdmin'] = 'Admin Contacteren';
$lang['Forum'] = 'Forum';
$lang['Topics'] = 'Onderwerpen';
$lang['Posts'] = 'Berichten';
$lang['LatestPost'] = 'Laatste bericht';
$lang['RSSFeed'] = 'RSS Feed';
$lang['NewPosts'] = 'Nieuwe berichten';
$lang['NoNewPosts'] = 'Geen nieuwe berichten';
$lang['LockedNewPosts'] = 'Gesloten (nieuwe berichten)';
$lang['LockedNoNewPosts'] = 'Gesloten (geen nieuwe berichten)';
$lang['Locked'] = 'Gesloten';
$lang['LastLogin'] = 'Laatste maal ingelogd';
$lang['VariousInfo'] = 'Diverse Informatie';
$lang['IndexStats'] = 'Dit forum bevat %d berichten in %d onderwerpen geplaatst door %d leden.';
$lang['NewestMember'] = 'Welkom aan ons nieuwste lid: %s.';
$lang['UsersOnline'] = 'Er waren %d leden (%d verborgen) en %d gasten online in de voorbije %d minuten.';
$lang['Username'] = 'Gebruikersnaam';
$lang['CurrentPassword'] = 'Huidig wachtwoord';
$lang['UserID'] = 'Gebruikers-ID';
$lang['NoSuchForum'] = 'Het forum %s bestaat niet (meer) op dit discussieforum.';
$lang['WrongPassword'] = 'Sorry, maar dat wachtwoord is niet correct! Vraag een nieuw wachtwoord aan via het inlogformulier indien je het vergeten bent.';
$lang['Reset'] = 'Herstellen';
$lang['SendPassword'] = 'Een nieuw wachtwoord zenden';
$lang['RegisterNewAccount'] = 'Een account registreren';
$lang['RememberMe'] = 'Onthouden';
$lang['Yes'] = 'Ja';
$lang['No'] = 'Nee';
$lang['NotActivated'] = 'Je account %s is nog niet geactiveerd. Gelieve je e-mail account waarmee je je registreerde te controleren op instructies over het activeren van je account.';
$lang['Error'] = 'Fout';
$lang['Profile'] = 'Profiel van %s';
$lang['Level'] = 'Niveau';
$lang['Administrator'] = 'Administrator';
$lang['Moderator'] = 'Moderator';
$lang['Registered'] = 'Geregistreerd';
$lang['Email'] = 'E-mail adres';
$lang['ContactInfo'] = 'Contactinformatie';
$lang['Password'] = 'Wachtwoord';
$lang['PasswordAgain'] = 'Wachtwoord (nogmaals)';
$lang['EverythingRequired'] = 'Alle velden zijn vereist!';
$lang['UserAlreadyExists'] = 'Sorry, de gebruiker %s bestaat reeds op dit forum. Indien jij dit bent, log in of vraag een nieuw wachtwoord aan. Indien dit niet het geval is, gelieve een andere gebruikersnaam te kiezen.';
$lang['RegisteredNotActivated'] = 'Je account %s werd aangemaakt. Een e-mail werd verstuurd naar %s met instructies voor het activeren van je account. Je account moet geactiveerd worden voordat je kan inloggen.';
$lang['RegisteredActivated'] = 'Je account %s werd aangemaakt. Een e-mail werd verstuurd naar %s met je accountinformatie. Je kan meteen inloggen.';
$lang['Never'] = 'Nooit';
$lang['Member'] = 'Lid';
$lang['RegistrationActivationEmailBody'] = 'Hallo,

Dit is de forumsoftware van [board_name]. Je hebt zonet een account genaamd [account_name] geregistreerd, maar deze werd nog niet geactiveerd. Gelieve op onderstaande link te klikken om je account te activeren:

[activate_link]

of kopieer en plak het in de adresbalk van je browser. Daarna kan je inloggen met de volgende gebruikersnaam en wachtwoord:

Gebruikersnaam: [account_name]
Wachtwoord: [password]

Indien je je wachtwoord vergeet kan je een nieuwe aanvragen via de link op het inlogformulier. Bedankt voor het registreren!

[board_name]
[board_link]
[admin_email]';
$lang['SendpwdActivationEmailSubject'] = 'Nieuw wachtwoord';
$lang['NoForums'] = 'Dit is een leeg discussieforum. De administrator heeft nog geen forums aangemaakt.';
$lang['AlreadyActivated'] = 'De account met ID %d werd reeds geactiveerd.';
$lang['Activate'] = 'Activeren';
$lang['Activated'] = 'Je account %s werd nu ge(re)activeerd. Je kan nu inloggen met de gebruikersnaam en wachtwoord in de e-mail.';
$lang['WrongActivationKey'] = 'Je account met ID %d kon niet geactiveerd worden. De activatiesleutel is foutief. Ben je zeker dat je ondertussen geen nieuw wachtwoord aangevraagd hebt?';
$lang['RegisterIt'] = 'Je kan het aanmaken via de \'Registreer\'-link.';
$lang['BoardClosed'] = 'Discussieforum Gesloten';
$lang['SendpwdActivationEmailBody'] = 'Hallo,

Dit is de forumsoftware van [board_name]. Je hebt zonet een nieuw wachtwoord aangevraagd voor je account [account_name]. Gelieve op onderstaande link te klikken om je account te heractiveren:

[activate_link]

of kopieer en plak het in de adresbalk van je browser. Daarna kan je inloggen met de volgende gebruikersnaam en wachtwoord:

Gebruikersnaam: [account_name]
Wachtwoord: [password]

Indien je je wachtwoord vergeet kan je een nieuwe aanvragen via de link op het inlogformulier.

[board_name]
[board_link]
[admin_email]';
$lang['SendpwdEmailBody'] = 'Hallo,

Dit is de forumsoftware van [board_name]. Je hebt zonet een nieuw wachtwoord aangevraagd voor je account [account_name]. Je kan inloggen met de volgende gebruikersnaam en wachtwoord:

Gebruikersnaam: [account_name]
Wachtwoord: [password]

Indien je je wachtwoord vergeet kan je een nieuwe aanvragen via de link op het inlogformulier.

[board_name]
[board_link]
[admin_email]';
$lang['SendpwdEmailSubject'] = 'Nieuw Wachtwoord';
$lang['SendpwdActivated'] = 'Het nieuwe wachtwoord voor je account %s werd verstuurd naar %s. Je kan nu met je nieuwe wachtwoord inloggen.';
$lang['SendpwdNotActivated'] = 'Het nieuwe wachtwoord voor je account %s werd verstuurd naar %s, tesamen met informatie over het heractiveren van je account.';
$lang['ForumIndex'] = 'Forum-index';
$lang['MissingFields'] = 'De volgende velden ontbreken of zijn foutief ingevuld: %s.';
$lang['TermsOfUseContent'] = 'Je aanvaard dat alle berichten op dit discussieforum de meningen uitdrukken van de auteurs van de berichten en niet van de eigenaar van de website of administrators of moderators van dit forum, behalve voor de berichten geschreven door een van hen.

Je gaat ermee akkoord geen misbruikende, offensieve, obscene of onaanvaardbare inhoud of inhoud dat bij wet verboden is te plaatsen op dit discussieforum. Indien je dit wel doet kan dit resulteren in het bannen van je account of zelfs het verwijderen ervan, en je internetprovider eventueel op de hoogte gebracht van je gedrag. Daarvoor wordt je IP-adres opgeslagen bij elk bericht dat je plaatst. Je gaat er ook mee akkoord dat administrators en moderators het recht hebben je onderwerpen te bewerken, verwijderen, verplaatsen of sluiten wanneer ze denken dat dit nodig is.

Alle informatie die je op dit discussieforum plaatst wordt opgeslagen in een databasesysteem voor later gebruik. De forumadministrators zullen deze informatie niet openbaar maken zonder je toestemming of juridische verplichting. Hoewel, noch de webmaster, administrators of moderators noch het UseBB Team kan aansprakelijk gesteld worden voor het lekken van enige informatie als gevolg van een hack.

Dit discussieforum gebruikt cookies voor de opslag van tijdelijke informatie benodigd voor het forumsysteem op je eigen computer. Een cookie kan, indien gewenst, ook je gebruikers-ID en wachtwoord in een geencrypteerde vorm opslaan om het automatisch inloggen mogelijk te maken. Indien je geen cookies wilt laten opslaa, raadpleeg de handleiding van je browser.

Door het klikken op de "Aanvaarden"-knop geef je aan gebonden te zijn aan deze gebruikersovereenkomst.';
$lang['TermsOfUse'] = 'Gebruikersovereenkomst';
$lang['RegistrationActivationEmailSubject'] = 'Account-activatie';
$lang['NeedToBeLoggedIn'] = 'Je moet ingelogd zijn om dit te doen. Klik op de \'Inloggen\'-link of maak een nieuwe account aan via de \'Registreren\'-link.';
$lang['WrongEmail'] = 'Sorry, maar %s is niet het correctie e-mailadres voor de account %s. Contacteer een administrator indien je je e-mailadres niet kan herinneren.';
$lang['Topic'] = 'Onderwerp';
$lang['Author'] = 'Auteur';
$lang['Replies'] = 'Antwoorden';
$lang['Views'] = 'Bekeken';
$lang['Note'] = 'Opmerking';
$lang['Hidden'] = 'Verborgen';
$lang['ACP'] = 'Administratiepaneel';
$lang['SendMessage'] = 'Bericht verzenden';
$lang['NoViewableForums'] = 'Je hebt niet de vereiste rechten om een forum te bekijken. Indien je niet ingelogd bent, doe dit. Indien je ingelogd bent, zou je hier waarschijnlijk niet mogen zijn.';
$lang['Rank'] = 'Rank';
$lang['Location'] = 'Locatie';
$lang['Website'] = 'Website';
$lang['Occupation'] = 'Beroep';
$lang['Interests'] = 'Interesses';
$lang['MSNM'] = 'MSN Messenger';
$lang['YahooM'] = 'Yahoo Messenger';
$lang['AIM'] = 'AIM';
$lang['ICQ'] = 'ICQ';
$lang['Jabber'] = 'Jabber';
$lang['BannedIP'] = 'Je IP-adres %s is geband van dit discussieforum.';
$lang['Avatar'] = 'Avatar';
$lang['AvatarURL'] = 'Avatar-URL';
$lang['BannedUser'] = 'Gebande Account';
$lang['BannedUserExplain'] = 'Je account %s werd van dit discussieforum geband. De reden is:';
$lang['BannedUsername'] = 'De account %s werd van dit discussieforum geband. Gelieve een andere te kiezen.';
$lang['BannedEmail'] = 'Het e-mailadres %s werd van dit discussieforum geband. Gelieve een andere te kiezen.';
$lang['PostsPerDay'] = 'Berichten per dag';
$lang['BoardClosedOnlyAdmins'] = 'Alleen administrators kunnen inloggen op een gesloten discussieforum.';
$lang['NoPosts'] = 'Geen Berichten';
$lang['NoActivetopics'] = 'Dit discussieforum heeft momenteel geen actieve onderwerpen.';
$lang['AuthorDate'] = 'Door %s op %s';
$lang['ByAuthor'] = 'Door: %s';
$lang['OnDate'] = 'Op: %s';
$lang['Re'] = 'Re:';
$lang['MailForm'] = 'Een e-mail naar %s zenden';
$lang['SendEmail'] = 'Een bericht naar %s zenden';
$lang['NoMails'] = 'Deze gebruiker heeft er voor gekozen geen e-mails te ontvangen.';
$lang['UserEmailBody'] = 'Hallo,

Dit is de forumsoftware van [board_name]. De gebruiker [username] heeft via ons discussieforum een bericht naar je verstuurd. Het bericht volgt.

[board_name]
[board_link]
[admin_email]

-----

[body]';
$lang['EmailSent'] = 'Je e-mail naar %s werd succesvol verzonden!';
$lang['To'] = 'Aan';
$lang['From'] = 'Van';
$lang['Subject'] = 'Onderwerp';
$lang['Body'] = 'Inhoud';
$lang['Send'] = 'Verzenden';
$lang['EditProfile'] = 'Profiel Bewerken';
$lang['EditOptions'] = 'Instellingen';
$lang['EditPasswd'] = 'Wachtwoord aanpassen';
$lang['PanelHome'] = 'Paneel-beginpagina';
$lang['NewEmailNotActivated'] = 'Je profiel werd succesvol aangepast. Omdat je je e-mailadres voor je account %s hebt aangepast, dien je je opnieuw te activeren. Een e-mail werd verzonden naar %s met verdere instructies. Ondertussen blijf je uitgelogd.';
$lang['Required'] = 'Vereist';
$lang['ViewProfile'] = 'Profiel Bekijken';
$lang['NewEmailActivationEmailBody'] = 'Hallo,

Dit is de forumsoftware van [board_name]. Je hebt net het e-mailadres van je account [account_name] aangepast, maar deze werd nog niet terug geactiveerd. Gelieve op onderstaande link te klikken om je account terug te activeren:

[activate_link]

of kopieer en plak het in de adresbalk van je browser. Daarna kan je inloggen met de volgende gebruikersnaam en wachtwoord:

Gebruikersnaam: [account_name]
Wachtwoord: [password]

Indien je je wachtwoord vergeet kan je een nieuwe aanvragen via de link op het inlogformulier.

[board_name]
[board_link]
[admin_email]';
$lang['NewEmailActivationEmailSubject'] = 'Account Heractiveren';
$lang['Signature'] = 'Handtekening';
$lang['SessionInfo'] = 'Sessie-informatie';
$lang['SessionID'] = 'Sessie-ID';
$lang['IPAddress'] = 'IP-adres';
$lang['Seconds'] = 'Seconden';
$lang['Updated'] = 'Bijgewerkt';
$lang['Pages'] = 'Pagina\'s';
$lang['AutoLogin'] = 'Automatisch inloggen';
$lang['Enabled'] = 'Ingeschakeld';
$lang['Disabled'] = 'Uitgeschakeld';
$lang['Enable'] = 'Inschakelen';
$lang['Disable'] = 'Uitschakelen';
$lang['AutoLoginSet'] = 'De automatisch inloggen-cookie werd nu geplaatst.';
$lang['AutoLoginUnset'] = 'De automatisch inloggen-cookie werd nu verwijderd.';
$lang['RegistrationEmailBody'] = 'Hallo,

Dit is de forumsoftware van [board_name]. Je hebt zonet een account genaamd [account_name] geregistreerd. Je kan inloggen met de volgende gebruikersnaam en wachtwoord:

Gebruikersnaam: [account_name]
Wachtwoord: [password]

Indien je je wachtwoord vergeet kan je een nieuwe aanvragen via de link op het inlogformulier. Bedankt voor het registreren!

[board_name]
[board_link]
[admin_email]';
$lang['RegistrationEmailSubject'] = 'Account-registratie';
$lang['PublicEmail'] = 'Publiek e-mailadres';
$lang['PublicLastLogin'] = 'Publieke laatste inlogtijd';
$lang['DateFormat'] = 'Datumformaat';
$lang['DateFormatHelp'] = 'De syntax van het datumformaat is gelijk aan dat van de %s-functie in PHP.';
$lang['Again'] = 'Nogmaals';
$lang['NewPassword'] = 'Nieuw wachtwoord';
$lang['NewPasswordAgain'] = 'Nieuw wachtwoord (nogmaals)';
$lang['PasswordEdited'] = 'Je wachtwoord werd succesvol aangepast';
$lang['DetailedOnlineList'] = 'Gedetailleerde online gebruikerslijst';
$lang['Detailed'] = 'Gedetailleerd';
$lang['OptionsEdited'] = 'Je instellingen werden succesvol aangepast.';
$lang['ProfileEdited'] = 'Je profiel werd succesvol aangepast.';
$lang['Started'] = 'Gestart';
$lang['Minutes'] = 'Minuten';
$lang['Hours'] = 'Uren';
$lang['Days'] = 'Dagen';
$lang['Weeks'] = 'Weken';
$lang['TotalTime'] = 'Totale tijd';
$lang['NoTopics'] = 'Dit forum bevat geen onderwerpen. Je zou de eerste kunnen plaatsen!';
$lang['NotPermitted'] = 'Je hebt niet de vereiste rechten om dit te doen. Bij twijfel, contacteer de administrator.';
$lang['Language'] = 'Taal';
$lang['Template'] = 'Template';
$lang['NoSuchMember'] = 'Het lid %s bestaat niet (meer) op dit discussieforum.';
$lang['FeatureDisabledBecauseCookiesDisabled'] = 'Deze functie is niet beschikbaar omdat het forum geen cookies kan plaatsen of lezen met je browser.';
$lang['LogOutConfirm'] = 'Ben je zeker dat je wilt uitloggen? De automatisch inloggen-cookie zal verwijderd worden!';
$lang['Cancel'] = 'Annuleren';
$lang['Timezone'] = 'Tijdzone';
$lang['DST'] = 'Zomertijd';
$lang['Sticky'] = 'Sticky';
$lang['PostNewTopic'] = 'Nieuw Onderwerp';
$lang['ForumIsLocked'] = 'Forum is gesloten';
$lang['NoSuchTopic'] = 'Het onderwerp %s bestaat niet meer op dit discussieforum.';
$lang['PostReply'] = 'Antwoord Plaatsen';
$lang['TopicIsLocked'] = 'Onderwerp is gesloten';
$lang['Post'] = 'Bericht';
$lang['Edit'] = 'Bewerken';
$lang['Delete'] = 'Verwijderen';
$lang['Quote'] = 'Citaat';
$lang['Wrote'] = '%s schreef';
$lang['ViewingIP'] = 'IP: %s';
$lang['TopicIsLockedExplain'] = 'Het onderwerp waarop je probeert te antwoorden is gesloten. Alleen geautoriseerde personen kunnen nog berichten plaatsen.';
$lang['Content'] = 'Inhoud';
$lang['Options'] = 'Opties';
$lang['EnableBBCode'] = 'BBCode inschakelen.';
$lang['EnableSmilies'] = 'Smilies inschakelen.';
$lang['EnableSig'] = 'Handtekening inschakelen.';
$lang['EnableHTML'] = 'HTML inschakelen.';
$lang['LockTopicAfterPost'] = 'Onderwerp sluiten na het verzenden.';
$lang['Guest'] = 'Gast';
$lang['BackToPrevious'] = 'Terug naar de vorige pagina';
$lang['NoSuchPost'] = 'Het bericht %s bestaat niet (meer) op dit discussieforum.';
$lang['UserPostedImage'] = 'Afbeelding geplaatst door gebruiker';
$lang['ForumIsLockedExplain'] = 'Dit forum is gesloten. Alleen geautoriseerde personen kunnen nog berichten plaatsen.';
$lang['MakeTopicSticky'] = 'Maak het onderwerp sticky.';
$lang['QuickReply'] = 'Snel antwoord';
$lang['ReturnToTopicAfterPosting'] = 'Terugkeren naar het onderwerp na het verzenden';
$lang['ModeratorList'] = 'Moderators: %s.';
$lang['Nobody'] = 'Niemand';
$lang['DeleteTopic'] = 'Verwijderen';
$lang['MoveTopic'] = 'Verplaatsen';
$lang['LockTopic'] = 'Sluiten';
$lang['UnlockTopic'] = 'Heropenen';
$lang['MakeSticky'] = 'Sticky maken';
$lang['ConfirmDeleteTopic'] = 'Ben je zeker dat je het onderwerp %s in het forum %s wilt verwijderen? Dit is onomkeerbaar!';
$lang['MakeNormalTopic'] = 'Normaliseren';
$lang['OldForum'] = 'Oud forum';
$lang['NewForum'] = 'Nieuw forum';
$lang['IAccept'] = 'Aanvaarden';
$lang['IDontAccept'] = 'Afwijzen';
$lang['OpenLinksNewWindow'] = 'Externe links in een nieuw venster openen';
$lang['HideAllAvatars'] = 'Alle avatars verbergen';
$lang['HideUserinfo'] = 'Gebruikersinformatie verbergen';
$lang['HideAllSignatures'] = 'Alle handtekeningen verbergen';
$lang['HideFromOnlineList'] = 'Verberg mij van de online gebruikerslijst';
$lang['PageLinks'] = 'Pagina: %s';
$lang['Preview'] = 'Voorbeeld';
$lang['DeletePost'] = 'Bericht verwijderen';
$lang['ConfirmDeletePost'] = 'Ben je zeker dat je dit bericht in het onderwerp %s wilt verwijderen? Dit is onomkeerbaar!';
$lang['EditPost'] = 'Bericht bewerken';
$lang['PostEditInfo'] = 'Laatste aanpassing door %s op %s.';
$lang['PasswdInfo'] = 'Het wachtwoord kan enkel alfanumerieke karakters bevatten en heeft een minimum lengte van %d karakters.';
$lang['SubscribeTopic'] = 'Inschrijven';
$lang['UnsubscribeTopic'] = 'Uitschrijven';
$lang['NewReplyEmailBody'] = 'Hallo,

Dit is de forumsoftware van [board_name]. Iemand ([poster_name]) heeft een antwoord geplaatst op een onderwerp waar je op ingeschreven bent ("[topic_title]"). Om het antwoord te zien, klik op de volgende link: [topic_link].

Klik op de volgende link om jezelf van het onderwerp uit te schrijven (vereist login): [unsubscribe_link].

[board_name]
[board_link]
[admin_email]';
$lang['NewReplyEmailSubject'] = 'Nieuw antwoord in "%s"';
$lang['SubscribedTopic'] = 'Je bent nu op dit onderwerp ingeschreven.';
$lang['UnsubscribedTopic'] = 'Je bent nu van dit onderwerp uitgeschreven.';
$lang['SubscribeToThisTopic'] = 'Inschrijven op dit onderwerp.';
$lang['OK'] = 'OK';
$lang['Subscriptions'] = 'Subscripties';
$lang['NoSubscribedTopics'] = 'Je bent op dit moment op geen enkel onderwerp ingeschreven.';
$lang['LatestUpdate'] = 'Laatste bijwerking';
$lang['Unknown'] = 'Onbekend';
$lang['PostingTopic'] = 'Een onderwerp plaatsen in %s';
$lang['PostingReply'] = 'Een bericht plaatsen in %s';
$lang['MovingTopic'] = 'Onderwerp %s verplaatsen';
$lang['DeletingTopic'] = 'Onderwerp %s verwijderen';
$lang['TrashingTopic'] = 'Onderwerp %s trashen';
$lang['EditingPost'] = 'Bericht in %s bewerken';
$lang['DeletingPost'] = 'Bericht in %s verwijderen';
$lang['DebugMode'] = 'Debug-modus';
$lang['ParseTime'] = 'Verwerkingstijd';
$lang['ServerLoad'] = 'Serverbelasting';
$lang['TemplateSections'] = 'Templatesecties';
$lang['SQLQueries'] = 'SQL-queries';
$lang['RealName'] = 'Echte naam';
$lang['Skype'] = 'Skype';
$lang['Administrators'] = 'Administrators';
$lang['Moderators'] = 'Moderators';
$lang['SortBy'] = 'Sorteren op: %s';
$lang['TopicReview'] = 'Onderwerp herbekijken';
$lang['ViewMorePosts'] = 'Meer berichten weergeven';
$lang['DisplayedName'] = 'Weergegeven naam';
$lang['UsernameInfo'] = 'Een gebruikersnaam kan enkel alfanumerieke karakters, _ en - bevatten (geen spaties).';
$lang['Code'] = 'Code';
$lang['Img'] = 'Afb';
$lang['URL'] = 'URL';
$lang['Color'] = 'Kleur';
$lang['Size'] = 'Grootte';
$lang['ViewingForum'] = 'Forum bekijken: %s.';
$lang['ViewingTopic'] = 'Onderwerp bekijken: %s.';
$lang['FloodIntervalWarning'] = 'De administrator heeft ingesteld dat je slechts berichten kan plaatsen met intervals van %d seconden. Gelieve te wachten en het formulier dadelijk opnieuw te verzenden.';
$lang['AutoSubscribe'] = 'Automatisch inschrijven';
$lang['OnPostingNewTopics'] = 'Bij het plaatsen van onderwerpen';
$lang['OnPostingNewReplies'] = 'Bij het plaatsen van antwoorden';
$lang['UnsubscribeSelected'] = 'Geselecteerde uitschrijven';
$lang['SelectedTopicsUnsubscribed'] = 'Je bent nu van de geslecteerde onderwerpen uitgeschreven.';
$lang['TrashTopic'] = 'Trashen';
$lang['ConfirmTrashTopic'] = 'Ben je zeker dat je het onderwerp %s in het forum %s wilt verplaatsen naar het trash-forum?';
$lang['Birthday'] = 'Verjaardag';
$lang['Age'] = 'Leeftijd';

//
// Date translations
//
$lang['date_translations'] = array(
	'Mon' => 'maa',
	'Tue' => 'din',
	'Wed' => 'woe',
	'Thu' => 'don',
	'Fri' => 'vri',
	'Sat' => 'zat',
	'Sun' => 'zon',
	'Monday' => 'maandag',
	'Tuesday' => 'dinsdag',
	'Wednesday' => 'woensdag',
	'Thursday' => 'donderdag',
	'Friday' => 'vrijdag',
	'Saturday' => 'zaterdag',
	'Sunday' => 'zondag',
	'Jan' => 'jan',
	'Feb' => 'feb',
	'Mar' => 'maa',
	'Apr' => 'apr',
	'May' => 'mei',
	'Jun' => 'jun',
	'Jul' => 'jul',
	'Aug' => 'aug',
	'Sep' => 'sep',
	'Oct' => 'okt',
	'Nov' => 'nov',
	'Dec' => 'dec',
	'January' => 'januari',
	'February' => 'februari',
	'March' => 'maart',
	'April' => 'april',
	'May' => 'mei',
	'June' => 'juni',
	'July' => 'juli',
	'August' => 'augustus',
	'September' => 'september',
	'October' => 'oktober',
	'November' => 'november',
	'December' => 'december',
	'st' => 'ste',
	'nd' => 'de',
	'th' => 'de'
);
