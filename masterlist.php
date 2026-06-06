<?php

// Session absichern
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use nexpell\LanguageService;
global $languageService;

/**
 * Game Server Masterlist Plugin für nexpell
 */

$config = mysqli_fetch_array(safe_query("SELECT selected_style FROM settings_headstyle_config WHERE id=1"));
$class = htmlspecialchars($config['selected_style']);

$data_array = [
    'class'    => $class,
    'title'    => 'Masterlist',
    'subtitle' => 'Game Server Übersicht'
];

// Head-Templates ausgeben
echo $tpl->loadTemplate("masterlist", "head", $data_array, "plugin");


/* ===============================
   VERSION MAPPING
================================ */
function masterlistVersionMap(): array
{
    return [
        'cod1_1.1' => 'cod/1.1',
        'cod1_1.2' => 'cod/1.2',
        'cod1_1.3' => 'cod/1.3',
        'cod1_1.4' => 'cod/1.4',
        'cod1_1.5' => 'cod/1.5',

        'coduo_1.41' => 'coduo/1.41',
        'coduo_1.51' => 'coduo/1.51',

        'cod2_1.0' => 'cod2/1.0',
        'cod2_1.2' => 'cod2/1.2',
        'cod2_1.3' => 'cod2/1.3',

        'cod4_1.0' => 'cod4/1.0',
        'cod4_1.7' => 'cod4/1.7',
        'cod4_1.8' => 'cod4/1.8',
    ];
}

function masterlistTokenToVersion(?string $token): string
{
    $map = masterlistVersionMap();
    $token = trim((string)$token);

    if ($token !== '' && isset($map[$token])) {
        return $map[$token];
    }

    return 'cod/1.1';
}

function masterlistVersionToToken(string $version): string
{
    $map = masterlistVersionMap();
    $token = array_search($version, $map, true);

    return is_string($token) ? $token : 'coduo_1.51';
}


/* ===============================
   VERSION AUSWAHL
================================ */
$selected_version = masterlistTokenToVersion($_GET['version'] ?? null);
$selected_version_token = masterlistVersionToToken($selected_version);


/* ===============================
   API
================================ */
require_once 'fetch_servers.php';
$servers = fetchCodUoServers($selected_version);


/* ===============================
   VERSION LABELS
================================ */
$versions = [
    "Call of Duty" => ["cod1/1.1", "cod1/1.2", "cod1/1.3", "cod1/1.4", "cod1/1.5"],
    "Call of Duty: United Offensive" => ["coduo/1.41", "coduo/1.51"],
    "Call of Duty 2" => ["cod2/1.0", "cod2/1.2", "cod2/1.3"],
    "Call of Duty 4: Modern Warfare" => ["cod4/1.0", "cod4/1.7", "cod4/1.8"]
];

function normalizeVersion(string $v): string
{
    if (str_starts_with($v, 'cod1/')) {
        return str_replace('cod1/', 'cod/', $v);
    }
    return $v;
}

function getVersionLabel(string $version, array $versions): string
{
    foreach ($versions as $game => $versList) {
        foreach ($versList as $v) {

            if (normalizeVersion($v) === $version) {
                $short = explode('/', $v)[1];
                return $game . ' ' . $short;
            }
        }
    }

    return $version;
}

function getVersionDisplay(string $version, array $versions): array
{
    foreach ($versions as $game => $versList) {
        foreach ($versList as $v) {

            if (normalizeVersion($v) === $version) {

                $short = explode('/', $v)[1];

                // Game Key bestimmen
                $gameKey = explode('/', normalizeVersion($v))[0];

                // Cover Mapping
                $covers = [
                    'cod'   => 'cod1.jpg',
                    'coduo' => 'coduo.jpg',
                    'cod2'  => 'cod2.jpg',
                    'cod4'  => 'cod4.jpg',
                ];

                $cover = $covers[$gameKey] ?? 'default.jpg';

                return [
                    'title' => $game,
                    'version' => $short,
                    'cover' => "includes/plugins/masterlist/images/covers/" . $cover
                ];
            }
        }
    }

    return [
        'title' => $version,
        'version' => '',
        'cover' => "includes/plugins/masterlist/images/covers/default.jpg"
    ];
}

$version_display = getVersionDisplay($selected_version, $versions);

$selected_version_label = getVersionLabel($selected_version, $versions);


/* ===============================
   SELECT
================================ */
$select_setting = '

<form method="GET">
<input type="hidden" name="site" value="masterlist">

<label for="version">Version:</label>

