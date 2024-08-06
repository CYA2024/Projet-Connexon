<?php
session_start();

// Connexion à la base de données
try {
    $db = new PDO("mysql:host=localhost;dbname=lep-fbf", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur de connexion : " . htmlspecialchars($e->getMessage());
    die();
}
if (isset($_GET['delete'])) {
    $joueurId = $_GET['delete'];
    try {
        $stmt_delete = $db->prepare("DELETE FROM inscription WHERE id_joueur = :id_joueur");
        $stmt_delete->bindParam(':id_joueur', $joueurId);
        $stmt_delete->execute();
        $_SESSION['message'] = "Utilisateur supprimé avec succès !";
        header("Location: joueur.php");
        exit();
    } catch (PDOException $e) {
        echo "Erreur lors de la suppression : " . $e->getMessage();
    }
}

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['id_conn'])) {
    // Si l'utilisateur n'est pas connecté, redirigez-le vers la page de connexion
    header("Location: connexion.php");
    exit();
}

// Ajouter un bouton de déconnexion
if (isset($_POST['logout'])) {
    session_unset(); // Supprime toutes les variables de session
    session_destroy(); // Détruit la session
    header("Location: connexion.php"); // Redirige vers la page de connexion
    exit();
}

// Ajout ou mise à jour d'un joueur
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $datnaissance = $_POST['datnaissance'];
    $ville = $_POST['ville'];
    $categorie = $_POST['categorie'];
    $club = $_POST['club'];
    $annee = $_POST['annee'];

    try {
        // Vérifiez si le joueur existe déjà
        $stmt = $db->prepare("SELECT id_joueur FROM joueur WHERE nom = :nom AND prenom = :prenom AND datnaissance = :datnaissance");
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':datnaissance', $datnaissance);
        $stmt->execute();
        $existingPlayer = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingPlayer) {
            // Joueur existe déjà, utilisez son ID
            $id_joueur = $existingPlayer['id_joueur'];
        } else {
            // Ajouter un nouveau joueur
            $stmt = $db->prepare("INSERT INTO joueur (nom, prenom, datnaissance, ville) VALUES (:nom, :prenom, :datnaissance, :ville)");
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':prenom', $prenom);
            $stmt->bindParam(':datnaissance', $datnaissance);
            $stmt->bindParam(':ville', $ville);
            $stmt->execute();

            // Récupération de l'ID du joueur inséré
            $id_joueur = $db->lastInsertId();
        }

        // Vérifiez si l'inscription existe déjà
        $stmt = $db->prepare("SELECT id_joueur FROM inscription WHERE id_joueur = :idjoueur AND annee = :annee");
        $stmt->bindParam(':idjoueur', $id_joueur);
        $stmt->bindParam(':annee', $annee);
        $stmt->execute();
        $existingInscription = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingInscription) {
            // Mise à jour de l'inscription existante
            $stmt = $db->prepare("UPDATE inscription SET categorie = :categorie, club = :club WHERE id_joueur = :id_joueur");
            $stmt->bindParam(':categorie', $categorie);
            $stmt->bindParam(':club', $club);
            $stmt->bindParam(':id_joueur', $existingInscription['id_joueur']);
            $stmt->execute();
        } else {
            // Ajouter une inscription
            $stmt = $db->prepare("INSERT INTO inscription (id_joueur, annee, categorie, club) VALUES (:idjoueur, :annee, :categorie, :club)");
            $stmt->bindParam(':idjoueur', $id_joueur);
            $stmt->bindParam(':annee', $annee);
            $stmt->bindParam(':categorie', $categorie);
            $stmt->bindParam(':club', $club);
            $stmt->execute();
        }

        // Redirection après l'ajout
        header("Location: joueur.php");
        exit();

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}


