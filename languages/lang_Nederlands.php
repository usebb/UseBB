<?php

/*
	Copyright (C) 2003-2011 UseBB Team
	http://www.usebb.net
	
	$Id$
	
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
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
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
$lang['RSSFeed'] = 'RSS-feed';
$lang['NewPosts'] = 'Nieuwe berichten';
$lang['NoNewPosts'] = 'Geen nieuwe berichten';
$lang['LockedNewPosts'] = 'Gesloten (nieuwe berichten)';
$lang['LockedNoNewPosts'] = 'Gesloten (geen nieuwe berichten)';
$lang['Locked'] = 'Gesloten';
$lang['LastLogin'] = 'Laatste maal ingelogd';
$lang['VariousInfo'] = 'Diverse Informatie';
$lang['IndexStats'] = 'Dit forum bevat %d berichten in %d onderwerpen en heeft %d geregistreerde leden.';
$lang['NewestMemberExtended'] = 'Welkom aan ons nieuwste lid: %s.';
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
$lang['NotActivatedByAdmin'] = 'De administrator van dit forum heeft je account %s nog niet geactiveerd. Gelieve hiervoor geduld uit te oefenen.';
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
$lang['RegisteredNotActivated'] = 'Je account %s werd aangemaakt. Een e-mail werd verstuurd naar %s met instructies voor het activeren van je account. Je account moet geactiveerd worden voordat je kan inloggen.';
$lang['RegisteredActivated'] = 'Je account %s werd aangemaakt. Je kan meteen inloggen.';
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
$lang['NoForums'] = 'Dit is een leeg discussieforum. De administrator heeft nog geen forums aangemaakt.';
$lang['AlreadyActivated'] = 'De account met ID %d werd reeds geactiveerd.';
$lang['Activate'] = 'Activeren';
$lang['Activated'] = 'Je account %s werd nu ge(re)activeerd. Je wordt automatisch ingelogd.';
$lang['WrongActivationKey'] = 'Je account met ID %d kon niet geactiveerd worden. De activatiesleutel is foutief. Ben je zeker dat je ondertussen geen nieuw wachtwoord aangevraagd hebt?';
$lang['RegisterIt'] = 'Je kan het aanmaken via de \'Registreer\'-link.';
$lang['BoardClosed'] = 'Discussieforum Gesloten';
$lang['SendpwdEmailBody'] = 'Hallo,

Dit is de forumsoftware van [board_name]. Je hebt zonet een nieuw wachtwoord aangevraagd voor je account [account_name]. Je kan inloggen met de volgende gebruikersnaam en wachtwoord:

Gebruikersnaam: [account_name]
Wachtwoord: [password]

Indien je je wachtwoord vergeet kan je een nieuwe aanvragen via de link op het inlogformulier.

[board_name]
[board_link]
[admin_email]';
$lang['SendpwdEmailSubject'] = 'Nieuw Wachtwoord';
$lang['SendpwdActivated'] = 'Het nieuwe wachtwoord voor je account %s werd verstuurd naar %s.';
$lang['ForumIndex'] = 'Forum-index';
$lang['MissingFields'] = 'De volgende verplichte velden ontbreken of zijn foutief ingevuld: %s.';
$lang['TermsOfUseContent'] = 'Je aanvaard dat alle berichten op dit discussieforum de meningen uitdrukken van de auteurs van de berichten en niet van de eigenaar van de website of administrators of moderators van dit forum, behalve voor de berichten geschreven door een van hen.

Je gaat ermee akkoord geen misbruikende, offensieve, obscene of onaanvaardbare inhoud of inhoud dat bij wet verboden is te plaatsen op dit discussieforum. Indien je dit wel doet kan dit resulteren in het bannen van je account of zelfs het verwijderen ervan, en je internetprovider eventueel op de hoogte gebracht van je gedrag. Daarvoor wordt je IP-adres opgeslagen bij elk bericht dat je plaatst. Je gaat er ook mee akkoord dat administrators en moderators het recht hebben je onderwerpen te bewerken, verwijderen, verplaatsen of sluiten wanneer ze denken dat dit nodig is.

Alle informatie die je op dit discussieforum plaatst wordt opgeslagen in een databasesysteem voor later gebruik. De forumadministrators zullen deze informatie niet openbaar maken zonder je toestemming of juridische verplichting. Hoewel, noch de webmaster, administrators of moderators noch het UseBB Project kan aansprakelijk gesteld worden voor het lekken van enige informatie als gevolg van een hack.

Dit discussieforum gebruikt cookies voor de opslag van tijdelijke informatie benodigd voor het forumsysteem op je eigen computer. Een cookie kan, indien gewenst, ook je gebruikers-ID en wachtwoord in een geencrypteerde vorm opslaan om het automatisch inloggen mogelijk te maken. Indien je geen cookies wilt laten opslaan, raadpleeg de handleiding van je browser.

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
$lang['MSNM'] = 'Windows Live Messenger';
$lang['YahooM'] = 'Yahoo! Messenger';
$lang['AIM'] = 'AIM';
$lang['ICQ'] = 'ICQ';
$lang['Jabber'] = 'Jabber/XMPP';
$lang['BannedIP'] = 'Je IP-adres %s is geband van dit discussieforum.';
$lang['Avatar'] = 'Avatar';
$lang['AvatarURL'] = 'Avatar-URL';
$lang['BannedUser'] = 'Gebande Account';
$lang['BannedUserExplain'] = 'Je account %s wordt van dit discussieforum geband. De reden is:';
$lang['BannedUsername'] = 'De gebruikersnaam %s wordt van dit discussieforum geband. Gelieve een andere te kiezen.';
$lang['BannedEmail'] = 'Het e-mailadres %s wordt van dit discussieforum geband. Gelieve een andere te kiezen.';
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

of kopieer en plak het in de adresbalk van je browser.

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
$lang['NoTopics'] = 'Dit forum bevat geen onderwerpen. Je zou het eerste kunnen plaatsen!';
$lang['NotPermitted'] = 'Je hebt niet de vereiste rechten om dit te doen. Bij twijfel, contacteer de administrator.';
$lang['Language'] = 'Taal';
$lang['Template'] = 'Template';
$lang['NoSuchMember'] = 'Het lid %s bestaat niet (meer) op dit discussieforum.';
$lang['FeatureDisabledBecauseCookiesDisabled'] = 'Deze functie is niet beschikbaar omdat het forum geen cookies kan plaatsen of lezen met je browser.';
$lang['LogOutConfirm'] = 'Ben je zeker dat je wilt uitloggen?';
$lang['Cancel'] = 'Annuleren';
$lang['Timezone'] = 'Tijdzone';
$lang['DST'] = 'Zomertijd';
$lang['Sticky'] = 'Sticky';
$lang['PostNewTopic'] = 'Nieuw Onderwerp';
$lang['ForumIsLocked'] = 'Forum is gesloten';
$lang['NoSuchTopic'] = 'Het onderwerp %s bestaat niet meer op dit discussieforum.';
$lang['PostReply'] = 'Beantwoorden';
$lang['TopicIsLocked'] = 'Onderwerp is gesloten';
$lang['Post'] = 'Bericht';
$lang['Edit'] = 'Bewerken';
$lang['Delete'] = 'Verwijderen';
$lang['Quote'] = 'Citeren';
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
$lang['PasswdInfoNew'] = 'Het wachtwoord kan enkel alfanumerieke karakters en symbolen bevatten. Omwille van de veiligheid moet het ten minste &eacute;&eacute;n letter en &eacute;&eacute;n cijfer bevatten, en een minimum lengte van %d karakters hebben.';
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
$lang['EditingPost'] = 'Bericht in %s bewerken';
$lang['DeletingPost'] = 'Bericht in %s verwijderen';
$lang['DebugMode'] = 'Debug-modus';
$lang['ParseTime'] = 'Verwerkingstijd';
$lang['ServerLoad'] = 'Serverbelasting';
$lang['TemplateSections'] = 'Templatesecties';
$lang['SQLQueries'] = 'SQL-queries';
$lang['MemoryUsage'] = 'Geheugengebruik';
$lang['MegaByteShort'] = 'MB';
$lang['RealName'] = 'Echte naam';
$lang['Skype'] = 'Skype';
$lang['Administrators'] = 'Administrators';
$lang['Moderators'] = 'Moderators';
$lang['TopicReview'] = 'Onderwerp herbekijken';
$lang['ViewMorePosts'] = 'Meer berichten weergeven';
$lang['DisplayedName'] = 'Weergegeven naam';
$lang['UsernameInfo'] = 'Een gebruikersnaam kan enkel alfanumerieke karakters, spaties, _ en - bevatten.';
$lang['Code'] = 'Code';
$lang['Img'] = 'Afb';
$lang['URL'] = 'URL';
$lang['Color'] = 'Kleur';
$lang['Size'] = 'Grootte';
$lang['ViewingForum'] = 'Forum: %s';
$lang['ViewingTopic'] = 'Onderwerp: %s';
$lang['FloodIntervalWarning'] = 'De administrator heeft ingesteld dat je slechts berichten kan plaatsen met intervals van %d seconden. Gelieve ten minste %d seconden te wachten alvorens het formulier opnieuw te verzenden.';
$lang['AutoSubscribe'] = 'Automatisch inschrijven';
$lang['OnPostingNewTopics'] = 'Bij het plaatsen van onderwerpen';
$lang['OnPostingNewReplies'] = 'Bij het plaatsen van antwoorden';
$lang['UnsubscribeSelected'] = 'Geselecteerde uitschrijven';
$lang['SelectedTopicsUnsubscribed'] = 'Je bent nu van de geslecteerde onderwerpen uitgeschreven.';
$lang['Birthday'] = 'Geboortedatum';
$lang['Age'] = 'Leeftijd';
$lang['Month'] = 'Maand';
$lang['Day'] = 'Dag';
$lang['Year'] = 'Jaar';
$lang['PoweredBy'] = '%s wordt aangedreven door %s';
$lang['ForumSoftware'] = 'Forumsoftware';
$lang['GeneralStats'] = 'Algemene statistieken';
$lang['Members'] = 'Leden';
$lang['TopicsPerDay'] = 'Onderwerpen per dag';
$lang['MembersPerDay'] = 'Leden per dag';
$lang['BoardStarted'] = 'Forum gestart';
$lang['BoardAge'] = 'Leeftijd forum';
$lang['NewestMember'] = 'Nieuwste lid';
$lang['MostActiveTopics'] = 'Meest actieve onderwerpen';
$lang['MostViewedTopics'] = 'Meest bekeken onderwerpen';
$lang['PostsPerMember'] = 'Berichten per lid';
$lang['PostsPerForum'] = 'Berichten per forum';
$lang['Categories'] = 'Categorie&euml;n';
$lang['Forums'] = 'Forums';
$lang['TopicsPerMember'] = 'Onderwerpen per lid';
$lang['TopicsPerForum'] = 'Onderwerpen per forum';
$lang['MostActiveMembers'] = 'Meest actieve leden';
$lang['MostActiveForums'] = 'Meest actieve forums';
$lang['DisplayedNameTaken'] = 'Sorry, %s werd reeds als loginnaam of weergegeven naam ingenomen.';
$lang['SearchKeywords'] = 'Sleutelwoorden zoeken';
$lang['SearchMode'] = 'Zoekmodus';
$lang['SearchAuthor'] = 'Zoeken op auteur';
$lang['SearchForums'] = 'Zoeken in forums';
$lang['AllForums'] = 'Alle forums';
$lang['NoSearchResults'] = 'Sorry, maar er werden geen resultaten gevonden die aan de opgegeven criteria voldoen.';
$lang['SearchMembersPosts'] = 'Zoek berichten van lid';
$lang['CurrentPage'] = 'Huidige pagina';
$lang['MemberGuestOnline'] = 'In de laatste %d minuten hebben %d lid (%d verborgen) en %d gast het forum bezocht.';
$lang['MembersGuestOnline'] = 'In de laatste %d minuten hebben %d leden (%d verborgen) en %d gast het forum bezocht.';
$lang['MemberGuestsOnline'] = 'In de laatste %d minuten hebben %d lid (%d verborgen) en %d gasten het forum bezocht.';
$lang['MembersGuestsOnline'] = 'In de laatste %d minuten hebben %d leden (%d verborgen) en %d gasten het forum bezocht.';
$lang['WhosOnline'] = 'Wie is er Online';
$lang['Done'] = 'Klaar';
$lang['KeywordsExplain'] = 'Sleutelwoorden van minimum %d tekens gescheiden door spaties.';
$lang['BCCMyself'] = 'Verzend een kopie naar mijn eigen e-mailadres.';
$lang['Save'] = 'Opslaan';
$lang['Add'] = 'Toevoegen';
$lang['MarkAllAsRead'] = 'Alles gelezen markeren';
$lang['MarkAllAsReadDone'] = 'Alle forums en onderwerpen zijn nu als gelezen gemarkeerd.';
$lang['StringTooShort'] = '%s is te kort, er zijn tenminste %d karakters benodigd.';
$lang['StringTooLong'] = '%s is te lang, maximaal %d karakters zijn toegestaan.';
$lang['Upload'] = 'Uploaden';
$lang['RegistrationsDisabled'] = 'Registraties uitgeschakeld';
$lang['PostFormShortcut'] = 'Druk op Alt+S (Control+S op Mac OS) om dit formulier snel te verzenden.';
$lang['EditThisMember'] = 'Bewerk dit lid';
$lang['EmailTaken'] = 'Het e-mailadres %s wordt reeds op dit forum gebruikt. Gelieve een andere te kiezen.';
$lang['RegisteredNotActivatedByAdmin'] = 'Je account %s werd nu aangemaakt. Voor je kan inloggen moet de administrator van dit forum je account activeren. Gelieve hiervoor geduld uit te oefenen.';
$lang['AdminActivationEmailBody'] = 'Hallo,

Dit is de forumsoftware van [board_name]. Je hebt zonet een account genaamd [account_name] geregistreerd. De administrator van het forum moet je account echter nog activeren. Zodra dit is gebeurd, kan je inloggen met:

Gebruikersnaam: [account_name]
Wachtwoord: [password]

Indien je je wachtwoord vergeet kan je een nieuwe aanvragen via de link op het inlogformulier. Bedankt voor het registreren!

[board_name]
[board_link]
[admin_email]';
$lang['AdminActivationEmailSubject'] = 'Account wacht op activatie';
$lang['NewEmailNotActivatedByAdmin'] = 'Je profiel werd succesvol aangepast. Omdat je het e-mailadres van je account %s hebt aangepast, moet de administrator van dit forum je account opnieuw activeren. Ondertussen word je uitgelogd.';
$lang['NewEmailAdminActivationEmailBody'] = 'Hallo,

Dit is de forumsoftware van [board_name]. Je hebt zonet het e-mailadres van je account [account_name] aangepast, maar je account werd door de administrator nog niet gereactiveerd. Gelieve hiervoor geduld uit te oefenen.

[board_name]
[board_link]
[admin_email]';
$lang['NewEmailAdminActivationEmailSubject'] = 'Account wacht op reactivatie';
$lang['AdminActivatedAccountEmailBody'] = 'Hallo,

Dit is de forumsoftware van [board_name]. De administrator heeft je account [account_name] geactiveerd. Je kan nu inloggen.

[board_name]
[board_link]
[admin_email]';
$lang['AdminActivatedAccountEmailSubject'] = 'Account geactiveerd';
$lang['Sort'] = 'Sorteren';
$lang['SortBy'] = 'Sorteren op';
$lang['SortBy-displayed_name'] = 'Gebruikersnaam';
$lang['SortBy-real_name'] = 'Echte naam';
$lang['SortBy-level'] = 'Niveau';
$lang['SortBy-rank'] = 'Rank';
$lang['SortBy-regdate'] = 'Geregistreerd';
$lang['SortBy-posts'] = 'Berichten';
$lang['SortBy-latest_post'] = 'Laatste bericht';
$lang['SortBy-topic_title'] = 'Onderwerptitel';
$lang['SortBy-forum'] = 'Forum';
$lang['SortBy-author'] = 'Auteur';
$lang['SortBy-replies'] = 'Antwoorden';
$lang['SortBy-views'] = 'Bekeken';
$lang['SortOrder-asc'] = 'Oplopend';
$lang['SortOrder-desc'] = 'Aflopend';
$lang['NoUsersFound'] = 'Geen leden gevonden.';
$lang['SaveConfigManually'] = 'Omdat config.php niet beschrijfbaar is, kan UseBB de configuratie niet zelf opslaan. Gelieve de volgende inhoud manueel in config.php op te slaan en eventueel te uploaden.';
$lang['ShowResultsAs'] = 'Toon resultaten als';
$lang['ShowMode-topics'] = 'Onderwerpen';
$lang['ShowMode-posts'] = 'Berichten';
$lang['Mode-and'] = 'Alle zoekwoorden';
$lang['Mode-or'] = 'E&eacute;n of meerdere zoekwoorden';
$lang['ExactMatch'] = 'Exacte overeenkomst';
$lang['IncludeGuests'] = 'Inclusief gasten';
$lang['Top'] = 'Boven';
$lang['Bottom'] = 'Beneden';
$lang['Action'] = 'Actie';
$lang['AntiSpamQuestion'] = 'Anti-spamvraag';
$lang['AntiSpamQuestionInfo'] = 'Als een anti-spammaatregel moet het correcte antwoord op deze vraag worden gegeven alvorens dit component van het forum kan worden geopend. Per sessie wordt slechts &eacute;&eacute;n vraag gesteld, terwijl registreren dit volledig uitschakelt. Dank voor uw begrip.';
$lang['Question'] = 'Vraag';
$lang['Answer'] = 'Antwoord';
$lang['AntiSpamQuestionMathPlus'] = 'Hoeveel is %d plus %d?';
$lang['AntiSpamQuestionMathMinus'] = 'Hoeveel is %d min %d?';
$lang['AntiSpamWrongAnswer'] = 'Het verzonden antwoord is fout. Gelieve het juiste antwoord in te geven om dit component van het forum te openen.';
$lang['WrongUsernamePassword'] = 'De combinatie van gebruikersnaam (%s) en wachtwoord is niet correct. Gelieve opnieuw te proberen.';
$lang['WrongUsernameEmail'] = 'De combinatie van gebruikersnaam (%s) en e-mailadres bestaat niet. Gelieve opnieuw te proberen.';
$lang['All'] = 'Alles';
$lang['Staff'] = 'Staf';
$lang['Guests'] = 'Gasten';
$lang['ShowOnly'] = 'Toon enkel';
$lang['InvalidFormTokenNotice'] = 'De veiligheidstoken is onjuist of vervallen.

Indien je hier geleid werd via een link of website van een derde, gelieve deze pagina te negeren!

Indien je wijzigingen aanbracht en het formulier zelf verzond, gelieve het opnieuw te verzenden.';
$lang['InvalidURLTokenNotice']  = 'De veiligheidstoken is onjuist of vervallen.

Indien je hier geleid werd via een link of website van een derde, gelieve deze pagina te negeren!

Anderzijds, gelieve de oorspronkelijke pagina te herladen en de link opnieuw te bezoeken.';
$lang['Name'] = 'Naam';
$lang['GuestName'] = '%s (gast)';
$lang['RSSFeedForTopic'] = 'RSS-feed voor onderwerp %s';
$lang['RSSFeedForForum'] = 'RSS-feed voor forum %s';
$lang['PotentialSpammer'] = 'Potenti&euml;le spammer';
$lang['PotentialSpammerNoProfileLinks'] = 'Je hebt de (tijdelijke) status van potenti&euml;le spammer, wat betekent dat geen profiellinks ingesteld kunnen of getoond zullen worden. Bedankt voor je begip.';
$lang['PotentialSpammerNoPostLinks'] = 'Je hebt de (tijdelijke) status van potenti&euml;le spammer, wat betekent dat geen links in je berichten getoond zullen worden. Bedankt voor je begrip.';
$lang['InvisibleToGuests'] = 'Profielen zijn momenteel onzichtbaar voor anonieme gebruikers (gasten).';

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

?>
