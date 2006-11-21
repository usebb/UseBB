<?php

/*
	Copyright (C) 2003-2006 UseBB Team
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
$lang['Category-forums'] = 'Forums';
$lang['Category-various'] = 'Diversen';
$lang['Category-members'] = 'Leden';
$lang['Category-pruning'] = 'Pruning';
$lang['Category-security'] = 'Veiligheid';
$lang['Item-index'] = 'ACP-index';
$lang['Item-version'] = 'Versiecontrole';
$lang['Item-config'] = 'Algemene configuratie';
$lang['Item-categories'] = 'Categorie&euml;n Beheren';
$lang['Item-forums'] = 'Forums Beheren';
$lang['Item-iplookup'] = 'IP-adres opzoeken';
$lang['Item-sqltoolbox'] = 'SQL-werkset';
$lang['Item-modules'] = 'ACP-modules';
$lang['Item-members'] = 'Leden Bewerken';
$lang['Item-delete_members'] = 'Leden Verwijderen';
$lang['Item-register_members'] = 'Leden Registreren';
$lang['Item-activate_members'] = 'Leden Activeren';
$lang['Item-prune_forums'] = 'Forums Prunen';
$lang['Item-prune_members'] = 'Leden Prunen';
$lang['Item-dnsbl'] = 'DNSBL-bans';
$lang['Item-badwords'] = 'Woordcensuur';
$lang['Item-mass_email'] = 'Massa-email';
$lang['Item-bans'] = 'Banbeheer';

$lang['IndexWelcome'] = 'Welkom op het ACP van je UseBB-forum. Hier kan je alle aspecten van je forum beheren, zoals de instellingen wijzigen, forums beheren, leden, enz.';
$lang['IndexSystemInfo'] = 'Systeeminfo';
$lang['IndexUseBBVersion'] = 'UseBB-versie';
$lang['IndexPHPVersion'] = 'PHP-versie';
$lang['IndexSQLServer'] = 'SQL-server driver';
$lang['IndexHTTPServer'] = 'HTTP-server';
$lang['IndexOS'] = 'Besturingssysteem';
$lang['IndexServerLoad'] = 'Serverbelastingswaardes';
$lang['IndexLinks'] = 'Links';
$lang['IndexUnactiveMembers'] = 'Niet-geactiveerde Leden';
$lang['IndexNoUnactiveMembers'] = 'Er zijn geen leden die wachten op admin-activatie.';
$lang['IndexOneUnactiveMember'] = 'Er is 1 lid die wacht op admin-activatie.';
$lang['IndexMoreUnactiveMembers'] = 'Er zijn %d leden die wachten op admin-activatie.';
$lang['IndexWarning'] = 'Waarschuwing!';
$lang['IndexUnwritableConfig'] = 'Op dit moment is %s niet door PHP beschrijfbaar. Dit wordt niet aangeraden. Om dit beschrijfbaar te maken, pas de permissies aan met een FTP-client of voer een chmod-operatie (0777) op het bestand uit. Contacteer je host in geval van problemen.';

$lang['VersionFailed'] = 'Het forum kon de laatste versie niet achterhalen (%s is uitgeschakeld). Gelieve geregeld %s te bezoeken om er zeker van te zijn dat je de laatste versie hebt.';
$lang['VersionLatestVersionTitle'] = 'Dit is de laatste versie';
$lang['VersionLatestVersion'] = 'Dit forum wordt aangedreven door UseBB %s welke de laatste stabiele versie is.';
$lang['VersionNeedUpdateTitle'] = 'Nieuwe versie beschikbaar!';
$lang['VersionNeedUpdate'] = 'Dit forum draaiend op UseBB %s moet geupdate worden naar versie %s om veilig en bugvrij te blijven! Bezoek %s om de laatste versie te downloaden.';
$lang['VersionBewareDevVersionsTitle'] = 'Ontwikkelingsversie gevonden';
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
$lang['ConfigBoardSection-cookies-info'] = 'Je mag deze leeg laten voor auto-detectie.';
$lang['ConfigBoardSection-sessions'] = 'Sessies';
$lang['ConfigBoardSection-page_counts'] = 'Pagina-aantallen';
$lang['ConfigBoardSection-date_time'] = 'Datums &amp; Tijden';
$lang['ConfigBoardSection-date_time-info'] = 'Alleen van toepassing op gasten en nieuwe accounts.';
$lang['ConfigBoardSection-database'] = 'Database-configuratie';
$lang['ConfigBoardSection-database-info'] = 'Pas deze enkel aan als je zeker bent dat de nieuwe instellingen meteen werken.';
$lang['ConfigBoardSection-advanced'] = 'Geavanceerde instellingen';
$lang['ConfigBoardSection-email'] = 'E-mail';
$lang['ConfigBoardSection-additional'] = 'Bijkomende functies';
$lang['ConfigBoardSection-user_rights'] = 'Gebruikersrechten';
$lang['ConfigBoardSection-layout'] = 'Layout-instellingen';
$lang['ConfigBoardSection-min_levels'] = 'Minimale toegangsniveau\'s';
$lang['ConfigBoardSection-min_levels-info'] = 'Dit zijn de minimale niveau\'s benodigd om elk item te bekijken.';
$lang['ConfigBoardSection-security'] = 'Veiligheid';
$lang['ConfigBoard-admin_email'] = 'Admin e-mailadres';
$lang['ConfigBoard-board_descr'] = 'Forumomschrijving';
$lang['ConfigBoard-board_keywords'] = 'Forum-kernwoorden';
$lang['ConfigBoard-board_keywords-info'] = 'Gescheiden door komma\'s.';
$lang['ConfigBoard-board_name'] = 'Forumnaam';
$lang['ConfigBoard-date_format'] = 'Datumformaat';
$lang['ConfigBoard-date_format-info'] = 'Zelfde syntax als PHP\'s date().';
$lang['ConfigBoard-language'] = 'Standaardtaal';
$lang['ConfigBoard-language-info'] = 'Alleen van toepassing op gasten en nieuwe accounts.';
$lang['ConfigBoard-session_name'] = 'Sessienaam';
$lang['ConfigBoard-session_name-info'] = 'Alleen alfanumerieke karakters, geen spaties. Moet ten minste 1 letter bevatten.';
$lang['ConfigBoard-template'] = 'Standaard template';
$lang['ConfigBoard-template-info'] = 'Alleen van toepassing op gasten en nieuwe accounts.';
$lang['ConfigBoard-active_topics_count'] = 'Aantal actieve onderwerpen';
$lang['ConfigBoard-avatars_force_width'] = 'Vaste avatarbreedte (px)';
$lang['ConfigBoard-avatars_force_height'] = 'Vaste avatarhoogte (px)';
$lang['ConfigBoard-avatars_force_width-info'] = 'Laat leeg of nul om uit te schakelen.';
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
$lang['ConfigBoard-session_max_lifetime'] = 'Maximale sessieleeftijd (minuten)';
$lang['ConfigBoard-show_edited_message_timeout'] = 'Bewerkt bericht-mededeling';
$lang['ConfigBoard-show_edited_message_timeout-info'] = 'Wanneer het bericht binnen deze seconden na het plaatsen werd bewerkt, blijft de mededeling verborgen.';
$lang['ConfigBoard-topicreview_posts'] = 'Aantal berichten in onderwerpreview';
$lang['ConfigBoard-topics_per_page'] = 'Onderwerpen per pagina';
$lang['ConfigBoard-view_active_topics_min_level'] = 'Actieve onderwerpen';
$lang['ConfigBoard-view_detailed_online_list_min_level'] = 'Gedetailleerde onlinelijst';
$lang['ConfigBoard-view_forum_stats_box_min_level'] = 'Kleine statistieken';
$lang['ConfigBoard-view_hidden_email_addresses_min_level'] = 'Verborgen e-mailadressen';
$lang['ConfigBoard-view_memberlist_min_level'] = 'Ledenlijst';
$lang['ConfigBoard-view_search_min_level'] = 'Zoekmachine';
$lang['ConfigBoard-view_stafflist_min_level'] = 'Staflijst';
$lang['ConfigBoard-view_stats_min_level'] = 'Statistiekenpagina';
$lang['ConfigBoard-view_contactadmin_min_level'] = '\'Admin contacteren\'-link';
$lang['ConfigBoard-allow_multi_sess'] = 'Sta meerdere sessies toe per IP-adres';
$lang['ConfigBoard-board_closed'] = 'Sluit het forum';
$lang['ConfigBoard-board_closed-info'] = 'Admins kunnen nog steeds inloggen.';
$lang['ConfigBoard-cookie_secure'] = 'Beveiligde cookies';
$lang['ConfigBoard-cookie_secure-info'] = 'Versleutelde cookies (enkel HTTPS)';
$lang['ConfigBoard-cookie_httponly'] = 'Gebruik HTTP-only cookies';
$lang['ConfigBoard-cookie_httponly-info'] = 'Voegt een httpOnly-vlag toe aan de cookies, welke deze veiliger maakt tegen XSS.';
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
$lang['ConfigBoard-friendly_urls-info'] = 'Vereist Apache en mod_rewrite. Schakelt sessie-ID\'s in URL uit.';
$lang['ConfigBoard-guests_can_access_board'] = 'Gasten kunnen het forum bekijken';
$lang['ConfigBoard-guests_can_see_contact_info'] = 'Gasten kunnen contactinformatie in profielen bekijken';
$lang['ConfigBoard-guests_can_view_profiles'] = 'Gasten kunnen profielen bekijken';
$lang['ConfigBoard-hide_avatars'] = 'Verberg alle avatars';
$lang['ConfigBoard-hide_signatures'] = 'Verberg alle handtekeningen';
$lang['ConfigBoard-hide_userinfo'] = 'Verberg gebruikersinformatie';
$lang['ConfigBoard-rel_nofollow'] = 'Google\'s nofollow inschakelen';
$lang['ConfigBoard-rel_nofollow-info'] = 'Dit laat Google alle links in BBCode negeren.';
$lang['ConfigBoard-return_to_topic_after_posting'] = 'Terugkeren naar onderwerp na het plaatsen';
$lang['ConfigBoard-sig_allow_bbcode'] = 'BBCode in handtekeningen inschakelen';
$lang['ConfigBoard-sig_allow_smilies'] = 'Smilies in handtekeningen inschakelen';
$lang['ConfigBoard-sig_max_length'] = 'Maximale lengte handtekening';
$lang['ConfigBoard-single_forum_mode'] = 'Enkel forum-modus';
$lang['ConfigBoard-single_forum_mode-info'] = 'Geef het enige zichtbare forum weer als forum-index.';
$lang['ConfigBoard-target_blank'] = 'BBCode-links openen een nieuw venster';
$lang['ConfigBoard-activation_mode'] = 'Activatiemodus';
$lang['ConfigBoard-activation_mode0'] = 'Geen activatie';
$lang['ConfigBoard-activation_mode1'] = 'E-mailactivatie';
$lang['ConfigBoard-activation_mode2'] = 'Admin-activatie';
$lang['ConfigBoard-board_closed_reason'] = 'Reden van sluiting';
$lang['ConfigBoard-board_url'] = 'Forum-URL';
$lang['ConfigBoard-board_url-info'] = 'Volledige URL inclusief eindigende /; laat leeg voor auto-detectie.';
$lang['ConfigBoard-cookie_domain'] = 'Cookie-domein';
$lang['ConfigBoard-cookie_path'] = 'Cookie-pad';
$lang['ConfigBoard-session_save_path'] = 'Map voor opslaan sessiedata';
$lang['ConfigBoard-session_save_path-info'] = 'Aangepaste locatie voor opslag sessiedata; alleen absolute mapnamen.';
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
$lang['ConfigBoard-disable_registrations'] = 'Gebruikersregistratie uitschakelen';
$lang['ConfigBoard-disable_registrations-info'] = 'Gebruikers kunnen nog steeds via het ACP worden geregistreerd.';
$lang['ConfigBoard-disable_registrations_reason'] = 'Reden van uitschakeling';
$lang['ConfigBoard-allow_duplicate_emails'] = 'Gedupliceerde e-mailadressen toestaan';
$lang['ConfigBoard-enable_badwords_filter'] = 'Woordcensuur-filter inschakelen';
$lang['ConfigBoard-enable_ip_bans'] = 'IP-adresbans inschakelen';
$lang['ConfigBoard-show_raw_entities_in_code'] = 'Toon ruwe entiteiten in [code]-tags.';
$lang['ConfigBoard-show_raw_entities_in_code-info'] = 'Toon de ruwe entiteitcode i.p.v. zijn HTML-representatie.';
$lang['ConfigBoard-username_min_length'] = 'Gebruikersnaam minimumlengte';
$lang['ConfigBoard-username_max_length'] = 'Gebruikersnaam maximumlengte';
$lang['ConfigBoard-show_never_activated_members'] = 'Toon nooit geactiveerde leden';
$lang['ConfigBoard-show_never_activated_members-info'] = 'Toon deze in de statistieken en ledenlijst.';
$lang['ConfigBoard-enable_registration_log'] = 'Registratielog inschakelen';
$lang['ConfigBoard-enable_registration_log-info'] = 'Schrijft een registratielog naar een tekstbestand.';
$lang['ConfigBoard-registration_log_file'] = 'Registratielogbestand';
$lang['ConfigBoard-registration_log_file-info'] = 'Relatief t.o.v. de map van het forum, of een absoluut pad.';
$lang['ConfigBoard-enable_email_dns_check'] = 'E-mailadres DNS-controle inschakelen';
$lang['ConfigBoard-enable_email_dns_check-info'] = 'Valideert e-mailadressen door MX-records op te zoeken. Dit werkt mogelijk niet op alle domeinen.';
$lang['ConfigBoard-edit_post_timeout'] = 'Bericht bewerken-timeout';
$lang['ConfigBoard-edit_post_timeout-info'] = 'Een gebruiker kan zijn berichten bewerken binnen x seconden na het posten.';
$lang['ConfigBoard-disable_xhtml_header'] = 'XHTML-header voor XHTML-templates uitschakelen';
$lang['ConfigBoard-disable_xhtml_header-info'] = 'Een XHTML Content-Type kan alleen gebruikt worden wanneer de inhoud 100% welgevormd is. Dit is altijd uitgeschakeld voor niet-XHTML browsers.';
$lang['ConfigBoard-email_reply-to_header'] = 'Gebruik Reply-To-header';
$lang['ConfigBoard-email_reply-to_header-info'] = 'Gebruik Reply-To in plaats van From voor gebruiker\'s e-mailadres (vereist op sommige hosts).';

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
$lang['ForumsDescriptionExplain'] = 'Dit is een HTML-veld. Indien je speciale karakters wenst te gebruiken, vervang deze dan door de juiste HTML-entiteiten (bvb. &amp;amp; i.p.v. &amp;).';
$lang['ForumsStatus'] = 'Status';
$lang['ForumsStatusOpen'] = 'Open';
$lang['ForumsAutoLock'] = 'Automatisch sluiten';
$lang['ForumsAutoLockXReplies'] = 'Sluiten na %s antwoorden.';
$lang['ForumsIncreasePostCount'] = 'Gebruikers\' berichtenteller aanpassen';
$lang['ForumsModerators'] = 'Moderators';
$lang['ForumsModeratorsExplain'] = 'Gebruikersnamen (niet weergegeven namen), gescheiden door komma\'s. Hoofdletterongevoelig.';
$lang['ForumsModeratorsUnknown'] = 'Onbekende leden: %s.';
$lang['ForumsHideModsList'] = 'Lijst met moderators verbergen';
$lang['ForumsSortID'] = 'Sort.-ID';
$lang['ForumsMissingFields'] = 'Enkele vereiste velden ontbreken. Gelieve deze in te vullen.';
$lang['ForumsSortChangesApplied'] = 'Je aanpassingen aan de sort-ID\'s werden opgeslagen.';
$lang['ForumsConfirmForumDelete'] = 'Bevestigen van verwijderen forum';
$lang['ForumsConfirmForumDeleteContent'] = 'Ben je zeker dat je het forum %s wilt verwijderen? Deze actie is onomkeerbaar!';
$lang['ForumsMoveContents'] = 'Verplaats de inhoud van het forum naar %s';
$lang['ForumsMoveModerators'] = 'Bij het verplaatsen van inhoud, verplaats ook moderators.';
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

$lang['IPLookupSearchHostname'] = 'Zoek hostnaam';
$lang['IPLookupSearchUsernames'] = 'Zoek gebruikersnamen';
$lang['IPLookupHostname'] = 'Hostnaam';
$lang['IPLookupHostnameNotFound'] = 'Geen overeenkomstige hostnaam gevonden.';
$lang['IPLookupUsernames'] = 'Gebruikersnamen';
$lang['IPLookupUsernamesNotFound'] = 'Geen overeenkomstige gebruikersnamen gevonden.';

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
$lang['ModulesLongName'] = 'Lange naam';
$lang['ModulesShortName'] = 'Korte naam';
$lang['ModulesCategory'] = 'Categorie';
$lang['ModulesFilename'] = 'Bestandsnaam';
$lang['ModulesDeleteNotPermitted'] = 'Niet toegestaan';
$lang['ModulesDisabled'] = 'ACP-modules zijn uitgeschakeld in de forumconfiguratie.';
$lang['ModulesNoneAvailable'] = 'Op dit moment zijn er geen modules ingeladen.';
$lang['ModulesUpload'] = 'Module uploaden';
$lang['ModulesUploadInfo'] = 'Voer een lokale bestandsnaam van een UseBB ACP-module in om deze te uploaden.';
$lang['ModulesUploadDuplicateModule'] = 'Er bestaat reeds een module onder de bestandsnaam %s. Gelieve deze eerst te verwijderen.';
$lang['ModulesUploadNoValidModule'] = 'Het bestand %s is geen geldige ACP-module.';
$lang['ModulesUploadFailed'] = 'De module %s kon niet ge&iuml;nstalleerd worden. Kopi&euml;ren mislukt.';
$lang['ModulesUploadDisabled'] = 'De module-map is niet beschrijfbaar. Uploaden werd uitgeschakeld. Om dit in te schakelen, maak de map %s beschrijfbaar door de webserver (probeer chmod 777).';
$lang['ModulesConfirmModuleDelete'] = 'Bevestigen van verwijderen module';
$lang['ModulesConfirmModuleDeleteInfo'] = 'Ben je zeker dat je de module %s (%s) wilt verwijderen?';

$lang['MembersSearchMember'] = 'Lid zoeken';
$lang['MembersSearchMemberInfo'] = 'Voer een (deel van een) gebruikersnaam of weergegeven naam in om te bewerken.';
$lang['MembersSearchMemberExplain'] = 'Gebruikersnaam of weergegeven naam';
$lang['MembersSearchMemberNotFound'] = 'Geen leden met gebruikersnaam of weergegeven naam %s gevonden.';
$lang['MembersSearchMemberList'] = 'De volgende leden werden gevonden';
$lang['MembersEditingMember'] = 'Bewerk lid %s';
$lang['MembersEditingMemberInfo'] = 'Pas de informatie van het lid aan en verzend het formulier. Velden met een asterisk (*) zijn verplicht.';
$lang['MembersEditingMemberUsernameExists'] = 'De gebruikersnaam %s bestaat reeds als een gebruikersnaam of weergegeven naam.';
$lang['MembersEditingMemberDisplayedNameExists'] = 'De weergegeven naam %s bestaat reeds als een gebruikersnaam of weergegeven naam.';
$lang['MembersEditingMemberBanned'] = 'Geband';
$lang['MembersEditingMemberBannedReason'] = 'Reden voor ban';
$lang['MembersEditingMemberCantChangeOwnLevel'] = 'Je kan je eigen niveau niet aanpassen.';
$lang['MembersEditingMemberCantBanSelf'] = 'Je kan jezelf niet bannen.';
$lang['MembersEditingComplete'] = 'Account van lid %s werd succesvol bewerkt.';

$lang['DeleteMembersSearchMember'] = 'Lid zoeken';
$lang['DeleteMembersSearchMemberInfo'] = 'Voer een (deel van een) gebruikersnaam of weergegeven naam in om te verwijderen.';
$lang['DeleteMembersSearchMemberExplain'] = 'Gebruikersnaam of weergegeven naam';
$lang['DeleteMembersSearchMemberNotFound'] = 'Geen leden met gebruikersnaam of weergegeven naam %s gevonden.';
$lang['DeleteMembersSearchMemberList'] = 'De volgende leden werden gevonden';
$lang['DeleteMembersConfirmMemberDelete'] = 'Bevestigen van verwijderen lid';
$lang['DeleteMembersConfirmMemberDeleteContent'] = 'Ben je zeker dat je het lid %s wilt verwijderen? Dit is onomkeerbaar!';
$lang['DeleteMembersComplete'] = 'Lid %s werd succesvol verwijderd.';

$lang['RegisterMembersExplain'] = 'Hier kan je accounts voorregistreren. Vul de volgende informatie in om een account aan te maken.';
$lang['RegisterMembersComplete'] = 'Registratie van gebruiker %s voltooid. De gebruiker kan meteen inloggen.';

$lang['ActivateMembersExplain'] = 'Dit is een lijst van niet geactiveerde accounts op je forum. Hier kan je accounts manueel activeren. Een asterisk (*) duidt aan dat de account reeds gebruikt werd.';
$lang['ActivateMembersNoMembers'] = 'Geen leden.';
$lang['ActivateMembersListAdmin'] = 'Admin-activatie';
$lang['ActivateMembersListEmail'] = 'E-mailactivatie';
$lang['ActivateMembersListAll'] = 'Alle';

$lang['PruneForumsStart'] = 'Start prunen';
$lang['PruneForumsExplain'] = 'Door forums te prunen kunnen inactieve onderwerpen verplaatst of verwijderd en het forum schoon gehouden worden.';
$lang['PruneForumsForums'] = 'Te prunen forums';
$lang['PruneForumsAction'] = 'Actie';
$lang['PruneForumsActionLock'] = 'Sluiten';
$lang['PruneForumsActionMove'] = 'Verplaatsen';
$lang['PruneForumsActionDelete'] = 'Verwijderen';
$lang['PruneForumsMoveTo'] = 'Verplaatsen naar';
$lang['PruneForumsTopicAge'] = 'Onderwerpleeftijd';
$lang['PruneForumsTopicAgeField'] = 'Laatste bericht %s dagen geleden.';
$lang['PruneForumsMoveToForumSelectedForPruning'] = 'Het &quot;verplaatsen naar&quot;-forum kan niet voor pruning worden geselecteerd.';
$lang['PruneForumsConfirm'] = 'Bevestigen';
$lang['PruneForumsConfirmText'] = 'Ik begrijp dat deze actie onherroepelijk is.';
$lang['PruneForumsNotConfirmed'] = 'Je moet deze actie eerst bevestigen.';
$lang['PruneForumsDone'] = 'Het prunen is voltooid. %d onderwerpen werden gepruned.';
$lang['PruneForumsExcludeStickies'] = 'Sticky onderwerpen uitsluiten';

$lang['PruneMembersExplain'] = 'Door leden te prunen wordt het gebruikersbestand opgeschoond door niet-geactiveerde of inactieve accounts te verwijderen.';
$lang['PruneMembersTypeNeverActivated'] = 'Nooit geactiveerde leden';
$lang['PruneMembersRegisteredDaysAgo'] = 'Geregistreerd ten minste %s dagen geleden.';
$lang['PruneMembersTypeNeverPosted'] = 'Leden die nooit berichten plaatsten';
$lang['PruneMembersTypeInactive'] = 'Inactieve leden';
$lang['PruneMembersLastLoggedIn'] = 'De laatste maal ingelogd ten minste %s dagen geleden.';
$lang['PruneMembersExclude'] = 'Uitsluiten';
$lang['PruneMembersConfirmText'] = 'Ik begrijp dat deze actie onherroepelijk is.';
$lang['PruneMembersStart'] = 'Start prunen';
$lang['PruneMembersNotConfirmed'] = 'Je moet deze actie eerst bevestigen.';
$lang['PruneMembersDone'] = 'Het prunen is voltooid. %d leden werden gepruned.';

$lang['DNSBLIPBansDisabled'] = 'IP-adresbans uitgeschakeld';
$lang['DNSBLIPBansDisabledInfo'] = 'Om DNSBL-aangedreven banning te laten werken, moeten IP-adresbans ingeschakeld zijn.';
$lang['DNSBLGeneralInfo'] = 'Open proxies worden vaak gebruikt om spam of misbruikende berichten te plaatsen. Door het beschermingssysteem van UseBB kunnen veel van deze proxies opgespoord en geband worden. Hiervoor worden zogenaamde blacklists geraadpleegd voor informatie over het IP-adres van de gebruiker.';
$lang['DNSBLEnableOpenDNSBLBan'] = 'DNSBL-banning inschakelen';
$lang['DNSBLServers'] = 'DNS BlackList (DNSBL)-servers';
$lang['DNSBLServersInfo'] = 'Een DNSBL-hostnaam per regel. Merk op dat het gebruik van veel blacklists traagheid kan veroorzaken bij het starten van een nieuwe sessie.';
$lang['DNSBLMinPositiveHits'] = 'Ten minste %s positieve hits zijn benodigd om een IP-adres te bannen.';
$lang['DNSBLRecheckMinutes'] = 'Hercontroleer toegestane IP-adressen elke %s minuten (0 om uit te schakelen).';
$lang['DNSBLWhitelist'] = 'Whitelist';
$lang['DNSBLWhitelistInfo'] = 'Een IP-adres of hostnaam per regel (* en ? kunnen als wildcards worden gebruikt).';
$lang['DNSBLSettingsSaved'] = 'DNSBL-aangedreven banning instellingen werden opgeslaan.';

$lang['BadwordsInfo'] = 'Woorden kunnen gecensureerd of vervangen worden, eventueel d.m.v. wildcards (met *).';
$lang['BadwordsDisabled'] = 'Woordcensuur uitgeschakeld';
$lang['BadwordsDisabledInfo'] = 'Woordcensuur is uitgeschakeld in de forumconfiguratie.';
$lang['BadwordsNoBadwordsExist'] = 'Geen woordfilters bestaan reeds op dit forum.';
$lang['BadwordsAddBadwordWord'] = 'Woord';
$lang['BadwordsAddBadwordReplacement'] = 'Vervanging';

$lang['MassEmailInfo'] = 'Stuur een massa e-mailbericht naar alle leden of een niveaugroep.';
$lang['MassEmailRecipients'] = 'Bestemmelingen';
$lang['MassEmailRecipients-admins'] = 'Administratoren';
$lang['MassEmailRecipients-mods'] = 'Moderatoren';
$lang['MassEmailRecipients-members'] = 'Normale leden';
$lang['MassEmailSubject'] = 'Onderwerp';
$lang['MassEmailBody'] = 'Inhoud';
$lang['MassEmailTemplate'] = 'Hallo,

Dit is de forumsoftware van [board_name]. De administrator heeft een massa e-mailbericht verzonden. De inhoud volgt.

[board_name]
[board_link]
[admin_email]

-----

[body]';
$lang['MassEmailSent'] = 'Het e-mailbericht werd naar %d leden verzonden.';
$lang['MassEmailOptions'] = 'Opties';
$lang['MassEmailPublicEmailsOnly'] = 'Verstuur enkel naar publieke e-mailadressen.';
$lang['MassEmailExcludeBanned'] = 'Exclusief gebande leden.';

$lang['BansInfo'] = 'Hier kan je bijna alle banaspecten van je forum controleren. Gedeeltelijke selectie is mogelijk (met *). Individuele accounts kunnen geband worden bij het bewerken van leden.';
$lang['Bans-username'] = 'Gebruikersnamen';
$lang['Bans-email'] = 'E-mailadressen';
$lang['Bans-ip_addr'] = 'IP-adressen';
$lang['BansUsername'] = 'Gebruikersnaam';
$lang['BansEmail'] = 'E-mailadres';
$lang['BansIp_addr'] = 'IP-adres';
$lang['BansNoBansExist'] = 'Geen bans van dit type bestaan reeds op dit forum.';
$lang['BansIPBansDisabledInfo'] = 'IP-adresbans zijn uitgeschakeld in de forumconfiguratie.';

?>
