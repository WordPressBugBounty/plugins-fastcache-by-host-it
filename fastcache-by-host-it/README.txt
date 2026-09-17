=== Fastcache by Host.it ===
Contributors: Hostspa
Tags: cache, speed, seo, cdn, varnish
Requires at least: 6.0.0
Tested up to: 7.0
Requires PHP: 8.0
Stable Tag: 1.7.2
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt

FastCache è un plugin WordPress per caching avanzato, CDN e ottimizzazione delle prestazioni, sviluppato e supportato interamente in Italia.

== Description ==

FastCache è l’evoluzione del plugin FastCache 1.0.x sviluppato da HOST S.p.A. (Host.it).
La versione attuale introduce un **sistema completo di caching e ottimizzazione per WordPress**, progettato per migliorare le prestazioni del sito e ridurre in modo significativo il carico sull’infrastruttura.

Il plugin non si limita più alla sola integrazione CDN, ma include tutte le funzionalità tipiche di un moderno plugin di caching, integrandole in un unico strato software coerente e modulare.

### Full Page Caching

FastCache implementa un sistema di **full page caching nativo**, basato sulla generazione di versioni HTML statiche delle pagine.
Le pagine cache vengono servite direttamente dal web server tramite **reindirizzamento a livello di `.htaccess`**, evitando completamente:

- l’esecuzione di PHP,
- le query al database,
- il caricamento dello stack WordPress.

Questo approccio consente di ridurre drasticamente il consumo di CPU e RAM, liberando risorse di sistema per le operazioni che richiedono elaborazione dinamica, come:

- carrello e checkout,
- gestione degli ordini,
- moduli di invio dati,
- ricerche,
- processi applicativi critici.

Il risultato è una maggiore stabilità del sito e una migliore gestione dei picchi di traffico.

### CDN e gestione della cache

FastCache integra una **CDN proprietaria** gestita da HOST S.p.A.
La comunicazione con la CDN avviene tramite un token di autenticazione univoco per ciascun sito, che consente:

- gestione dinamica dei TTL,
- purge selettivo dei contenuti,
- sincronizzazione automatica tra cache locale e cache CDN.

Quando un contenuto viene modificato, FastCache invalida automaticamente:
- la pagina aggiornata,
- la homepage,
- le categorie correlate,
- archivi, feed RSS e sitemap associati.

Questo meccanismo consente di mantenere TTL ridotti garantendo contenuti sempre aggiornati senza interventi manuali.

### Ottimizzazione delle risorse

Il plugin include strumenti avanzati per:

- combinazione e minificazione di CSS e JavaScript,
- minificazione HTML,
- lazy load di immagini, iframe ed elementi HTML,
- ottimizzazione dinamica delle immagini con conversione WebP e AVIF,
- gestione automatica di `srcset`,
- preload delle risorse tramite HTTP/2.

Le funzionalità sono configurabili e adattabili sia a siti semplici sia a installazioni complesse, inclusi e-commerce basati su WooCommerce.

### Analisi e diagnostica

FastCache integra:
- test di performance basati su Google PageSpeed Insights e Lighthouse,
- strumenti di diagnostica dell’ambiente di hosting,
- rilevamento di conflitti con altri plugin di caching o ottimizzazione.

Queste funzioni permettono di verificare la correttezza della configurazione e la compatibilità dell’ambiente.

### Prodotto italiano

FastCache è un prodotto **100% italiano**:
- sviluppo del plugin in Italia,
- servizi CDN basati su server italiani,
- infrastruttura gestita ed erogata interamente in Italia,
- documentazione e supporto tecnico in lingua italiana.

Documentazione ufficiale:
https://assistenza.host.it/area-tecnica/fastcache/pluginwordpress

### Compatibilità

FastCache è progettato per operare come **unico plugin di caching**.
È fortemente consigliato disattivare altri plugin di caching per evitare sovrapposizioni o conflitti.

Il plugin include inoltre impostazioni dedicate per WooCommerce, che consentono di mantenere cacheabili le pagine compatibili senza compromettere il corretto funzionamento del carrello e delle sessioni utente.

### Supporto WordPress Multisite

A partire dalla versione 1.7.0, FastCache supporta ufficialmente **WordPress Multisite**, sia in modalità **subdirectory** (`tuosito.it/sito2/`) che **subdomain** (`sito2.tuosito.it`).

