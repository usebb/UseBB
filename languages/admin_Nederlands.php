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

$lang['AdminLogin'] = 'Administrator inloggen';
$lang['AdminPasswordExplain'] = 'Om veiligheidsredenen dien je je account\'s wachtwoord in te voeren om toegang te krijgen tot het ACP.';

$lang['RunningBadACPModule'] = 'UseBB kan deze module niet uitvoeren omdat een of meerdere aspecten ontbreken (geen $usebb_module object gevonden en/of ontbrekende run_module() objectmethode).';

$lang['Category-main'] = 'Algemeen';
$lang['Item-index'] = 'ACP-index';
$lang['Item-version'] = 'Versiecontrole';
$lang['Item-config'] = 'Algemene configuratie';
$lang['Category-forums'] = 'Forums';
$lang['Item-categories'] = 'Categorie&euml;n';
$lang['Item-forums'] = 'Forums';
$lang['Category-various'] = 'Diversen';
$lang['Item-iplookup'] = 'IP-adres opzoeken';
$lang['Item-sqltoolbox'] = 'SQL-werkset';
$lang['Item-modules'] = 'ACP-modules';

$lang['IndexWelcome'] = 'Welkom op het ACP van je UseBB-forum. Hier kan je alle aspecten van je forum beheren, zoals de instellingen wijzigen, forums beheren, leden, enz.';
$lang['IndexSystemInfo'] = 'Systeeminfo';
$lang['IndexUseBBVersion'] = 'UseBB-versie';
$lang['IndexPHPVersion'] = 'PHP-versie';
$lang['IndexSQLServer'] = 'SQL-server driver';
$lang['IndexHTTPServer'] = 'HTTP-server';
$lang['IndexOS'] = 'Besturingssysteem';
$lang['IndexLinks'] = 'Links';

$lang['VersionFailed'] = 'Het forum kon de laatste versie niet achterhalen (%s is uitgeschakeld). Gelieve geregeld %s te bezoeken om er zeker van te zijn dat je de laatste versie hebt.';
$lang['VersionLatestVersion'] = 'Dit forum wordt aangedreven door UseBB %s welke de laatste stabiele versie is.';
$lang['VersionNeedUpdate'] = 'Dit forum draaiend op UseBB %s moet geupdate worden naar versie %s om veilig en bugvrij te blijven! Bezoek %s om de laatste versie te downloaden.';
$lang['VersionBewareDevVersions'] = 'Dit forum draait op versie %s hoewel %s nog steeds de laatste stabiele versie is. Hou rekening met de (compatibiliteits)problemen die kunnen bestaan met ontwikkelingsversies.';

