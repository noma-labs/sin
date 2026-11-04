<?php

declare(strict_types=1);

namespace App\Biblioteca;

/**
 * Argomenti delle colonne della biblioteca, ricavati dalla classificazione e dai titoli dei libri
 * (export del catalogo del 4 novembre 2026). Gli argomenti degli elaborati SP sono ricavati dalle note.
 */
final class ArgomentiBiblioteca
{
    /**
     * Aree tematiche usate per colorare la piantina.
     *
     * @var array<string, string>
     */
    public const AREE = [
        'romanzi' => 'Romanzi',
        'gialli' => 'Gialli',
        'classici' => 'Classici, teatro e poesia',
        'ragazzi' => 'Ragazzi e bambini',
        'religione' => 'Religione e spiritualità',
        'storia' => 'Storia',
        'geografia' => 'Geografia e viaggi',
        'scienze' => 'Scienze e medicina',
        'arte' => 'Arte e musica',
        'enciclopedie' => 'Enciclopedie e dizionari',
        'societa' => 'Società e pensiero',
        'tempo-libero' => 'Tempo libero e vita pratica',
        'scuola' => 'Scuola di Nomadelfia',
        'video' => 'Video, DVD e cassette',
    ];

    /**
     * Argomento di ogni colonna presente sulla piantina, indicizzato per codice colonna.
     *
     * @var array<string, array{area: string, argomento: string, dettaglio: string}>
     */
    public const COLONNE = [
        'BA' => ['area' => 'scienze', 'argomento' => 'Medicina e salute', 'dettaglio' => 'A–F guide mediche, maternità · G psicologia'],
        'BB' => ['area' => 'enciclopedie', 'argomento' => 'Enciclopedie e medicina', 'dettaglio' => 'E enciclopedie · F–G Medicina viva'],
        'BC' => ['area' => 'enciclopedie', 'argomento' => 'Enciclopedie generali', 'dettaglio' => 'E Garzanti · F Pomba, Conoscere l\'Italia · G grandi opere'],
        'BD' => ['area' => 'enciclopedie', 'argomento' => 'Enciclopedie tematiche', 'dettaglio' => 'A Capolavori della musica · B, D–F enciclopedie · C scienze naturali'],
        'BE' => ['area' => 'arte', 'argomento' => 'Arte e musei', 'dettaglio' => 'Pittura italiana, musei, tesori d\'Italia'],
        'BF' => ['area' => 'enciclopedie', 'argomento' => 'Enciclopedie e grandi opere', 'dettaglio' => 'A arte · B–F enciclopedie internazionali, Protagonisti'],
        'BG' => ['area' => 'arte', 'argomento' => 'Pittori · Mondo degli animali', 'dettaglio' => 'A grandi pittori · B–C Il mondo degli animali'],
        'BH' => ['area' => 'arte', 'argomento' => 'Arte e enciclopedie', 'dettaglio' => 'A–B arte sacra e pittura · C Sacra Bibbia · D collana Elite · E–F Grolier'],
        'BI' => ['area' => 'arte', 'argomento' => 'Monografie di pittori', 'dettaglio' => 'I classici dell\'arte, I maestri del colore'],
        'BL' => ['area' => 'classici', 'argomento' => 'Letteratura straniera e annuari', 'dettaglio' => 'D classici stranieri · E I Quindici · F Di anno in anno'],
        'BM' => ['area' => 'enciclopedie', 'argomento' => 'Enciclopedie per ragazzi', 'dettaglio' => 'Enciclopedia dei maestri, Conoscere'],
        'BN' => ['area' => 'religione', 'argomento' => 'Religioni e Bibbia', 'dettaglio' => 'A religioni del mondo · B cristianesimo · C–E Bibbia · F Concilio Vaticano II'],
        'BO' => ['area' => 'religione', 'argomento' => 'Bibbia e storia della Chiesa', 'dettaglio' => 'A–F Bibbia, storia della Chiesa · G Bibliotheca Sanctorum'],
        'SPC' => ['area' => 'scuola', 'argomento' => 'Elaborati: viaggi e gite di studio', 'dettaglio' => 'viaggi e gite di studio 79% · scienze e natura 9% · vangelo e fede 5%'],
        'SPE' => ['area' => 'scuola', 'argomento' => 'Elaborati misti: fascicoli personali ed esami, aziende, laboratori e mestieri', 'dettaglio' => 'fascicoli personali ed esami 36% · aziende, laboratori e mestieri 24% · viaggi e gite di studio 15%'],
        'SPF' => ['area' => 'scuola', 'argomento' => 'Elaborati: fascicoli personali ed esami', 'dettaglio' => 'fascicoli personali ed esami 98% · viaggi e gite di studio 2%'],
        'SPG' => ['area' => 'scuola', 'argomento' => 'Elaborati misti: arte, poesia e letture, vangelo e fede', 'dettaglio' => 'arte, poesia e letture 38% · vangelo e fede 29% · fascicoli personali ed esami 7%'],
        'SPH' => ['area' => 'scuola', 'argomento' => 'Elaborati misti: arte, poesia e letture, vangelo e fede', 'dettaglio' => 'arte, poesia e letture 33% · vangelo e fede 16% · vita di nomadelfia 14%'],
        'AE' => ['area' => 'storia', 'argomento' => 'Storia contemporanea', 'dettaglio' => 'D–G fascismo, politica, comunismo · H–M Storia universale'],
        'AD' => ['area' => 'storia', 'argomento' => 'Storia antica e moderna', 'dettaglio' => 'Civiltà antiche, Europa, storia d\'Italia'],
        'AC' => ['area' => 'storia', 'argomento' => 'Storia e scienze', 'dettaglio' => 'A–D storia · E–G collane di scienze naturali'],
        'AB' => ['area' => 'scienze', 'argomento' => 'Scienze naturali divulgative', 'dettaglio' => 'Animali, piante, astronomia, tecnica'],
        'AA' => ['area' => 'geografia', 'argomento' => 'Viaggi nel mondo', 'dettaglio' => 'A–D viaggi e paesi · E–G scienze divulgative'],
        'SPR' => ['area' => 'scuola', 'argomento' => 'Elaborati misti: vita di nomadelfia, vangelo e fede', 'dettaglio' => 'vita di nomadelfia 25% · vangelo e fede 25% · scienze e natura 12%'],
        'SPB' => ['area' => 'scuola', 'argomento' => 'Elaborati: viaggi e gite di studio', 'dettaglio' => 'viaggi e gite di studio 87% · apostolato e serate 9% · vangelo e fede 2%'],
        'Z-VHS' => ['area' => 'video', 'argomento' => 'Videocassette VHS', 'dettaglio' => 'Sigle AM ambiente, IT film, CU corpo umano, corsi di lingua…'],
        'SPA' => ['area' => 'scuola', 'argomento' => 'Elaborati: apostolato e serate', 'dettaglio' => 'apostolato e serate 94% · viaggi e gite di studio 6%'],
        'SPS' => ['area' => 'scuola', 'argomento' => 'Elaborati misti: arte, poesia e letture, viaggi e gite di studio', 'dettaglio' => 'arte, poesia e letture 45% · viaggi e gite di studio 18% · vita di nomadelfia 9%'],
        'CA' => ['area' => 'romanzi', 'argomento' => 'Romanzi', 'dettaglio' => 'A–F romanzi · G biografie (La vita di…) · H opere complete'],
        'CB' => ['area' => 'romanzi', 'argomento' => 'Opere complete', 'dettaglio' => 'Camus, Tolstoj, Hawthorne, Chaucer'],
        'CC' => ['area' => 'romanzi', 'argomento' => 'Romanzi e opere complete', 'dettaglio' => 'F romanzi del \'900 · G teatro e romanzi russi · H novelle e poemi'],
        'CD' => ['area' => 'romanzi', 'argomento' => 'Romanzi classici e moderni', 'dettaglio' => 'Dostoevskij, Defoe, Fogazzaro, Chesterton'],
        'CE' => ['area' => 'romanzi', 'argomento' => 'Romanzi', 'dettaglio' => 'A–F romanzi · G–H classici (Divina Commedia, Orlando Furioso)'],
        'SPI' => ['area' => 'scuola', 'argomento' => 'Elaborati: scienze e natura', 'dettaglio' => 'scienze e natura 84% · vangelo e fede 8% · arte, poesia e letture 4%'],
        'SPN' => ['area' => 'scuola', 'argomento' => 'Elaborati misti: viaggi e gite di studio, scienze e natura', 'dettaglio' => 'viaggi e gite di studio 47% · scienze e natura 15% · vangelo e fede 9%'],
        'SPP' => ['area' => 'scuola', 'argomento' => 'Elaborati misti: viaggi e gite di studio, scienze e natura', 'dettaglio' => 'viaggi e gite di studio 25% · scienze e natura 19% · vangelo e fede 18%'],
        'Z-DVD' => ['area' => 'video', 'argomento' => 'DVD e CD', 'dettaglio' => 'Sigle che iniziano per Z (ZS storia, ZL inglese, ZC musica…)'],
        'SPM' => ['area' => 'scuola', 'argomento' => 'Elaborati: storia e società', 'dettaglio' => 'storia e società 65% · vangelo e fede 18% · geografia e paesi 9%'],
        'SPL' => ['area' => 'scuola', 'argomento' => 'Elaborati misti: vita di nomadelfia, vangelo e fede', 'dettaglio' => 'vita di nomadelfia 51% · vangelo e fede 9% · aziende, laboratori e mestieri 9%'],
        'DN' => ['area' => 'romanzi', 'argomento' => 'Romanzi', 'dettaglio' => 'A–H romanzi · I narrativa italiana · L Enciclopedia Motta'],
        'DM' => ['area' => 'romanzi', 'argomento' => 'Romanzi d\'avventura', 'dettaglio' => 'A–I romanzi e avventura (Salgari, Ivanhoe) · L enciclopedia per ricerche'],
        'DL' => ['area' => 'romanzi', 'argomento' => 'Romanzi e novelle', 'dettaglio' => 'H romanzi · I novelle straniere · L Enciclopedia Motta, melodramma'],
        'DI' => ['area' => 'romanzi', 'argomento' => 'Romanzi e letteratura straniera', 'dettaglio' => 'A letteratura straniera · B–H romanzi · I diari · L Bibbia, vocabolario'],
        'DH' => ['area' => 'romanzi', 'argomento' => 'Romanzi e letteratura straniera', 'dettaglio' => 'A letteratura straniera · B–H romanzi · I enciclopedia della cucina · L storia della Chiesa'],
        'DG' => ['area' => 'romanzi', 'argomento' => 'Romanzi e letteratura straniera', 'dettaglio' => 'A letteratura straniera · B–I romanzi · L Storia d\'Italia'],
        'DF' => ['area' => 'geografia', 'argomento' => 'Racconti · guide d\'Italia', 'dettaglio' => 'H racconti · I–L Tuttitalia, province e abbazie'],
        'DE' => ['area' => 'romanzi', 'argomento' => 'Romanzi · Maremma', 'dettaglio' => 'A–H romanzi · I Maremma e storia locale · L Il Milione'],
        'DD' => ['area' => 'romanzi', 'argomento' => 'Romanzi · regioni d\'Italia', 'dettaglio' => 'A–H romanzi e storie vere · I–L regioni d\'Italia e continenti'],
        'DC' => ['area' => 'geografia', 'argomento' => 'Giro del mondo', 'dettaglio' => 'Le vie del mondo, regioni d\'Italia, Stati Uniti'],
        'DB' => ['area' => 'geografia', 'argomento' => 'Paesi del mondo', 'dettaglio' => 'Guide, Popoli costumi paesi, atlanti'],
        'DA' => ['area' => 'geografia', 'argomento' => 'Toscana, Maremma e Italia', 'dettaglio' => 'A–G Toscana e Maremma · H città · I atlanti'],
        'FA' => ['area' => 'gialli', 'argomento' => 'Gialli', 'dettaglio' => 'A–F gialli (Christie, Simenon) · H fiabe e fumetti'],
        'FB' => ['area' => 'gialli', 'argomento' => 'Gialli', 'dettaglio' => 'Poirot, Charlie Chan, collane gialle'],
        'GB' => ['area' => 'religione', 'argomento' => 'Spiritualità · Nomadelfia', 'dettaglio' => 'A Enciclopedia cattolica · B Nomadelfia e Don Zeno · C–H spiritualità · I papi'],
        'GA' => ['area' => 'religione', 'argomento' => 'Spiritualità', 'dettaglio' => 'Teologia, preghiera, vita cristiana'],
        'FD' => ['area' => 'religione', 'argomento' => 'Santi e beati', 'dettaglio' => 'Vite dei santi · E Madonna e dottrina · F San Francesco'],
        'FC' => ['area' => 'religione', 'argomento' => 'Testimoni della fede', 'dettaglio' => 'A santi e venerabili · B–G testimoni, missionari · D interviste · H Enciclopedia dei santi'],
        'GC' => ['area' => 'religione', 'argomento' => 'Spiritualità e Magistero', 'dettaglio' => 'A–G spiritualità · H–I Papi, encicliche, discorsi'],
        'HA' => ['area' => 'societa', 'argomento' => 'Filosofia e psicologia', 'dettaglio' => 'A–B filosofia · C psicologia · D–I enciclopedie · L arte moderna'],
        'HB' => ['area' => 'arte', 'argomento' => 'Arte', 'dettaglio' => 'Pittura, architettura, musei · D collana di monografie (430 vol.)'],
        'HC' => ['area' => 'tempo-libero', 'argomento' => 'Cinema e lavori manuali', 'dettaglio' => 'D cinema · E ecologia · F lavori manuali · G arte del vetro'],
        'HD' => ['area' => 'enciclopedie', 'argomento' => 'Enciclopedie tecniche', 'dettaglio' => 'Informatica, scienza e tecnica, minerali, animali, agricoltura'],
        'HE' => ['area' => 'tempo-libero', 'argomento' => 'Tempo libero e natura', 'dettaglio' => 'A fotografia · B–C piante e agricoltura · D cucina · E giochi · F sport · G animali · H fai da te'],
        'HG' => ['area' => 'societa', 'argomento' => 'Problemi sociali', 'dettaglio' => 'A musica e lirica · B architettura · C–H famiglia, pace, diritti, scuola'],
        'HF' => ['area' => 'classici', 'argomento' => 'Teatro e poesia', 'dettaglio' => 'A teatro straniero · B–C teatro italiano · D archeologia · E–H poesia · I–L enciclopedie'],
        'LA' => ['area' => 'romanzi', 'argomento' => 'Romanzi e fantasy', 'dettaglio' => 'A, H enciclopedie d\'arte · B–G romanzi, Shannara · I ragazzi'],
        'LB' => ['area' => 'ragazzi', 'argomento' => 'Libri per ragazzi', 'dettaglio' => 'A Gesù e preghiera · B divulgazione · C–G narrativa · H–I storia'],
        'LC' => ['area' => 'ragazzi', 'argomento' => 'Classici d\'avventura per ragazzi', 'dettaglio' => 'Huck Finn, Gian Burrasca, Piccole donne · G storia'],
        'LD' => ['area' => 'ragazzi', 'argomento' => 'Classici per ragazzi', 'dettaglio' => 'Oliver Twist, Zanna Bianca, Moby Dick, Verne'],
        'PD' => ['area' => 'ragazzi', 'argomento' => 'Libri per bambini', 'dettaglio' => 'A–C prime letture · D pittura · E Patrimonio dell\'umanità'],
        'PC' => ['area' => 'ragazzi', 'argomento' => 'Libri per bambini', 'dettaglio' => 'A–B prime letture · C II guerra mondiale · D–E pittori'],
        'PB' => ['area' => 'ragazzi', 'argomento' => 'Libri per bambini', 'dettaglio' => 'A–B animali · C storia militare · D teatro · E Shakespeare'],
        'PA' => ['area' => 'ragazzi', 'argomento' => 'Libri per bambini', 'dettaglio' => 'A–B bambini · C storia · D romanzi storici · E musica'],
        'OD' => ['area' => 'romanzi', 'argomento' => 'Romanzi recenti', 'dettaglio' => 'A primi libri illustrati · B–C romanzi · D saggi storici · E arredamento'],
        'OC' => ['area' => 'romanzi', 'argomento' => 'Romanzi recenti', 'dettaglio' => 'A primi libri · B–D romanzi (Camilleri) · E disegno'],
        'OB' => ['area' => 'romanzi', 'argomento' => 'Romanzi recenti', 'dettaglio' => 'A hobby creativi · B–D romanzi · E fotografia'],
        'OA' => ['area' => 'romanzi', 'argomento' => 'Romanzi recenti', 'dettaglio' => 'A Natale · B–D romanzi · E pittura (Macchiaioli)'],
        'MA' => ['area' => 'romanzi', 'argomento' => 'Romanzi moderni', 'dettaglio' => 'Eco, Pavese, Böll · C–D ragazzi'],
        'MB' => ['area' => 'romanzi', 'argomento' => 'Romanzi e ragazzi', 'dettaglio' => 'A Faulkner, Joyce · B ragazzi (Wonder) · C gialli · D Cuore'],
        'MC' => ['area' => 'gialli', 'argomento' => 'Gialli', 'dettaglio' => 'A romanzi · B–D gialli'],
        'MD' => ['area' => 'gialli', 'argomento' => 'Gialli e classici', 'dettaglio' => 'A classici russi · B romanzi · C–D gialli'],
        'NA' => ['area' => 'classici', 'argomento' => 'Classici italiani', 'dettaglio' => 'A–B romanzi classici · C–D letteratura italiana antica'],
        'NB' => ['area' => 'classici', 'argomento' => 'Classici italiani', 'dettaglio' => 'A–B romanzi · C–D Pulci, Pellico, novelle del \'500'],
        'NC' => ['area' => 'classici', 'argomento' => 'Classici italiani', 'dettaglio' => 'A–B romanzi · C–D Sannazaro, Vasari, Boccaccio'],
        'ND' => ['area' => 'classici', 'argomento' => 'Teatro e classici', 'dettaglio' => 'B libretti d\'opera · C poemi · D Castiglione, Dante'],
        'SPO' => ['area' => 'scuola', 'argomento' => 'Elaborati misti: lettere e diari, fascicoli personali ed esami', 'dettaglio' => 'lettere e diari 25% · fascicoli personali ed esami 21% · scienze e natura 16%'],
        'SPQ' => ['area' => 'scuola', 'argomento' => 'Elaborati misti: vangelo e fede, storia e società', 'dettaglio' => 'vangelo e fede 29% · storia e società 21% · arte, poesia e letture 14%'],
        'RA' => ['area' => 'enciclopedie', 'argomento' => 'Dizionari e diritto', 'dettaglio' => 'Dizionario dei papi, diritto romano, capolavori'],
        'SPD' => ['area' => 'scuola', 'argomento' => 'Elaborati misti: vangelo e fede, storia e società', 'dettaglio' => 'vangelo e fede 44% · storia e società 13% · viaggi e gite di studio 12%'],
        'EF' => ['area' => 'enciclopedie', 'argomento' => 'Dizionari e Enciclopedia dei ragazzi', 'dettaglio' => 'A–B vocabolari · C Enciclopedia dei ragazzi · D dizionari'],
        'EE' => ['area' => 'enciclopedie', 'argomento' => 'Vocabolari e Enciclopedia dei Papi', 'dettaglio' => 'A dizionari tecnici · B vocabolario Treccani · D Enciclopedia dei Papi'],
        'ED' => ['area' => 'enciclopedie', 'argomento' => 'Dizionari di lingue', 'dettaglio' => 'A grammatiche e dizionari stranieri · B–D Novecento, Piccola Treccani'],
        'EC' => ['area' => 'enciclopedie', 'argomento' => 'Enciclopedia · Don Zeno', 'dettaglio' => 'A Nomadelfia e Don Zeno · B–D Treccani, Piccola Treccani'],
        'EB' => ['area' => 'enciclopedie', 'argomento' => 'Enciclopedia Italiana', 'dettaglio' => 'A iconografie · B–D Treccani'],
        'EA' => ['area' => 'enciclopedie', 'argomento' => 'Enciclopedia Italiana', 'dettaglio' => 'Treccani: volumi e appendici'],
        'EG' => ['area' => 'storia', 'argomento' => 'Classici antichi e storia', 'dettaglio' => 'A classici greci e latini · B–E storia del \'900 e politica'],
        'EH' => ['area' => 'storia', 'argomento' => 'Classici latini e storia', 'dettaglio' => 'A Sallustio, Terenzio · B romanzi · C, E storia · D sport'],
        'EI' => ['area' => 'storia', 'argomento' => 'Storia e antologie', 'dettaglio' => 'A grammatica e mitologia · B biografie storiche · C–D antologie · E politica italiana'],
        'EL' => ['area' => 'classici', 'argomento' => 'Opere complete di autori', 'dettaglio' => 'A Storia d\'Italia · B–D opere complete · E monografie (Dante, Shakespeare)'],
    ];

