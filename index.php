<?php
$path = '/';
if(isset($_GET['path'])){
    $path = '/'.$_GET['path'].'/';
}

// connection settings
$ftp_server = "localhost";  //address of ftp server.
$ftp_user_name = "root"; // Username
$ftp_user_pass = "root";   // Password

$conn_id = ftp_connect($ftp_server);        // set up basic connection
$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass) or die("<h2 class='error'>Permission refus&eacute; pour se connecter</h2>");   // login with username and password, or give invalid user message
ftp_pasv($conn_id, true) or die("Cannot switch to passive mode");
if ((!$conn_id) || (!$login_result)) {  // check connection
    // wont ever hit this, b/c of the die call on ftp_login
    echo "La connexion FTP a &eacute;chou&eacute;! <br />";
    exit;
}

if(isset($_POST['SubmitFile'])){
    $myFile = $_FILES['txt_file']; // This will make an array out of the file information that was stored.
    $file = $myFile['tmp_name'];  //Converts the array into a new string containing the path name on the server where your file is.

    $myFileName = basename($_POST['txt_fileName']); //Retrieve filename out of file path

    $destination_file = $path.$myFileName;  //where you want to throw the file on the webserver (relative to your login dir)

    $upload = ftp_put($conn_id, $destination_file, $file, FTP_BINARY);  // upload the file
    if (!$upload) {  // check upload status
        echo "<h2 class='error'>Envois $myFileName a &eacute;chou&eacute; !</h2> <br />";
    } else {
        echo "<h2 class='success'>Envois $myFileName a r&eacute;ussi !</h2><br /><br />";
    }
}

$tree = "";
$explode_path = explode("/",$path);
$explode_path[0] = "/";
// Remove empty
$explode_path = array_filter($explode_path, function($value) { return $value !== ''; });

for($i=0; $i < count($explode_path); $i++){
    $explode_path_temp = $explode_path;
    for($j=$i; $j < count($explode_path)-1; $j++){
        array_pop($explode_path_temp);
    }

    $folder_path = implode("/",$explode_path_temp);
    $tree .="<li>".$folder_path."<ul>";
}
$folders = "";
$files = "";
$listFolderFile = ftp_nlist($conn_id,$path);
asort($listFolderFile);
foreach($listFolderFile as $file){
    $explode_file = explode("/",$file);
    if(ftp_size($conn_id, $file) !== -1){
        $files .= "<li>".array_pop($explode_file)."</li>";
    }else{
        $folders .= "<li>".$file."<ul></ul></li>";
    }
}
$tree .= $folders.$files;
foreach($explode_path as $folder){
    $tree .="</ul></li>";
}

ftp_close($conn_id); // close the FTP stream
?>

<html>
<head>
    <link href="main.css" rel="stylesheet">
</head>
<body>
    <!--
    <div class="folder-tree">
        <div class="box">
            <h2>Explorateur de dossier</h2>
            <ul class="directory-list">
                <?php echo $tree; ?>
            </ul>
        </div>
    </div>
    -->
    <form enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST">
        <div class="frame">
            <div class="information">
                <h2>Dossier cible :</h2>
                <h3><?php echo $path ?></h3>
            </div>
            <div class="center">
                <div class="bar"></div>
                <div class="title">D&eacute;poser un fichier</div>
                <div class="dropzone">
                    <div class="content">
                        <img src="assets/upload.png" class="upload">
                        <span class="filename"></span>
                        <input class="input" name="txt_file" type="file" id="txt_file" tabindex="1" size="35" onChange="txt_fileName.value=txt_file.value" />
                    </div>
                </div>
                <img src="assets/syncing.png" class="syncing">
                <img src="assets/checkmark.png" class="done">
                <input name="txt_fileName" type="hidden" id="txt_fileName" tabindex="99" size="1" />
                <input class="upload-btn" type="submit" name="SubmitFile" value="Valider" accesskey="ENTER" tabindex="2" />
            </div>
            <div class="warning">
                <h3>Si le fichier existe d&eacute;j&agrave; il sera remplacer</h3>
            </div>
        </div>
    </form>
    <div class="shortcut">
        <h2>Raccourcis</h2>
        <ul id="nav">
            <li>
                <a href="?path=/exemple/">
                    Exemple
                </a>
            </li>
        </ul>
    </div>
</body>
<script src="vendor/jquery-3.3.1.min.js"></script>
<script src="main.js"></script>
<html>