Il plugin si attiva **una sola volta a livello di rete** (Network Admin → Plugin → Attiva network), rendendolo disponibile su tutti i subsite. Ogni subsite configura poi le proprie impostazioni FastCache in autonomia dal proprio pannello di amministrazione — esattamente come su un'installazione singola: token CDN, esclusioni cache, cache specifica per piattaforma mobile/desktop e tutte le altre opzioni sono indipendenti tra un subsite e l'altro. Non è richiesta né prevista, in questa versione, una configurazione centralizzata a livello di rete.

Cosa viene isolato automaticamente per ciascun subsite:

- cache di pagina, con directory dedicate per ogni sito,
- cache immagini ottimizzate, anche con l'algoritmo di hashing disattivato,
- cache oggetti,
- pulizia programmata della cache scaduta (cron),
- purge e comunicazione con la CDN, tramite il token proprio di ciascun subsite.

**Modalità subdirectory**: la cache di pagina beneficia del bypass Apache completo tramite `.htaccess`, esattamente come su installazione singola — le pagine vengono servite come file statici senza mai eseguire WordPress, con isolamento del percorso per ciascun subsite.

**Modalità subdomain**: poiché Apache non può distinguere i singoli sottodomini leggendo solo il percorso della richiesta, il bypass diretto via `.htaccess` non è disponibile; la cache di pagina resta comunque pienamente attiva e isolata per dominio, servita da un livello di sicurezza a livello PHP con overhead minimo rispetto al bypass Apache puro.

*Nota per chi gestisce più subsite in modalità subdirectory con impostazioni di cache htaccess differenti tra loro (es. cache mobile/desktop attiva solo su alcuni siti): la generazione del file `.htaccess` condiviso a livello di rete può temporaneamente riflettere le impostazioni dell'ultimo subsite salvato anziché quelle specifiche di ciascuno. Un allineamento più granulare per-subsite è pianificato per una release successiva.*

== Installation ==
È consigliato utilizzare l’installer di default di WordPress.
In alternativa:

- Upload del pacchetto fastcache (.zip) in `/wp-content/plugins/`
- Decompressione dell’archivio
- Impostazione dei permessi secondo le best practice WordPress
- Attivazione del plugin dal pannello “Plugin”

== Frequently Asked Questions ==

Q: Come posso ottenere un token di autenticazione per FastCache?
R: Il token viene generato all’interno dell’area clienti su https://host.it durante l’attivazione del servizio CDN.

Q: Cosa succede se un server CDN va offline?
R: Le richieste vengono automaticamente instradate verso nodi disponibili, senza interruzioni del servizio.

Q: FastCache è compatibile con altri plugin di caching?
R: È fortemente consigliato rimuovere altri plugin di caching.

== Screenshots ==
1. Cache di pagina
2. Cache CDN
3. Configurazione Automatica
4. Ottimizzazioni
5. Inclusione Risorse
6. Esclusione Risorse
7. Lazy Load
8. Ottimizza immagini
9. Gestione Risorse
10. Http/2
11. Combina Immagini
12. Avanzate
13. Speed Test
14. Diagnostica
15. Menu

== Changelog ==
1.7.2
Risolto un bug per cui un visitatore con articoli nel carrello WooCommerce poteva ricevere la stessa cache di pagina statica condivisa generata per un visitatore ospite con carrello vuoto, mostrando il mini-carrello (es. widget ShopEngine) sempre vuoto e non cliccabile su pagine cacheate come home o catalogo, finché la cache non veniva svuotata manualmente. L'opzione "Escludi cache pagina per cookie" ora include di default anche i cookie di sessione WooCommerce (woocommerce_items_in_cart, woocommerce_cart_hash, wp_woocommerce_session_), oltre a cmplz_, così un visitatore con sessione carrello attiva riceve sempre una pagina generata al momento invece della cache condivisa (Bug#34406).
Chiarito il funzionamento dell'opzione "Cache lifetime" (durata cache file CSS/JS combinati): un valore basso qui non influisce solo sui file combinati, ma può far scattare più di frequente anche la pulizia interna della cache di pagina, indipendentemente dal valore impostato in "Page cache lifetime". Aggiunto un avviso esplicito nel pannello impostazioni sotto il campo, e allineato il valore preselezionato di default da 5 minuti a 1 giorno, coerente con "Page cache lifetime", per evitare configurazioni rischiose su siti ad alto traffico non scelte deliberatamente.

