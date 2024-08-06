<?php
session_start();

// Connexion à la base de données
try {
    $db = new PDO("mysql:host=localhost;dbname=examen", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
    die();
}


// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Si l'utilisateur n'est pas connecté, redirigez-le vers la page de connexion
    header("Location: connexion.php");
    exit();
}

// Si l'utilisateur est connecté, continuez avec le reste du code
echo "VOUS ETES CONNECTE " ;

// Supprimer un candidat
if (isset($_GET['delete'])) {
    $candidatId = $_GET['delete'];
    try {
        $stmt_delete = $db->prepare("DELETE FROM candidat WHERE id_cand = :id_cand");
        $stmt_delete->bindParam(':id_cand', $candidatId);
        $stmt_delete->execute();
        $_SESSION['message'] = "Utilisateur supprimé avec succès !";
        header("Location: candidat.php");
        exit();
    } catch (PDOException $e) {
        echo "Erreur lors de la suppression : " . $e->getMessage();
    }
}
// Ajouter un bouton de déconnexion
if (isset($_POST['logout'])) {
    session_unset(); // Supprime toutes les variables de session
    session_destroy(); // Détruit la session
    header("Location: connexion.php"); // Redirige vers la page de connexion
    exit();
}
// Ajouter un nouveau candidat
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['enregistrer'])) {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $datnais = $_POST['datnais'];
    $ville = $_POST['ville'];
    $sexe = $_POST['sexe'];
    $codefil = $_POST['codefil'];

    try {
        $stmt_insert = $db->prepare("INSERT INTO candidat (nom, prenom, datnais, ville, sexe, codefil) VALUES (:nom, :prenom, :datnais, :ville, :sexe, :codefil)");
        $stmt_insert->bindParam(':nom', $nom);
        $stmt_insert->bindParam(':prenom', $prenom);
        $stmt_insert->bindParam(':datnais', $datnais);
        $stmt_insert->bindParam(':ville', $ville);
        $stmt_insert->bindParam(':sexe', $sexe);
        $stmt_insert->bindParam(':codefil', $codefil);
        $stmt_insert->execute();
        $_SESSION['message'] = "Enregistrement réussi !";
        header("Location: candidat.php");
        exit();
    } catch (PDOException $e) {
        echo "Erreur lors de l'enregistrement : " . $e->getMessage();
    }
}

// Récupération de la liste des candidats
try {
    $stmt_select = $db->prepare("SELECT id_cand, nom, prenom, datnais, ville, sexe, codefil FROM candidat");
    $stmt_select->execute();
    $candidats = $stmt_select->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur de récupération des candidats : " . $e->getMessage();
    die();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des candidats</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .btn-group {
            display: flex;
            justify-content: flex-start;
        }
        .btn {
            background-color: blue;
            color: white;
            border: none;
            padding: 6px 12px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            cursor: pointer;
            margin-right: 5px;
        }
        .btn1 {
            background-color: red;
            color: white;
            border: none;
            padding: 6px 12px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            cursor: pointer;
            margin-right: 5px;
        }
        .btn2 {
            background-color: red;
            color: white;
            border: none;
            padding: 6px 12px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            cursor: pointer;
            margin-right: 5px;
        }
        .btn3 {
            background-color: green;
            color: white;
            border: none;
            padding: 6px 12px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            cursor: pointer;
            margin-right: 5px;
        }
    </style>
</head>
<body>

<h1>BIENVENUE</h1>

<!-- Formulaire d'enregistrement d'un nouveau candidat -->
<form action="candidat.php" method="post">
    <fieldset>
        <legend><b> VEUILLEZ REMPLIR TOUS LES CHAMPS</b></legend>
        <table>
            <tr>
                <td>Nom:</td>
                <td><input type="text" name="nom" size="25" maxlength="25" required></td>
            </tr>
            <tr>
                <td>Prénoms:</td>
                <td><input type="text" name="prenom" size="50" maxlength="20" required></td>
            </tr>
            <tr>
                <td>Date de naissance:</td>
                <td><input type="date" name="datnais" size="20" maxlength="20" required></td>
            </tr>
            <tr>
                <td>Ville:</td>
                <td><input type="text" name="ville" size="25" maxlength="25" required></td>
            </tr>
            <tr>
                <td>Sexe:</td>
                <td>
                    <select name="sexe" required>
                        <option value="M">Masculin</option>
                        <option value="F">Féminin</option>
                    </select>
                </td>
            </tr>
            <tr><td>
    <label for="code _filiere"> Choisissez une filiere : </label>
    <select id="code_filiere" name="codefil">               
    <?php
    try {
        $db = new PDO("mysql:host=localhost;dbname=examen", "root", "");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        
        $stmt = $db->prepare("SELECT codefil FROM filiere");
        $stmt->execute();

        $rowCount = $stmt->rowCount();

        if ($rowCount > 0) {
            
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo"<option value= '". $row["codefil"]. "'>". $row["codefil"]. "</option>";
            }
        }else{
            echo"<option>Aucune filiere trouvee</option>";

        }
    } catch (PDOException $e) {
        echo "Echec de la connexion : " . $e->getMessage();
        die();
    }
    ?>
   
    </select><br><br>
    </tr></td>

            <tr>
                <td><input type="reset" name="annuler" value="Annuler"></td>
                <td><input type="submit" name="enregistrer" value="Enregistrer" class="btn3"></td>
            </tr>
        </table>
    </fieldset>
</form>
<form method="post">
<button type="submit" name="logout" class="btn2">Déconnexion</button>
</form>    
<hr>

<h1>LISTES DES CANDIDATS</h1>

<!-- Tableau affichant la liste des candidats -->
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>NOM</th>
            <th>PRÉNOMS</th>
            <th>DATNAIS</th>
            <th>VILLE</th>
            <th>SEXE</th>
            <th>CODEFIL</th>
            <th>ACTION</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($candidats as $candidat): ?>
        <tr>
            <td><?php echo htmlspecialchars($candidat['id_cand']); ?></td>
            <td><?php echo htmlspecialchars($candidat['nom']); ?></td>
            <td><?php echo htmlspecialchars($candidat['prenom']); ?></td>
            <td><?php echo htmlspecialchars($candidat['datnais']); ?></td>
            <td><?php echo htmlspecialchars($candidat['ville']); ?></td>
            <td><?php echo htmlspecialchars($candidat['sexe']); ?></td>
            <td><?php echo htmlspecialchars($candidat['codefil']); ?></td>
            <td>
                <form style="display:inline;" action="candidat.php" method="get">
                    <input type="hidden" name="delete" value="<?php echo $candidat['id_cand']; ?>">
                    <input type="submit" value="Supprimer" class="btn1">
                </form>
                <form style="display:inline;" action="modifier.php" method="get">
                    <input type="hidden" name="id" value="<?php echo $candidat['id_cand']; ?>">
                    <input type="submit" value="Modifier" class="btn">                                                 
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php
// Affichage du message de succès ou d'erreur
if (isset($_SESSION['message'])) {
    echo "<p>{$_SESSION['message']}</p>";
    unset($_SESSION['message']);
}
?>

</body>
</html>