$lang['ConfigInfo'] = 'Op deze pagina kan je alle instellingen van je forum wijzigen. Wees voorzichtig met het aanpassen van de databaseconfiguratie. Velden aangeduid met een asterisk (*) zijn verplicht.';
$lang['ConfigSet'] = 'De nieuwe configuratie werd opgeslagen. Het zal worden ingeladen bij het laden van een nieuwe pagina.';
$lang['ConfigMissingFields'] = 'Sommige velden werden foutief ingevuld (bv. tekst waar een getal werd verwacht). Gelieve de volgende velden te controleren:';
$lang['ConfigBoard-type'] = 'Type';
$lang['ConfigBoard-server'] = 'Server';
$lang['ConfigBoard-username'] = 'Gebruikersnaam';
$lang['ConfigBoard-passwd'] = 'Wachtwoord';
$lang['ConfigBoard-dbname'] = 'Databasenaam';
$lang['ConfigBoard-prefix'] = 'Tabel-prefix';
$lang['ConfigBoardSection-general'] = 'Algemeen';
$lang['ConfigBoardSection-cookies'] = 'Cookies';
$lang['ConfigBoardSection-sessions'] = 'Sessies';
$lang['ConfigBoardSection-page_counts'] = 'Pagina-aantallen';
$lang['ConfigBoardSection-date_time'] = 'Datums &amp; Tijden';
$lang['ConfigBoardSection-database'] = 'Database-configuratie';
$lang['ConfigBoardSection-advanced'] = 'Geavanceerde instellingen';
$lang['ConfigBoardSection-email'] = 'E-mail';
$lang['ConfigBoardSection-additional'] = 'Bijkomende functies';
$lang['ConfigBoardSection-user_rights'] = 'Gebruikersrechten';
$lang['ConfigBoardSection-layout'] = 'Layout-instellingen';
$lang['ConfigBoard-admin_email'] = 'Admin e-mailadres';
$lang['ConfigBoard-board_descr'] = 'Forumomschrijving';
$lang['ConfigBoard-board_keywords'] = 'Forum-kernwoorden (gescheiden door komma\'s)';
$lang['ConfigBoard-board_name'] = 'Forumnaam';
$lang['ConfigBoard-date_format'] = 'Datumformaat';
$lang['ConfigBoard-language'] = 'Standaard taal';
$lang['ConfigBoard-session_name'] = 'Sessienaam';
$lang['ConfigBoard-template'] = 'Standaard template';
$lang['ConfigBoard-active_topics_count'] = 'Aantal actieve onderwerpen';
$lang['ConfigBoard-avatars_force_width'] = 'Vaste avatarbreedte (px)';
$lang['ConfigBoard-avatars_force_height'] = 'Vaste avatarhoogte (px)';
$lang['ConfigBoard-debug'] = 'Debug-modus';
$lang['ConfigBoard-email_view_level'] = 'E-mailadressen weergeven';
$lang['ConfigBoard-flood_interval'] = 'Flood-interval (seconden)';
$lang['ConfigBoard-members_per_page'] = 'Leden per pagina';
$lang['ConfigBoard-online_min_updated'] = 'Online gebruikers gedurende voorbije minuten';
$lang['ConfigBoard-output_compression'] = 'Uitvoercompressie';
$lang['ConfigBoard-passwd_min_length'] = 'Wachtwoord minimumlengte';
$lang['ConfigBoard-posts_per_page'] = 'Berichten per pagina';
$lang['ConfigBoard-rss_items_count'] = 'Aantal RSS-items';
$lang['ConfigBoard-search_limit_results'] = 'Beperk zoekresultaten tot x items';
$lang['ConfigBoard-search_nonindex_words_min_length'] = 'Minimale lengte van zoekwoord';
$lang['ConfigBoard-session_max_lifetime'] = 'Maximale sessieleeftijd (seconden)';
$lang['ConfigBoard-show_edited_message_timeout'] = 'Toon bewerkt bericht-mededeling enkel wanneer bewerkt na x seconden na het plaatsen';
$lang['ConfigBoard-topicreview_posts'] = 'Aantal berichten in onderwerpreview';
$lang['ConfigBoard-topics_per_page'] = 'Onderwerpen per pagina';
$lang['ConfigBoard-view_detailed_online_list_min_level'] = 'Vereiste niveau voor bekijken gedetailleerde onlinelijst';
$lang['ConfigBoard-view_forum_stats_box_min_level'] = 'Vereiste niveau voor bekijken kleine statistieken';
$lang['ConfigBoard-view_hidden_email_addresses_min_level'] = 'Vereiste niveau voor bekijken verborgen e-mailadressen';
$lang['ConfigBoard-view_memberlist_min_level'] = 'Vereiste niveau voor bekijken ledenlijst';
$lang['ConfigBoard-view_stafflist_min_level'] = 'Vereiste niveau voor bekijken staflijst';
$lang['ConfigBoard-view_stats_min_level'] = 'Vereiste niveau voor bekijken statistiekenpagina';
$lang['ConfigBoard-view_contactadmin_min_level'] = 'Vereiste niveau voor bekijken \'admin contacteren\'-link';
$lang['ConfigBoard-allow_multi_sess'] = 'Sta meerdere sessies toe per IP-adres';
$lang['ConfigBoard-board_closed'] = 'Sluit het forum';
$lang['ConfigBoard-cookie_secure'] = 'Beveiligde cookies (voor HTTPS)';
$lang['ConfigBoard-disable_info_emails'] = 'Informatie-emails uitschakelen';
$lang['ConfigBoard-dst'] = 'Zomertijd';
$lang['ConfigBoard-enable_contactadmin'] = '\'Admin contacteren\'-link inschakelen';
$lang['ConfigBoard-enable_detailed_online_list'] = 'Gedetailleerde onlinelijst inschakelen';
$lang['ConfigBoard-enable_forum_stats_box'] = 'Kleine statistieken inschakelen';
$lang['ConfigBoard-enable_memberlist'] = 'Ledenlijst inschakelen';
$lang['ConfigBoard-enable_quickreply'] = 'Snel antwoord inschakelen';
$lang['ConfigBoard-enable_rss'] = 'RSS-feed inschakelen';
$lang['ConfigBoard-enable_stafflist'] = 'Staflijst inschakelen';
$lang['ConfigBoard-enable_stats'] = 'Statistiekenpagina inschakelen';
$lang['ConfigBoard-friendly_urls'] = 'Vriendelijke URL\'s inschakelen';
$lang['ConfigBoard-guests_can_access_board'] = 'Gasten kunnen het forum bekijken';
$lang['ConfigBoard-guests_can_view_profiles'] = 'Gasten kunnen profielen bekijken';
$lang['ConfigBoard-hide_avatars'] = 'Verberg alle avatars';
$lang['ConfigBoard-hide_signatures'] = 'Verberg alle handtekeningen';
$lang['ConfigBoard-hide_userinfo'] = 'Verberg gebruikersinformatie';
$lang['ConfigBoard-rel_nofollow'] = 'Google\'s nofollow inschakelen voor BBCode-links';
$lang['ConfigBoard-return_to_topic_after_posting'] = 'Terugkeren naar onderwerp na het plaatsen';
$lang['ConfigBoard-sig_allow_bbcode'] = 'BBCode in handtekeningen inschakelen';
$lang['ConfigBoard-sig_allow_smilies'] = 'Smilies in handtekeningen inschakelen';
$lang['ConfigBoard-sig_max_length'] = 'Maximale lengte handtekening';
$lang['ConfigBoard-single_forum_mode'] = 'Enkel forum-modus (indien van toepassing)';
$lang['ConfigBoard-target_blank'] = 'BBCode-links openen een nieuw venster';
$lang['ConfigBoard-users_must_activate'] = 'E-mailactivatie inschakelen';
$lang['ConfigBoard-board_closed_reason'] = 'Reden van sluiting';
$lang['ConfigBoard-board_url'] = 'Forum-URL (leeg voor automatisch detecteren)';
$lang['ConfigBoard-cookie_domain'] = 'Cookie-domein';
$lang['ConfigBoard-cookie_path'] = 'Cookie-pad';
$lang['ConfigBoard-session_save_path'] = 'Map voor opslaan sessiedata';
$lang['ConfigBoard-exclude_forums_active_topics'] = 'Forums uit actieve onderwerpen filteren';
$lang['ConfigBoard-exclude_forums_rss'] = 'Forums uit RSS-feed filteren';
$lang['ConfigBoard-exclude_forums_stats'] = 'Forums uit statistiekenpagina filteren';
$lang['ConfigBoard-timezone'] = 'Tijdzone';
$lang['ConfigBoard-debug0'] = 'Uitgeschakeld';
$lang['ConfigBoard-debug1'] = 'Eenvoudige debuginformatie';
$lang['ConfigBoard-debug2'] = 'Uitgebreide debuginformatie';
$lang['ConfigBoard-email_view_level0'] = 'Verberg alle e-mailadressen';
$lang['ConfigBoard-email_view_level1'] = 'E-mailformulier inschakelen';
$lang['ConfigBoard-email_view_level2'] = 'Tonen met spambeveiliging';
$lang['ConfigBoard-email_view_level3'] = 'Onbewerkt tonen';
$lang['ConfigBoard-output_compression0'] = 'Uitgeschakeld';
$lang['ConfigBoard-output_compression1'] = 'Comprimeer HTML';
$lang['ConfigBoard-output_compression2'] = 'Gzip inschakelen';
$lang['ConfigBoard-output_compression3'] = 'Comprimeer HTML en schakel gzip in';
$lang['ConfigBoard-level0'] = 'Gasten';
$lang['ConfigBoard-level1'] = 'Leden';
$lang['ConfigBoard-level2'] = 'Moderators';
$lang['ConfigBoard-level3'] = 'Administrators';
$lang['ConfigBoard-enable_acp_modules'] = 'ACP-modules inschakelen';