<select class="form-select border border-dark" onchange="changeVersion(this.value)">

    <optgroup label="COD 1">
        <option value="cod1_1.1" ' . ($selected_version_token == 'cod1_1.1' ? 'selected' : '') . '>1.1</option>
        <option value="cod1_1.2" ' . ($selected_version_token == 'cod1_1.2' ? 'selected' : '') . '>1.2</option>
        <option value="cod1_1.3" ' . ($selected_version_token == 'cod1_1.3' ? 'selected' : '') . '>1.3</option>
        <option value="cod1_1.4" ' . ($selected_version_token == 'cod1_1.4' ? 'selected' : '') . '>1.4</option>
        <option value="cod1_1.5" ' . ($selected_version_token == 'cod1_1.5' ? 'selected' : '') . '>1.5</option>
    </optgroup>

    <optgroup label="COD UO">
        <option value="coduo_1.41" ' . ($selected_version_token == 'coduo_1.41' ? 'selected' : '') . '>1.41</option>
        <option value="coduo_1.51" ' . ($selected_version_token == 'coduo_1.51' ? 'selected' : '') . '>1.51</option>
    </optgroup>

    <optgroup label="COD 2">
        <option value="cod2_1.0" ' . ($selected_version_token == 'cod2_1.0' ? 'selected' : '') . '>1.0</option>
        <option value="cod2_1.2" ' . ($selected_version_token == 'cod2_1.2' ? 'selected' : '') . '>1.2</option>
        <option value="cod2_1.3" ' . ($selected_version_token == 'cod2_1.3' ? 'selected' : '') . '>1.3</option>
    </optgroup>

    <optgroup label="COD 4">
        <option value="cod4_1.0" ' . ($selected_version_token == 'cod4_1.0' ? 'selected' : '') . '>1.0</option>
        <option value="cod4_1.7" ' . ($selected_version_token == 'cod4_1.7' ? 'selected' : '') . '>1.7</option>
        <option value="cod4_1.8" ' . ($selected_version_token == 'cod4_1.8' ? 'selected' : '') . '>1.8</option>
    </optgroup>

</select>
</form>
';


/* ===============================
   SERVER LISTE
================================ */
// Daten für das Template vorbereiten
// Serverliste generieren
$server_html = "";
foreach ($servers as $index => $server) {
    $server_id = "server-" . $index; // Eindeutige ID für Bootstrap Collapse
    $status_badge = ($server['clients'] > 0) 
    ? '<span class="badge bg-success d-inline-block text-center" style="width: 150px;">Player online</span>' 
    : '<span class="badge bg-danger d-inline-block text-center" style="width: 150px;">No Player online</span>';
    $server_name = convertColorCodes($server['sv_hostname']); // Farbcodes umwandeln


    if (!empty($server['playerinfo']) && is_array($server['playerinfo'])) {
    $player_details = [];
    $score_details = [];  // Initialisierung notwendig!
    $ping_details = [];   // Initialisierung notwendig!
    
    foreach ($server['playerinfo'] as $player) {
        $name = htmlspecialchars($player['name'] ?? "Unbekannter Spieler");
        $score = htmlspecialchars($player['score'] ?? "N/A");  
        $ping = htmlspecialchars($player['ping'] ?? "N/A");  
        
        $player_details[] = $name;
        $score_details[] = $score;
        $ping_details[] = $ping;
    }
    
    $spieler = implode("<br>", $player_details); 
    $score = implode("<br>", $score_details); 
    $ping = implode("<br>", $ping_details); 
} else {
    $spieler = "Keine Spieler gefunden.";
    $score = "N/A";
    $ping = "N/A";
}

    $name = convertColorCodes($spieler); // Farbcodes umwandeln
$map_image_path = "includes/plugins/masterlist/images/coduo/custom/" . htmlspecialchars($server['mapname']) . ".png";

// Prüfen, ob die Datei existiert und ein gültiges Bild ist
if (file_exists($map_image_path) && @getimagesize($map_image_path)) {
    $map_image_url = $map_image_path;
} else {
    $map_image_url = "includes/plugins/masterlist/images/default_map.png"; // Fallback-Bild
}

    $server_html .= "
    <tr class='server-row' data-bs-toggle='collapse' data-bs-target='#{$server_id}'>
        <td> + </td>
        <td>{$server_name}</td>
        <td>{$server['ip']}</td>
        <td>{$server['port']}</td>
        <td>{$server['game']}</td>
        <td>{$server['mapname']}</td>
        <td>{$server['clients']}/{$server['sv_maxclients']}</td>
        <td>$status_badge</td>
    </tr>
    <tr id='{$server_id}' class='collapse server-details'>
        <td colspan='8'>
            <table class='table table-striped table-dark'>
                <tr>
                    <td width='50%'>     
                        <img src='" . $map_image_url . "' alt='" . htmlspecialchars($server['mapname']) . "' style='width: 450px; height: auto;'><br>
                        Weitere Informationen:<br>
                        Map: " . htmlspecialchars($server['mapname']) . "<br>
                        Gametype: " . htmlspecialchars($server['g_gametype']) . "</td> 
                    <td>
                        <table class='table table-dark'>
                            <thead>
                                <tr>
                                    <th>Spielernamen:</th>
                                    <th>Score</th>
                                    <th>Ping</th>
                                </tr>
                            </thead>
                            <tbody>
                                <td>" . $name . "</td>
                                <td>" . $score . "</td>
                                <td>" . $ping . "</td>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>";
}


/* ===============================
   TEMPLATE
================================ */
$data_array = [
    'servers' => $server_html,
    'selected_version' => $selected_version,
    'selected_version_label' => $selected_version_label,
    'select_setting' => $select_setting,
    'version_title' => $version_display['title'],
    'version_number' => $version_display['version'],
    'version_cover' => $version_display['cover'],
];

echo $tpl->loadTemplate("masterlist", "content", $data_array, "plugin");

?>


<script>
function changeVersion(version) {
    window.location.href = "index.php?site=masterlist&version=" + version;
}
</script>