    /**
     * Argomenti degli elaborati della Scuola Parentale con le colonne SP in cui collocarli, in ordine di preferenza.
     *
     * @var array<string, array{nome: string, descrizione: string, colonne: list<string>}>
     */
    public const ARGOMENTI_ELABORATI = [
        'apostolato' => ['nome' => 'Apostolato e serate', 'descrizione' => 'Serate, propaganda e apostolato nelle regioni', 'colonne' => ['SPA']],
        'viaggi' => ['nome' => 'Viaggi e gite di studio', 'descrizione' => 'Gite, viaggi di studio, visite a città e musei', 'colonne' => ['SPB', 'SPC', 'SPN']],
        'fede' => ['nome' => 'Vangelo e fede', 'descrizione' => 'Vangelo, esercizi spirituali, santi, scritti di Don Zeno', 'colonne' => ['SPD']],
        'aziende' => ['nome' => 'Aziende, laboratori e mestieri', 'descrizione' => 'Aziende di Nomadelfia, laboratori, corsi, cinema e fotografia', 'colonne' => ['SPE']],
        'personali' => ['nome' => 'Fascicoli personali ed esami', 'descrizione' => 'Fascicoli personali, tesine e argomenti per esami', 'colonne' => ['SPF']],
        'arte' => ['nome' => 'Arte, poesia e letture', 'descrizione' => 'SPG letture e poesie, SPH disegni ed educazione artistica', 'colonne' => ['SPG', 'SPH']],
        'scienze' => ['nome' => 'Scienze e natura', 'descrizione' => 'Natura, animali, piante, olio, vino, corpo umano', 'colonne' => ['SPI']],
        'vita' => ['nome' => 'Vita di Nomadelfia', 'descrizione' => 'Feste, attività scolastiche, prescuola, fotografie', 'colonne' => ['SPL']],
        'storia' => ['nome' => 'Storia e società', 'descrizione' => 'Storia, esigenze umane, diritti, educazione civica', 'colonne' => ['SPM']],
        'geografia' => ['nome' => 'Geografia e paesi', 'descrizione' => 'Paesi e continenti, insieme alla storia', 'colonne' => ['SPM']],
        'lettere' => ['nome' => 'Lettere e diari', 'descrizione' => 'Lettere a persone e famiglie, diari', 'colonne' => ['SPO']],
    ];