1.7.1
Aggiunta la gestione automatica della compatibilità con Divi ed Elegant Themes: quando FastCache è attivo, la generazione dei file CSS statici di Divi viene disabilitata per evitare che le pagine HTML in cache continuino a referenziare risorse CSS eliminate o rigenerate separatamente dal tema. Il controllo viene registrato soltanto quando è configurato il tema Divi, anche tramite child theme, oppure il plugin Divi Builder; viene eseguito in contesto amministrativo autorizzato e invalida in modo sicuro la cache pagina interessata. Aggiunte protezioni per multisite, purge CDN, errori del filesystem e tentativi ripetuti. Corretti i deprecated PHP 8.5 che potevano produrre un avviso durante la chiusura dell'output buffer di ottimizzazione.

1.7.0
Supporto ufficiale WordPress Multisite (subdirectory mode). La cache di pagina è ora isolata per subsite tramite directory dedicate per blog_id, così come la cache immagini ottimizzate (hash_images_algo=none), la cache oggetti e la pulizia programmata (cron prune). Ogni subsite configura il proprio token CDN dal proprio pannello impostazioni FastCache, come su installazione singola; il purge CDN utilizza gli URL generati dalle funzioni WordPress, già isolati per subsite. In subdomain mode le regole Apache non vengono generate (Apache non può distinguere i siti dal solo REQUEST_URI), ma la cache di pagina resta attiva: i file vengono generati nello stesso formato .html della cache htaccess standard e serviti a ogni richiesta dal livello di sicurezza PHP, correttamente isolati per dominio tramite blog_id. La gestione centralizzata a livello di rete (Network Admin) è prevista per una release successiva.
Aggiunta l'opzione "Escludi cache pagina per cookie" per bypassare la cache in presenza di specifici cookie (es. cmplz_ di Complianz), in modo che i segnali di consenso GDPR/TCF vengano sempre inoltrati correttamente ai network pubblicitari; gli script di Complianz/TCF sono inoltre esclusi dalla combinazione/differimento JS per non comprometterne il funzionamento (Ticket#71449869).
Risolto un bug per cui FastCache poteva salvare in cache pagine di errore (es. blocco 403 di Wordfence, pagine 404) e servirle staticamente al posto del contenuto corretto: ora vengono cachate solo le risposte con status HTTP 200 (Bug#34270).
Introdotto il supporto alla cache separata per dispositivi mobile e desktop nella modalità htaccess (opzione "Cache specifica della piattaforma"): in precedenza il primo visitatore generava un unico file .html statico che Apache serviva a tutti indipendentemente dal dispositivo, con il risultato che gli utenti desktop potevano ricevere il layout mobile e viceversa. Ora le regole htaccess rilevano il tipo di dispositivo tramite User-Agent e servono file distinti (_mobile.html per mobile, _.html per desktop), garantendo che ogni visitatore riceva sempre la versione corretta della pagina. Quando l'opzione è attiva, FastCache invia inoltre l'header Vary: User-Agent affinché anche la CDN distingua correttamente le due varianti invece di servire la prima versione ricevuta a tutti i dispositivi; l'header non viene inviato sui siti che non usano questa opzione, per non frammentare inutilmente la cache CDN (Bug#34278, Ticket#71450934).
Risolto un bug per cui i file CSS e JavaScript combinati da FastCache venivano referenziati con URL in http:// anche su siti HTTPS, causando errori Mixed Content nei browser. Il problema si presentava su installazioni dietro CDN o reverse proxy che terminano TLS prima del server web, in quanto WordPress non rilevava correttamente la connessione sicura e generava URL con schema errato; tali URL venivano poi scritti nei file .html statici e permanevano fino allo svuotamento manuale della cache. Risolto normalizzando lo schema dell'URL in fase di generazione della cache, in modo che sia sempre coerente con lo schema reale della richiesta (Bug#34278, Ticket#71450934).

1.6.19
Corretti errori fatali (HTTP 500) su WordPress Multisite con FastCache attivato a livello di rete. Rimossa la richiesta prematura di wp-includes/pluggable.php in Main.php, che su alcune configurazioni multisite avveniva prima che WordPress definisse le costanti dei cookie di autenticazione (AUTH_COOKIE, SECURE_AUTH_COOKIE), causando errore fatale nelle pagine di amministrazione. Corretto inoltre un possibile conflitto tra la libreria psr/log inclusa nel plugin (v1) e versioni più recenti richieste da altri plugin (es. Monolog 3, che richiede psr/log v3): l'autoloader di FastCache ora evita di sovrascrivere un'interfaccia Psr\Log\LoggerInterface già caricata da un altro plugin e non ha più priorità forzata (prepend) nella catena di autoload, riducendo il rischio di conflitti di caricamento classi tra plugin su installazioni multisite.

1.6.18
Compatibilità Divi Visual Builder: aggiunto bypass completo di FastCache per le sessioni del Divi Builder (parametri ?et_fb=1 e ?et_pb_preview). Nelle richieste con questi parametri vengono disabilitati sia il combiner JS/CSS che la page cache, evitando che i bundle consolidati rompano la sequenza di inizializzazione di Divi e blocchino il caricamento del Visual Builder.

1.6.17
Correzione selettore nei pulsanti CDN Purge All e CDN Purge Homepage nella admin bar: il binding del click veniva applicato a tutte le voci del menu FastCache (incluse Impostazioni e Local Cache Purge All) invece che alle sole voci di purge CDN. Il selettore è stato ristretto per intercettare esclusivamente i pulsanti CDN corretti.

1.6.16
Correzione bug nei pulsanti di purge manuale CDN nella admin bar (CDN Purge All, CDN Purge Homepage): il selettore jQuery faceva riferimento a un ID obsoleto (#wp-admin-bar-hst-purge-cache-default) non più presente nel DOM, rendendo i pulsanti completamente non funzionanti. Aggiornato il selettore al valore corretto (#wp-admin-bar-fastcache-main-default). Sostituito jQuery(window).load() deprecato con jQuery(document).ready() in tutti i file JS del plugin.
Ricerca delle esclusioni JS/CSS ora case-insensitive: il metodo findExcludes() utilizzava strpos() (case-sensitive), impedendo il match di nomi file con capitalizzazione diversa (es. ajax.js, Ajax.js, AJAX.js). Sostituito con stripos() per garantire l'esclusione indipendentemente dalla capitalizzazione.

1.6.15
Correzione bug per purge della cache in home page: il purge automatico della cache filesystem (file .html) non veniva mai eseguito alla pubblicazione di un articolo. Il metodo canPurge() veniva chiamato nel costruttore di HAdmin prima dell'hook init di WordPress, impostando purgeable=false come misura di sicurezza. Le chiamate successive al momento del purge reale (publish_post, save_post) non reimpostavano mai purgeable=true in caso di successo, bloccando silenziosamente tutta la purge della cache di pagina inclusa la home page. Particolarmente impattante sui siti di news dove la home page deve aggiornarsi immediatamente alla pubblicazione di ogni nuovo articolo. Fix: aggiunto il ramo else { purgeable=true } nel percorso di successo di canPurge().

1.6.12
Migliorata la compatibilità con WordPress Multisite: il plugin ora si carica correttamente anche su installazioni multisite, disabilitando automaticamente le funzionalità incompatibili (cache di pagina .htaccess e purge CDN) e mantenendo operative le ottimizzazioni CSS/JS, lazy load e immagini. La guard bloccante precedente è stata sostituita con un avviso informativo e l'inibizione selettiva delle feature non compatibili.

1.6.11
Correzione bug critico nella purge CDN: durante il salvataggio di un post, il plugin andava in fatal error "Cannot use object of type WP_Error as array" quando la richiesta HTTP di purge falliva. Il controllo is_wp_error() esisteva ma il log successivo veniva eseguito ugualmente. Il bug era presente in due punti (metodo regexp ed exact). Correzione bug Class "FastCache\Platform\Plugin" not found in NonceRefresh.php quando processCacheContent() viene chiamata da un contesto esterno al plugin: aggiunto controllo class_exists() con fallback diretto a get_option().

1.6.10
Correzione bug critico nella cache .htaccess: le pagine interne mostravano la home page quando la URL della home veniva visitata tramite index.php. Il CacheId veniva generato come _index.php_.html invece di _.html, causando un match errato nelle regole htaccess per le pagine interne del tipo /2026/04/20/post-name/. La URI viene ora normalizzata rimuovendo index.php prima della generazione del CacheId.

1.6.9
Introdotta la funzionalità Late Nonce Refresh per prevenire errori di “verifica nonce non riuscita” quando le pagine vengono cachate più a lungo della durata del nonce di WordPress (12 ore). I nonce nelle pagine cachate vengono ora sostituiti con placeholder durante il caching e aggiornati automaticamente tramite REST API prima delle richieste AJAX. Supporta sia il caching statico .htaccess che il caching di pagina PHP. Nuova impostazione admin per abilitare/disabilitare la funzionalità. Completamente compatibile con form AJAX, WooCommerce e handler di form personalizzati.

1.6.8
Miglioramenti della purge della cache per post programmati e cron job, con una migliore gestione dell'invalidazione della cache e della gestione delle risorse

1.6.7
Miglioramenti all'efficienza e all'affidabilità dell'archiviazione del file system della cache degli oggetti
Correzione per cancellare i file cache

1.6.6
Corretti i problemi di purge automatica nei post programmati, alcuni miglioramenti nella cache degli oggetti

1.6.4
Correzione minore per un avviso PHP

1.6.3
Aggiunte alias di compatibilità `wp_cache_incr()` e `wp_cache_decr()` nel drop-in object-cache per prevenire errori fatali con gli aggiornamenti di ordini/prodotti WooCommerce.
Aggiunta dichiarazione `wp_cache_supports()` nel drop-in per segnalare esplicitamente le funzionalità della cache supportate.

1.6.1/1.6.2
Importanti miglioramenti al caching del file system nella cache degli oggetti.
Ora la garbage collection è limitata nel tempo per evitare timeout su siti di grandi dimensioni.

1.6
Introdotto il supporto Object Caching in FastCache per migliorare significativamente le prestazioni di WordPress e ridurre il carico del database. La nuova funzionalità si integra perfettamente sia con Redis che con Memcached, abilitando risposte di query più veloci e un utilizzo più efficiente delle risorse. Questa versione include anche numerose correzioni di bug e miglioramenti della stabilità nel layer di caching. Ideale per siti ad alto traffico, negozi WooCommerce e piattaforme con contenuti dinamici che cercano di ottimizzare velocità e scalabilità. Completamente compatibile con ambienti di hosting moderni e facile da configurare direttamente dall'interfaccia del plugin.

1.5.20
Miglioramenti agli Header di risposta per ispezionare lo stato della cache per Full Page Caching quando si usa .htaccess

1.5.19
Correzione per il trucco svn
Importanti miglioramenti al caching del file system.
Tutte le cache del file system ora funzionano in modalità auto-prune.
Quando un post viene creato, aggiornato, cestinato, eliminato, o quando qualsiasi evento ne cambia lo stato, tutte le pagine correlate nell'ecosistema WordPress vengono automaticamente rimosse dalla cache del file system.
Full Page Caching è ora gestito tramite un job cron orario. Durante ogni esecuzione, tutte le versioni della cache statica vengono automaticamente eliminate secondo il ritardo definito nell'impostazione “TTL aging of cache”.
Un nuovo widget dashboard è stato aggiunto per fornire accesso rapido alle informazioni di base e alle funzioni principali, inclusi i controlli di attivazione/disattivazione per:
	Cache PHP
	Cache a Pagina Intera
	CDN
con scorciatoie di purge incorporate.

1.5.18
Importanti miglioramenti al caching del file system.
Tutte le cache del file system ora funzionano in modalità auto-prune.
Quando un post viene creato, aggiornato, cestinato, eliminato, o quando qualsiasi evento ne cambia lo stato, tutte le pagine correlate nell'ecosistema WordPress vengono automaticamente rimosse dalla cache del file system.
Full Page Caching è ora gestito tramite un job cron orario. Durante ogni esecuzione, tutte le versioni della cache statica vengono automaticamente eliminate secondo il ritardo definito nell'impostazione “TTL aging of cache”.
Un nuovo widget dashboard è stato aggiunto per fornire accesso rapido alle informazioni di base e alle funzioni principali, inclusi i controlli di attivazione/disattivazione per:
	Cache PHP
	Cache a Pagina Intera
	CDN
con scorciatoie di purge incorporate.

1.5.17
Purge della cache tramite cron job. Timing cron: ogni ora, ttl: 1 giorno

1.5.16
Correzione per ripristinare l'htaccess originale quando il plugin viene disattivato
Cache di pagina e cache htaccess abilitate per impostazione predefinita per 1 giorno per consentire al plugin di funzionare immediatamente dopo l'attivazione

1.5.15
Nuova funzionalità per l'importazione e l'esportazione della configurazione
Correzione per le regole .htaccess duplicate

1.5.14
Correzione per consentire i download della configurazione

1.5.13
Miglioramenti della sicurezza

1.5.12
Correzione CSS nella distribuzione 1.5.11

1.5.11
Controlli per 6.9.1
Diverse correzioni e miglioramenti
Nuova strategia sulle regole di caching .htaccess, davvero molto più efficace ed efficiente. Super preciso per escludere POST e altri elementi come le stringhe di query dal caching di pagina.
Un solo menu nella barra admin per amministrare ogni purge e accesso alle opzioni.
Nuova logica nei concetti di workaround.
Hook attivati per la gestione della licenza (FastCache sarà gratuito per sempre ma è necessario monitorare la release per fare debugging).

1.5.10
Correzione per l'esclusione URL

1.5.9
Correzione di alcuni avvisi PHP

1.5.8
Correzione per il ttl predefinito

1.5.7
Correzione per YithWishList

1.5.6
Configurazione errata

1.5.5
Correzione urgente per risolvere il conflitto CDN <=> FileSystem Caching

1.5.4
Modifica dei parametri predefiniti per la combinazione e la minificazione di CSS e JS

1.5.3
Alcune correzioni minori, impostazione del TTL predefinito a 300 sec a causa del metodo di caching ttl e grace

1.5.2
Readme e screenshot

1.5.1
Correzione minore

1.5.0
Nuova versione con caching del file system

1.2.30
Correzione della sicurezza

1.2.29
Correzione della sicurezza

1.2.27
Correzione per la misconfiguration vsc

1.2.26
Correzione per la misconfiguration vsc

1.2.25
Correzione per l'errore nei log di debug

1.2.24
Correzione per l'errore nei log di debug

1.2.23
Correzione per l'errore nei log di debug

1.2.22
Correzione nel repository svn

1.2.21
Correzione minore nella pagina admin

1.2.20
Correzione per la compatibilità con le best practice dei plugin WordPress
Aggiunta esclusione URL
Aggiunto workaround per minicart woocommerce

1.2.12
I link di feed e rss sono gestiti con ttl da 60 secondi

1.2.0
Correzione per la compatibilità con le best practice dei plugin WordPress
Pubblicazione nel repository dei plugin WordPress
Correzione della posizione della cartella log in wp-content/logs
Correzione della purge della cache sulla pubblicazione di post programmati e tutti gli aggiornamenti cron

1.0.8
Correzione per la compatibilità con le best practice dei plugin WordPress
Creazione di due semplici workaround per Yith Woocommerce Wishlist e il tema Storefront (compatibilità ESI)

1.0.7
Primi workaround per yith woocommerce wishlist con esi

1.0.6
Correzione dei problemi di compatibilità con il controllo dei plugin.

1.0.5
Molte correzioni per la compatibilità con le impostazioni php.ini relative a error_reporting

1.0.4
Correzione per il controllo dei nonce
Correzione per la denominazione dei file
Correzione per la purge della cache
Avvisi gradevoli nelle pagine frontend e admin
Implementazione di Log Traces
Abilitazione di Debug Panel
Checker IP nel Debug Panel

1.0.1

Versione stable
Some fix about file naming and nonces checks

1.0

Versione stable
TTl per post type
Purge cache all
Purge cache post
Purge cache page
Purge cache url
Purge cache on save / update / delete / comment / pingback / trackback / publish

0.5

Internal BETA

== Upgrade Notice ==
Documentazione: https://assistenza.host.it/area-tecnica/fastcache/pluginwordpress
Guida all'installazione: https://assistenza.host.it/area-tecnica/fastcache/pluginwordpress
FAQ: https://assistenza.host.it/area-tecnica/fastcache/pluginwordpress
Supporto https://host.it/supporto.
Note: FastCache è un plugin sviluppato da HOST S.p.a. (Host.it).