// Affichage des joueurs existants
try {
    $stmt = $db->prepare("SELECT j.id_joueur, j.nom, j.prenom, j.datnaissance, j.ville, i.categorie, i.club, i.annee 
                           FROM joueur j
                           JOIN inscription i ON j.id_joueur = i.id_joueur");
    $stmt->execute();
    $joueurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des joueurs</title>
    <style>
        .container {
            display: flex;
            justify-content: center;
            gap: 20px;
        }
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

<h1>Gestion des joueurs</h1>

<!-- Formulaire d'inscription annuelle -->
<form action="joueur.php" method="post">
    <fieldset>
        <legend><b> INSCRIPTION ANNUELLE POUR LE CHAMPIONAT </b></legend>
        <div class="container">
            <table>
                <tr>
                    <td><label for="categorie"> Catégorie : </label></td>
                    <td>
                        <select id="categorie" name="categorie" required>
                            <option value="junior">junior</option>
                            <option value="senior">senior</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Club:</td>
                    <td>
                        <select name="club" required>
                            <option>ASPAC FC</option>
                            <option>MOGAS FC</option>
                            <option>REQUINS FC</option>
                            <option>DRAGON FC</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="nom"> Nom : </label></td>
                    <td>
                        <select id="nom" name="nom" onchange="loadPlayerData(this.value)" required>
                            <option value="">Sélectionner un joueur</option>
                            <?php
                            try {
                                $stmt = $db->prepare("SELECT DISTINCT nom FROM joueur");
                                $stmt->execute();
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<option value='" . htmlspecialchars($row["nom"]) . "'>" . htmlspecialchars($row["nom"]) . "</option>";
                                }
                            } catch (PDOException $e) {
                                echo "Échec de la connexion : " . htmlspecialchars($e->getMessage());
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Prénom:</td>
                    <td><input type="text" name="prenom" id="prenom" size="25" maxlength="25" required></td>
                </tr>
                <tr>
                    <td>Date de naissance:</td>
                    <td><input type="text" name="datnaissance" id="datnaissance" size="15" maxlength="15" required></td>
                </tr>
                <tr>
                    <td>Ville:</td>
                    <td><input type="text" name="ville" id="ville" size="25" maxlength="25" required></td>
                </tr>
                <tr>
                    <td>Année en cours:</td>
                    <td><input type="text" name="annee" size="15" maxlength="15" required></td>
                </tr>
                <tr>
                    <td><input type="hidden" name="id_joueur" id="id_joueur"></td>
                    <td><input type="submit" value="Enregistrer" class="btn2"></td>
                    <td><input type="reset" name="effacer" value="Annuler" style="background-color: gray; color: white; border: white;"></td>
                </tr>
            </table>
        </div>
    </fieldset>
</form>

<hr>

<!-- Tableau affichant la liste des joueurs avec leurs inscriptions -->
<table>
    <thead>
        <tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Date de Naissance</th>
            <th>Ville</th>
            <th>Année</th>
            <th>Catégorie</th>
            <th>Club</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($joueurs as $joueur): ?>
        <tr>
            <td><?php echo htmlspecialchars($joueur['nom']); ?></td>
            <td><?php echo htmlspecialchars($joueur['prenom']); ?></td>
            <td><?php echo htmlspecialchars($joueur['datnaissance']); ?></td>
            <td><?php echo htmlspecialchars($joueur['ville']); ?></td>
            <td><?php echo htmlspecialchars($joueur['annee']); ?></td>
            <td><?php echo htmlspecialchars($joueur['categorie']); ?></td>
            <td><?php echo htmlspecialchars($joueur['club']); ?></td>
            <td>
                <form action="joueur.php" method="get" style="display:inline;">
                    <input type="hidden" name="delete" value="<?php echo htmlspecialchars($joueur['id_joueur']); ?>">
                    <input type="submit" value="Supprimer" class="btn1">
                </form>
                <form action="modifier.php" method="get" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($joueur['id_joueur']); ?>">
                    <input type="submit" value="Modifier" class="btn">
                </form>
                <form method="post">
                 <button type="submit" name="logout">Déconnexion</button>
                 </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php
// Affichage du message de succès ou d'erreur
if (isset($_SESSION['message'])) {
    echo "<p>" . htmlspecialchars($_SESSION['message']) . "</p>";
    unset($_SESSION['message']);
}
?>

<script>
function loadPlayerData(nom) {
    if (nom === '') {
        document.getElementById('id_joueur').value = '';
        document.getElementById('prenom').value = '';
        document.getElementById('datnaissance').value = '';
        document.getElementById('ville').value = '';
        return;
    }

    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'get_player_data.php?nom=' + encodeURIComponent(nom), true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            var player = JSON.parse(xhr.responseText);
            if (player.error) {
                console.error(player.error);
            } else {
                document.getElementById('id_joueur').value = player.id_joueur || '';
                document.getElementById('prenom').value = player.prenom || '';
                document.getElementById('datnaissance').value = player.datnaissance || '';
                document.getElementById('ville').value = player.ville || '';
            }
        } else {
            console.error('Erreur lors de la récupération des données du joueur');
        }
    };
    xhr.send();
}
</script>

</body>
</html>