    /**
     * Numero di elaborati per argomento presenti in ogni colonna SP.
     *
     * @var array<string, array<string, int>>
     */
    public const ELABORATI_PER_COLONNA = [
        'SPA' => ['apostolato' => 34, 'viaggi' => 2],
        'SPB' => ['viaggi' => 47, 'apostolato' => 5, 'fede' => 1, 'vita' => 1],
        'SPC' => ['viaggi' => 34, 'scienze' => 4, 'fede' => 2, 'apostolato' => 2, 'storia' => 1],
        'SPD' => ['fede' => 23, 'storia' => 7, 'viaggi' => 6, 'vita' => 5, 'scienze' => 4, 'aziende' => 2, 'lettere' => 2, 'personali' => 2, 'arte' => 1],
        'SPE' => ['personali' => 20, 'aziende' => 13, 'viaggi' => 8, 'vita' => 5, 'fede' => 4, 'scienze' => 3, 'arte' => 2],
        'SPF' => ['personali' => 44, 'viaggi' => 1],
        'SPG' => ['arte' => 41, 'fede' => 32, 'personali' => 8, 'viaggi' => 7, 'scienze' => 6, 'vita' => 5, 'storia' => 5, 'lettere' => 2, 'aziende' => 2, 'apostolato' => 1],
        'SPH' => ['arte' => 32, 'fede' => 16, 'vita' => 14, 'scienze' => 13, 'viaggi' => 7, 'lettere' => 7, 'storia' => 6, 'aziende' => 2, 'personali' => 1],
        'SPI' => ['scienze' => 76, 'fede' => 7, 'arte' => 4, 'viaggi' => 2, 'storia' => 1, 'aziende' => 1],
        'SPL' => ['vita' => 38, 'fede' => 7, 'aziende' => 7, 'lettere' => 7, 'arte' => 6, 'scienze' => 4, 'viaggi' => 3, 'storia' => 3],
        'SPM' => ['storia' => 42, 'fede' => 12, 'geografia' => 6, 'viaggi' => 2, 'lettere' => 1, 'vita' => 1, 'scienze' => 1],
        'SPN' => ['viaggi' => 16, 'scienze' => 5, 'fede' => 3, 'vita' => 2, 'lettere' => 2, 'geografia' => 2, 'storia' => 2, 'apostolato' => 1, 'aziende' => 1],
        'SPO' => ['lettere' => 37, 'personali' => 31, 'scienze' => 24, 'fede' => 18, 'viaggi' => 13, 'arte' => 9, 'vita' => 8, 'storia' => 7, 'aziende' => 2, 'apostolato' => 1],
        'SPP' => ['viaggi' => 18, 'scienze' => 14, 'fede' => 13, 'vita' => 9, 'storia' => 5, 'personali' => 4, 'apostolato' => 4, 'arte' => 2, 'aziende' => 1, 'lettere' => 1, 'geografia' => 1],
        'SPQ' => ['fede' => 4, 'storia' => 3, 'arte' => 2, 'vita' => 2, 'scienze' => 2, 'personali' => 1],
        'SPR' => ['vita' => 2, 'fede' => 2, 'scienze' => 1, 'viaggi' => 1, 'geografia' => 1, 'apostolato' => 1],
        'SPS' => ['arte' => 5, 'viaggi' => 2, 'vita' => 1, 'scienze' => 1, 'apostolato' => 1, 'storia' => 1],
    ];