$lang['CategoriesInfo'] = 'Deze sectie geeft je controle over de categorie&euml;n van je forum.';
$lang['CategoriesAddNewCat'] = 'Nieuwe categorie aanmaken';
$lang['CategoriesAdjustSortIDs'] = 'Sort.-ID\'s aanpassen';
$lang['CategoriesSortAutomatically'] = 'Automatisch sorteren';
$lang['CategoriesNoCatsExist'] = 'Dit forum bevat nog geen categorie&eumln.';
$lang['CategoriesCatName'] = 'Categorienaam';
$lang['CategoriesSortID'] = 'Sort.-ID';
$lang['CategoriesMissingFields'] = 'Enkele vereiste velden ontbreken. Gelieve deze in te vullen.';
$lang['CategoriesSortChangesApplied'] = 'Je aanpassingen aan de sort-ID\'s werden opgeslagen.';
$lang['CategoriesConfirmCatDelete'] = 'Bevestigen van verwijderen categorie';
$lang['CategoriesConfirmCatDeleteContent'] = 'Ben je zeker dat je de categorie %s wilt verwijderen? Deze actie is onomkeerbaar!';
$lang['CategoriesMoveContents'] = 'Verplaats de inhoud van de categorie naar %s';
$lang['CategoriesDeleteContents'] = 'Verwijder de inhoud';
$lang['CategoriesEditingCat'] = 'Bewerk categorie %s';

$lang['ForumsInfo'] = 'Deze sectie geeft je de controle over de fora van je forum.';
$lang['ForumsAddNewForum'] = 'Nieuw forum aanmaken';
$lang['ForumsAdjustSortIDs'] = 'Sort.-ID\'s aanpassen';
$lang['ForumsSortAutomatically'] = 'Automatisch sorteren';
$lang['ForumsNoForumsExist'] = 'Dit forum bevat nog geen fora.';
$lang['ForumsForumName'] = 'Forumnaam';
$lang['ForumsCatName'] = 'Bovenliggende categorie';
$lang['ForumsDescription'] = 'Omschrijving';
$lang['ForumsStatus'] = 'Status';
$lang['ForumsStatusOpen'] = 'Open';
$lang['ForumsAutoLock'] = 'Onderwerpen automatisch sluiten na x antwoorden';
$lang['ForumsIncreasePostCount'] = 'Gebruikers\' berichtenteller aanpassen';
$lang['ForumsHideModsList'] = 'Lijst met moderators verbergen';
$lang['ForumsSortID'] = 'Sort.-ID';
$lang['ForumsMissingFields'] = 'Enkele vereiste velden ontbreken. Gelieve deze in te vullen.';
$lang['ForumsSortChangesApplied'] = 'Je aanpassingen aan de sort-ID\'s werden opgeslagen.';
$lang['ForumsConfirmForumDelete'] = 'Bevestigen van verwijderen forum';
$lang['ForumsConfirmForumDeleteContent'] = 'Ben je zeker dat je het forum %s wilt verwijderen? Deze actie is onomkeerbaar!';
$lang['ForumsMoveContents'] = 'Verplaats de inhoud van het forum naar %s';
$lang['ForumsDeleteContents'] = 'Verwijder de inhoud';
$lang['ForumsEditingForum'] = 'Bewerk forum %s';
$lang['ForumsGeneral'] = 'Algemene instellingen';
$lang['ForumsAuth'] = 'Authorizatie-instellingen';
$lang['ForumsAuthNote'] = 'Instellingen be&iuml;nvloeden elkaar niet!';
$lang['Forums-level0'] = 'Gasten';
$lang['Forums-level1'] = 'Leden';
$lang['Forums-level2'] = 'Moderators';
$lang['Forums-level3'] = 'Administrators';
$lang['Forums-auth0'] = 'Forum bekijken';
$lang['Forums-auth1'] = 'Onderwerpen bekijken';
$lang['Forums-auth2'] = 'Nieuwe onderwerpen plaatsen';
$lang['Forums-auth3'] = 'Onderwerpen beantwoorden';
$lang['Forums-auth4'] = 'Berichten van anderen bewerken';
$lang['Forums-auth5'] = 'Onderwerpen verplaatsen';
$lang['Forums-auth6'] = 'Onderwerpen en berichten verwijderen';
$lang['Forums-auth7'] = 'Onderwerpen sluiten';
$lang['Forums-auth8'] = 'Stickies plaatsen';
$lang['Forums-auth9'] = 'HTML plaatsen (gevaarlijk)';