    /**
     * Argomento delle sigle delle cassette video: le sigle che iniziano per Z sono DVD e CD, le altre videocassette VHS.
     *
     * @var array<string, string>
     */
    public const SIGLE_VIDEO = [
        'ZS' => 'Storia',
        'ZV' => 'Varie',
        'ZL' => 'Inglese',
        'ZC' => 'Musica e lirica',
        'ZSC' => 'Scienze',
        'ZF' => 'Film',
        'ZLE' => 'Letteratura',
        'ZT' => 'Teatro',
        'ZG' => 'Geografia',
        'ZA' => 'Arte',
        'ZR' => 'Spiritualità',
        'ZE' => 'Enciclopedie multimediali',
        'AM' => 'Ambiente e parchi',
        'IT' => 'Film in italiano',
        'CU' => 'Corpo umano e salute',
        'AR' => 'Archeologia',
        'VA' => 'Varie e attualità',
        'FI' => 'Film in inglese',
        'ST' => 'Storia',
        'LI' => 'Corsi di inglese',
        'GE' => 'Geografia estera',
        'AN' => 'Animali e natura',
        'TE' => 'Teatro',
        'SN' => 'Scienze naturali',
        'GI' => 'Geografia d\'Italia',
        'AS' => 'Astronomia',
        'PE' => 'Pedagogia',
        'LT' => 'Corsi di tedesco',
        'DF' => 'Documentari in francese',
        'BS' => 'Balletto e spettacolo',
        'NO' => 'Nomadelfia e Don Zeno',
        'LF' => 'Corsi di francese',
        'AV' => 'Arte',
        'DI' => 'Documentari in inglese',
        'BI' => 'Biologia',
        'FF' => 'Film in francese',
        'LR' => 'Corsi di russo',
        'SV' => 'Sport',
        'MO' => 'Moda',
        'CH' => 'Chimica',
        'DS' => 'Spagnolo',
    ];
}