$lang['IPLookupInfo'] = 'Voer een IP-adres in om de overeenkomstige hostnaam op te zoeken.';
$lang['IPLookupResult'] = 'De hostnaam die overeenkomt met het IP-adres %s is %s.';
$lang['IPLookupNotFound'] = 'Geen overeenkomstige hostnaam voor %s kon worden gevonden.';

$lang['SQLToolboxWarningTitle'] = 'Belangrijke waarschuwing!';
$lang['SQLToolboxWarningContent'] = 'Wees voorzichtig met de query-uitvoermogelijkheid. Het uitvoeren van ALTER, DELETE, TRUNCATE of andere soorten queries kunnen je forum onomkeerbaar beschadigen! Gebruik dit enkel wanneer je weet wat je doet.';
$lang['SQLToolboxExecuteQuery'] = 'Query uitvoeren';
$lang['SQLToolboxExecuteQueryInfo'] = 'Voer een query in om deze uit te voeren. Resultaten worden eventueel in een tweede tekstveld weergegeven.';
$lang['SQLToolboxExecute'] = 'Uitvoeren';
$lang['SQLToolboxExecutedSuccessfully'] = 'Query succesvol uitgevoerd.';
$lang['SQLToolboxMaintenance'] = 'Onderhoud';
$lang['SQLToolboxMaintenanceInfo'] = 'Deze functies optimaliseren en herstellen de SQL-tabellen gebruikt door UseBB. De tabellen geregeld optimaliseren is aangeraden voor grotere forums.';
$lang['SQLToolboxRepairTables'] = 'Tabellen herstellen';
$lang['SQLToolboxOptimizeTables'] = 'Tabellen optimaliseren';
$lang['SQLToolboxMaintenanceNote'] = 'Merk op: dit herstelt geen verloren data in de database.';

$lang['ModulesInfo'] = 'ACP-modules maken het mogelijk het ACP uit te breiden met eigen functies of functies gemaakt door derden. Modules kunnen gevonden worden via de UseBB-website: %s.';
$lang['ModulesOverview'] = 'Modules-overzicht';
$lang['ModulesLongName'] = 'Lange naam';
$lang['ModulesShortName'] = 'Korte naam';
$lang['ModulesCategory'] = 'Categorie';
$lang['ModulesFilename'] = 'Bestandsnaam';
$lang['ModulesDisabled'] = 'ACP-modules zijn uitgeschakeld in de forumconfiguratie.';
$lang['ModulesNoneAvailable'] = 'Op dit moment zijn er geen modules ingeladen.';

?>
